<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\AuthLog;
use App\Models\ActiveSession;
use App\Models\Router;
use Illuminate\Support\Facades\Log;

// Import class dari library RouterOS API yang baru saja di-install
use RouterOS\Client;
use RouterOS\Query;

class VerifyAuthController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'signature'      => 'required|string',
            'mac_address'    => 'required|string',
            'ip_address'     => 'required|string',
        ]);

        $walletAddress = $request->wallet_address;
        $macAddress = $request->mac_address;
        $ipAddress = $request->ip_address;
        $signature = $request->signature;

        try {
            $wallet = Wallet::where('wallet_address', $walletAddress)->first();

            if (!$wallet) {
                $this->catatLog($walletAddress, $signature, $ipAddress, $macAddress, 'Failed', 'Akses Ditolak: Wallet tidak terdaftar dalam sistem.');
                return response()->json(['status' => 'error', 'message' => 'Wallet tidak terdaftar!'], 401);
            }

            if (!$wallet->is_whitelisted) {
                $this->catatLog($walletAddress, $signature, $ipAddress, $macAddress, 'Failed', 'Akses Ditolak: Status Wallet sedang diblokir.');
                return response()->json(['status' => 'error', 'message' => 'Wallet sedang diblokir oleh Admin!'], 403);
            }

            // Catat Sesi Aktif di Database Dashboard
            ActiveSession::updateOrCreate(
                ['mac_address' => $macAddress],
                [
                    'wallet_id'  => $wallet->id,
                    'ip_address' => $ipAddress,
                    'start_time' => now(),
                    'end_time'   => null,
                    'status'     => 'Connected',
                ]
            );

            // Tembakkan Perintah ke Router MikroTik Fisik
            $mikrotikStatus = $this->bukaAksesMikrotik($macAddress, $ipAddress, $wallet->nama_pemilik);

            if ($mikrotikStatus) {
                $this->catatLog($walletAddress, $signature, $ipAddress, $macAddress, 'Success', 'Autentikasi berhasil, perangkat di-bypass.');
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Autentikasi berhasil. Internet dibuka.'
                ], 200);
            } else {
                // Jika MikroTik sedang mati/offline, kita tetap catat sebagai error teknis
                $this->catatLog($walletAddress, $signature, $ipAddress, $macAddress, 'Failed', 'Autentikasi valid, tetapi gagal terhubung ke Router MikroTik.');
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Sistem sedang maintenance (Router Offline).'
                ], 503);
            }

        } catch (\Exception $e) {
            Log::error('Error saat verifikasi Web3: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Fungsi Helper untuk mencatat log audit
     */
    private function catatLog($wallet, $sign, $ip, $mac, $status, $msg)
    {
        AuthLog::create([
            'wallet_address' => $wallet,
            'signature'      => $sign,
            'ip_address'     => $ip,
            'mac_address'    => $mac,
            'status'         => $status,
            'message'        => $msg,
        ]);
    }

    /**
     * Fungsi Core: Eksekusi API MikroTik (Otot Sistem)
     */
    private function bukaAksesMikrotik($mac, $ip, $namaPemilik)
    {
        // 1. Ambil data Router yang sedang Aktif dari tabel Routers
        $router = Router::where('is_active', true)->first();

        if (!$router) {
            Log::warning('Bypass gagal: Tidak ada router aktif di database.');
            return false;
        }

        try {
            // Karena kita menggunakan standar enterprise (casting 'encrypted' di model),
            // $router->api_password akan otomatis di-decrypt oleh Laravel di sini. Sangat aman!
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->api_username,
                'pass' => $router->api_password,
                'port' => (int) $router->api_port,
                'timeout' => 3, // Jangan buat sistem menunggu terlalu lama jika router mati
            ]);

            // 2. Cek apakah MAC Address ini sudah pernah di-bypass sebelumnya untuk mencegah error duplicate
            $checkQuery = (new Query('/ip/hotspot/ip-binding/print'))
                ->where('mac-address', $mac);
            $existing = $client->query($checkQuery)->read();

            if (empty($existing)) {
                // 3. Jika belum ada, tambahkan perangkat ke IP Binding (Bypassed)
                $addQuery = (new Query('/ip/hotspot/ip-binding/add'))
                    ->equal('mac-address', $mac)
                    // ->equal('address', $ip)
                    ->equal('type', 'bypassed')
                    ->equal('comment', "Web3: {$namaPemilik} (" . now()->format('d/m/Y H:i') . ")");
                
                $client->query($addQuery)->read();
                Log::info("MikroTik: Berhasil mem-bypass MAC {$mac} atas nama {$namaPemilik}.");
            } else {
                Log::info("MikroTik: MAC {$mac} sudah dalam status bypassed.");
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Koneksi RouterOS API Gagal: ' . $e->getMessage());
            return false; // Mengembalikan false agar frontend tahu ada masalah perangkat keras
        }
    }
}
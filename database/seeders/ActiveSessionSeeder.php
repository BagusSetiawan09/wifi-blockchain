<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActiveSession;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActiveSessionSeeder extends Seeder
{
    /**
     * Menjalankan proses injeksi data awal (seeding) untuk tabel active_sessions.
     */
    public function run(): void
    {
        // Ambil maksimal 3 data wallet dari database untuk dijadikan relasi
        $wallets = Wallet::take(3)->get();

        // Pengecekan aman: Jika tabel wallet kosong, hentikan seeder
        if ($wallets->isEmpty()) {
            $this->command->warn('Tabel wallets kosong! Silakan jalankan WalletSeeder terlebih dahulu.');
            return;
        }

        // Membuat data simulasi jaringan
        $sessions = [
            [
                'wallet_id'   => $wallets[0]->id,
                'mac_address' => '00:1A:2B:3C:4D:5E',
                'ip_address'  => '192.168.88.10',
                'start_time'  => Carbon::now()->subHours(2), // Simulasi: Terhubung sejak 2 jam lalu
                'end_time'    => null,
                'status'      => 'Connected',
            ],
            [
                'wallet_id'   => $wallets[1]->id ?? 1,
                'mac_address' => 'AA:BB:CC:DD:EE:FF',
                'ip_address'  => '192.168.88.15',
                'start_time'  => Carbon::now()->subDays(1), // Simulasi: Sesi hari kemarin
                'end_time'    => Carbon::now()->subDays(1)->addHours(3), // Simulasi: Putus setelah 3 jam
                'status'      => 'Disconnected',
            ],
            [
                'wallet_id'   => $wallets[2]->id ?? 1,
                'mac_address' => '11:22:33:44:55:66',
                'ip_address'  => '192.168.88.20',
                'start_time'  => Carbon::now()->subMinutes(15), // Simulasi: Baru terhubung 15 menit lalu
                'end_time'    => null,
                'status'      => 'Connected',
            ],
        ];

        try {
            foreach ($sessions as $session) {
                ActiveSession::create($session);
            }
            
            $this->command->info('Data seeder Active Session berhasil diinjeksi ke database!');
            
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan ActiveSessionSeeder: ' . $e->getMessage());
            $this->command->error('Terjadi kesalahan saat seeding Active Session. Cek log untuk detailnya.');
        }
    }
}
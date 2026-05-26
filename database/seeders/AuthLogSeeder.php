<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthLog;
use Carbon\Carbon;

class AuthLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'signature'      => '0x9b3b0a... (simulasi hash panjang) ...12c3f',
                'ip_address'     => '192.168.88.10',
                'mac_address'    => '00:1A:2B:3C:4D:5E',
                'status'         => 'Success',
                'message'        => 'Autentikasi berhasil, akses diberikan.',
                'created_at'     => Carbon::now()->subHours(2),
            ],
            [
                'wallet_address' => '0xUnknownHackerWallet9999999999999999999',
                'signature'      => '0xabc123... (simulasi hash) ...def456',
                'ip_address'     => '192.168.88.11',
                'mac_address'    => 'FF:EE:DD:CC:BB:AA',
                'status'         => 'Failed',
                'message'        => 'Akses Ditolak: Wallet tidak terdaftar di Whitelist.',
                'created_at'     => Carbon::now()->subMinutes(45),
            ],
            [
                'wallet_address' => '0x14dC79964da2C08b23698B3D3cc7Ca32193d9955',
                'signature'      => '0x111222... (simulasi hash) ...333444',
                'ip_address'     => '192.168.88.12',
                'mac_address'    => '11:22:33:44:55:66',
                'status'         => 'Failed',
                'message'        => 'Akses Ditolak: Status Wallet sedang diblokir oleh Admin.',
                'created_at'     => Carbon::now()->subMinutes(5),
            ],
        ];

        foreach ($logs as $log) {
            AuthLog::create($log);
        }
        
        $this->command->info('Data seeder Auth Log berhasil diinjeksi!');
    }
}
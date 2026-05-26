<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class WalletSeeder extends Seeder
{
    /**
     * Menjalankan proses injeksi data awal (seeding) untuk tabel wallets.
     */
    public function run(): void
    {
        // Daftar dompet simulasi dengan panjang 42 karakter (standar 0x...)
        $wallets = [
            [
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'nama_pemilik'   => 'Ratu Anggisyah',
                'role'           => 'Mahasiswa',
                'is_whitelisted' => true,
            ],
            [
                'wallet_address' => '0x32Be343B94f860124dC4fEe278FDCBD38C102D88',
                'nama_pemilik'   => 'Dr. Budi Santoso',
                'role'           => 'Dosen',
                'is_whitelisted' => true,
            ],
            [
                'wallet_address' => '0x14dC79964da2C08b23698B3D3cc7Ca32193d9955',
                'nama_pemilik'   => 'Ahmad Staf IT',
                'role'           => 'Staf',
                'is_whitelisted' => false, // Simulasi akun staf yang sedang diblokir/dinonaktifkan
            ],
            [
                'wallet_address' => '0x99C85bb64564D8eF0071611581826ba1a0628eA8',
                'nama_pemilik'   => 'Tamu Seminar Nasional',
                'role'           => 'Tamu',
                'is_whitelisted' => true,
            ],
        ];

        // Membungkus proses insert dalam blok Try-Catch untuk keamanan
        try {
            foreach ($wallets as $wallet) {
                // Menggunakan firstOrCreate agar jika seeder dijalankan 2x, 
                // tidak terjadi error duplicate entry pada wallet_address yang unique
                Wallet::firstOrCreate(
                    ['wallet_address' => $wallet['wallet_address']], 
                    $wallet
                );
            }
            
            $this->command->info('Data seeder Wallet berhasil diinjeksi ke database!');
            
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan WalletSeeder: ' . $e->getMessage());
            $this->command->error('Terjadi kesalahan saat seeding Wallet. Cek log untuk detailnya.');
        }
    }
}
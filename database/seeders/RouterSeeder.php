<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Router;
use Illuminate\Support\Facades\Log;

class RouterSeeder extends Seeder
{
    /**
     * Menjalankan proses injeksi data awal (seeding) untuk tabel routers.
     * Menggunakan metode iterasi array agar mudah menambahkan banyak data dummy.
     */
    public function run(): void
    {
        // Mendefinisikan array multi-dimensi berisi daftar router simulasi
        $routers = [
            [
                'nama_router'  => 'MikroTik Gedung Utama',
                'ip_address'   => '192.168.88.1',
                'api_port'     => 8728,
                'api_username' => 'admin',
                // Catatan: Nilai ini diinput sebagai teks biasa (plaintext), 
                // namun akan otomatis dienkripsi oleh Laravel sebelum masuk ke database
                // berkat deklarasi 'encrypted' pada casting di file Router Model.
                'api_password' => 'secret_password_123', 
                'is_active'    => true,
            ],
            [
                'nama_router'  => 'MikroTik Lab Jaringan',
                'ip_address'   => '10.0.0.1',
                'api_port'     => 8728,
                'api_username' => 'api_user',
                'api_password' => 'jaringan_aman_456',
                'is_active'    => false, // Status tidak aktif untuk menguji fitur toggle/filter
            ],
            [
                'nama_router'  => 'MikroTik Area Publik (Cafe)',
                'ip_address'   => '172.16.1.1',
                'api_port'     => 8728,
                'api_username' => 'hotspot_admin',
                'api_password' => 'kopi_pahit_789',
                'is_active'    => true,
            ],
        ];

        // Membungkus proses insert dalam blok Try-Catch 
        // sebagai standar keamanan untuk menangkap error jika terjadi kegagalan database
        try {
            foreach ($routers as $router) {
                // Menggunakan Eloquent ORM (Create) agar fitur mutator (enkripsi password) dari Model tetap berjalan
                Router::create($router);
            }
            
            // Mencetak log di console jika seeding berhasil
            $this->command->info('Data seeder Router berhasil diinjeksi ke database!');
            
        } catch (\Exception $e) {
            // Mencatat error ke file laravel.log jika gagal
            Log::error('Gagal menjalankan RouterSeeder: ' . $e->getMessage());
            $this->command->error('Terjadi kesalahan saat seeding Router. Cek log untuk detailnya.');
        }
    }
}
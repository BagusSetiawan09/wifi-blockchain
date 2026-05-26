<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel wallets.
     * Tabel ini berfungsi sebagai Whitelist (Daftar Putih) pengguna jaringan.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            
            // Wallet address kripto
            $table->string('wallet_address')->unique()->comment('Alamat dompet kripto Web3 (MetaMask, dll)');
            
            $table->string('nama_pemilik')->comment('Nama asli pemilik dompet untuk identifikasi admin');
            
            // Role ini berguna jika ingin membedakan limit bandwidth (bandwidth management) di MikroTik
            $table->enum('role', ['Mahasiswa', 'Dosen', 'Staf', 'Tamu'])->default('Mahasiswa')->comment('Grup hak akses pengguna');
            
            $table->boolean('is_whitelisted')->default(true)->comment('Jika false, dompet ini diblokir dari WiFi');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
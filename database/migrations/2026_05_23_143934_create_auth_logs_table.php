<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_logs', function (Blueprint $table) {
            $table->id();
            
            // menggunakan string biasa (bukan foreign key) karena dompet asing
            // yang tidak terdaftar di whitelist juga akan tercatat jika mereka mencoba login.
            $table->string('wallet_address')->comment('Alamat dompet yang mencoba login');
            
            $table->text('signature')->nullable()->comment('Digital signature dari Web3 (MetaMask)');
            $table->string('ip_address')->nullable()->comment('IP asal pengguna');
            $table->string('mac_address')->nullable()->comment('MAC Address perangkat pengguna');
            
            $table->enum('status', ['Success', 'Failed'])->comment('Status login');
            $table->text('message')->nullable()->comment('Alasan jika gagal (Misal: Wallet diblokir/tidak terdaftar)');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_logs');
    }
};
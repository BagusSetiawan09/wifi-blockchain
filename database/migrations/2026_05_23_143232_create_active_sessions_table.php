<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel wallets agar kita tahu ini sesi milik siapa
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            
            $table->string('mac_address')->comment('MAC Address dari HP/Laptop pengguna');
            $table->string('ip_address')->comment('IP Lokal yang didapat dari MikroTik');
            
            $table->timestamp('start_time')->comment('Waktu mulai terhubung');
            $table->timestamp('end_time')->nullable()->comment('Waktu terputus (jika ada)');
            
            $table->enum('status', ['Connected', 'Disconnected'])->default('Connected');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_sessions');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel routers.
     * Tabel ini menyimpan kredensial dan konfigurasi perangkat jaringan (MikroTik).
     */
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_router')->comment('Nama identifikasi router, misal: MikroTik Lab Komputer');
            $table->string('ip_address')->comment('Alamat IP untuk API MikroTik');
            $table->integer('api_port')->default(8728)->comment('Port standar API MikroTik adalah 8728');
            $table->string('api_username')->comment('Username API MikroTik');
            
            // Tipe data text digunakan untuk menampung string hasil enkripsi
            $table->text('api_password')->nullable()->comment('Password API MikroTik (Terenkripsi)');
            
            $table->boolean('is_active')->default(true)->comment('Status ketersediaan router');
            $table->timestamps();
        });
    }

    /**
     * Membalikkan migrasi (menghapus tabel routers).
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
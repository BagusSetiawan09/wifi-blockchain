<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_router',
        'ip_address',
        'api_port',
        'api_username',
        'api_password',
        'is_active',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     * Standar keamanan tinggi: Password API dienkripsi di database 
     * dan otomatis didekripsi ketika dipanggil di dalam aplikasi.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_password' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}
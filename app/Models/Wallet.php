<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_address',
        'nama_pemilik',
        'role',
        'is_whitelisted',
    ];

    protected function casts(): array
    {
        return [
            'is_whitelisted' => 'boolean',
        ];
    }

    public function activeSessions()
    {
        return $this->hasMany(ActiveSession::class);
    }
}
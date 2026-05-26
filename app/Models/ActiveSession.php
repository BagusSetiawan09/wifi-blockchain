<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'mac_address',
        'ip_address',
        'start_time',
        'end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    // Relasi balik ke tabel Wallet
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
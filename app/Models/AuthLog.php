<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_address',
        'signature',
        'ip_address',
        'mac_address',
        'status',
        'message',
    ];
}
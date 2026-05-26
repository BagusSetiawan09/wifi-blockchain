<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WifiAuthController;
use App\Http\Controllers\Api\VerifyAuthController;

// Mengalihkan halaman utama (/) langsung ke halaman login WiFi
Route::redirect('/', '/wifi/login');

// Route untuk menampilkan halaman login WiFi (diakses saat user redirect dari MikroTik)
Route::get('/wifi/login', [WifiAuthController::class, 'showLogin'])->name('wifi.login');

// Route API untuk menerima data signature dari halaman login Web3
Route::post('/api/verify-login', [VerifyAuthController::class, 'verify'])->name('api.verify');
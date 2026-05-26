<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WifiAuthController extends Controller
{
    /**
     * Menampilkan halaman login Captive Portal WiFi.
     * Menangkap data parameter dari query string MikroTik.
     */
    public function showLogin(Request $request)
    {
        // MikroTik biasanya mengirimkan data IP dan MAC via query string saat redirect
        // Contoh: /wifi/login?mac=00:11:22:33:44:55&ip=192.168.88.10
        $mac = $request->query('mac', '00:00:00:00:00:00');
        $ip = $request->query('ip', '0.0.0.0');
        
        // link-login-only adalah URL internal MikroTik untuk memproses login (jika dibutuhkan)
        $linkLoginOnly = $request->query('link-login-only', '');

        // Mengirimkan data ke view Blade
        return view('wifi.login', compact('mac', 'ip', 'linkLoginOnly'));
    }
}
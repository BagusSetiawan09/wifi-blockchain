# WiFi-Blockchain: Web3 WiFi Hotspot Authentication System

[![Laravel Version](https://img.shields.io/badge/Laravel-v10.x-red.svg)](https://laravel.com)
[![RouterOS Version](https://img.shields.io/badge/MikroTik-RouterOS%20v7.x-blue.svg)](https://mikrotik.com)

**WiFi-Blockchain** adalah sistem manajemen autentikasi gateway internet bertenaga Web3 yang mengintegrasikan **MikroTik RouterOS Hotspot** dengan dashboard **Laravel**. Sistem ini memungkinkan pengguna (klien) untuk mendapatkan akses internet melalui jaringan WiFi Captive Portal setelah melakukan verifikasi wallet crypto mereka di dashboard Web3.

---

## Fitur Utama

- **Web3 Wallet Authentication:** Autentikasi modern menggunakan alamat crypto wallet (MetaMask, WalletConnect, dll).
- **MikroTik Captive Portal Integration:** Manajemen otomatis akses internet klien berbasis IP/MAC Bindings melalui interaksi API RouterOS.
- **Dynamic Walled Garden:** Membuka jalur khusus untuk server internal dan API Web3 sebelum klien terautentikasi.
- **Admin Dashboard:** Panel kendali untuk memantau alamat crypto yang terdaftar, status perangkat klien, riwayat sesi aktif, dan manajemen hak akses.
- **Fail-safe Client Handling:** Sinkronisasi otomatis MAC Address fisik perangkat klien untuk akses internet yang stabil tanpa perlu login berulang kali.

---

## Tech Stack

- **Backend Framework:** Laravel (PHP)
- **Networking Device:** MikroTik Routerboard (RouterOS v7.x)
- **Frontend / UI:** Tailwind CSS, Blade Templates
- **Web3 Integration:** Ethers.js / Web3.js
- **Database:** MySQL / MariaDB

---

## Arsitektur & Alur Jaringan

1. **Klien Terhubung:** Klien menyambung ke SSID `WiFi-Blockchain` (dipancarkan via Virtual AP `wlan2`).
2. **Captive Portal Redirect:** MikroTik mendeteksi perangkat baru dan mengarahkan (*direct*) lalu lintas HTTP menggunakan file `login.html` ke URL Server Laravel (`http://<ip-laptop-server>:8000`).
3. **Web3 Verification:** Klien menyambungkan crypto wallet mereka di halaman login Laravel. Sistem mengecek status registrasi alamat crypto tersebut di database.
4. **Internet Access Granted:** Jika terverifikasi, Laravel mengirimkan perintah API ke MikroTik untuk membuat aturan `IP Binding` (tipe `bypassed`) menggunakan MAC Address perangkat klien. Akses internet dibuka sepenuhnya.

---

## Instalasi Aplikasi (Laravel)

1. **Clone Repositori:**
   ```bash
   git clone [https://github.com/username/wifi-blockchain.git](https://github.com/username/wifi-blockchain.git)
   cd wifi-blockchain
   ```

2. **Install Dependensi:**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Konfigurasi Environment (`.env`):**
   Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database serta API MikroTik Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wifi_blockchain
   DB_USERNAME=root
   DB_PASSWORD=

   MIKROTIK_HOST=192.168.88.1
   MIKROTIK_USER=admin
   MIKROTIK_PASS=password_mikrotik_anda
   ```

4. **Jalankan Migration:**
   ```bash
   php artisan migrate
   ```

5. **Jalankan Server:**
   **PENTING:** Anda wajib menggunakan *flag* `--host=0.0.0.0` agar aplikasi Laravel ini bisa diakses oleh perangkat luar (klien) yang berada dalam satu jaringan WiFi:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

---

## Konfigurasi Dasar MikroTik RouterOS

Untuk memastikan sistem portal berfungsi dengan baik, pastikan pengaturan berikut telah dilakukan di Winbox MikroTik Anda:

### 1. Bridge & Ports
Pastikan pemancar WiFi (`wlan2` / Virtual AP) dan kabel LAN (`ether2`) berada di dalam *Bridge* yang sama:
- Masuk ke **Bridge** > tab **Ports**, tambahkan `wlan2` dan `ether2` ke dalam `bridge1`.

### 2. Walled Garden IP List
Izinkan akses pra-login ke server Laravel Anda agar halaman Web3 bisa dibuka:
- Masuk ke **IP** > **Hotspot** > **Walled Garden IP List**
- Tambah (*New*): `Action=accept`, `Dst. Address=192.168.88.248` (IP Laptop Server), `Dst. Port=8000`.

### 3. Server Profiles (Hotspot)
- Buka profil hotspot Anda (contoh: `hsprof1`).
- Di tab **General**, isi **DNS Name** (contoh: `hotspot.unpab.local`). Jangan gunakan `http://`.
- Di tab **Login**, pastikan **Cookie** TIDAK dicentang selama fase *testing*.

### 4. File `login.html`
Pastikan file `login.html` di dalam folder `hotspot` MikroTik Anda memiliki kode *redirect* ke IP server Laravel:
```html
<meta http-equiv="refresh" content="0; url=[http://192.168.88.248:8000](http://192.168.88.248:8000)" />
```

---
*Developed for Web3-based network authentication by Bagus Setiawan.*
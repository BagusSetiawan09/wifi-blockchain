// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * @title WiFiAccessControl
 * @dev Smart Contract untuk Autentikasi dan Manajemen Hak Akses Jaringan Wi-Fi
 */
contract WiFiAccessControl {
    // Alamat dompet administrator sistem
    address public admin;

    // Struktur data untuk menyimpan status pengguna
    struct User {
        bool isRegistered;
        bool isActive;
    }

    // Mapping untuk menghubungkan alamat wallet dengan data User
    mapping(address => User) public users;

    // Modifier untuk membatasi akses hanya kepada administrator
    modifier onlyAdmin() {
        require(msg.sender == admin, "Akses Ditolak: Hanya Admin yang dapat mengeksekusi fungsi ini");
        _;
    }

    // Konstruktor dijalankan sekali saat contract di-deploy
    constructor() {
        // Menetapkan deployer sebagai admin
        admin = msg.sender;
    }

    /**
     * @dev Fungsi untuk mendaftarkan alamat wallet baru (Whitelist)
     * @param walletAddress Alamat wallet Ethereum klien
     */
    function registerUser(address walletAddress) public onlyAdmin {
        require(!users[walletAddress].isRegistered, "Gagal: Wallet address sudah terdaftar di sistem");
        
        users[walletAddress] = User({
            isRegistered: true,
            isActive: true
        });
    }

    /**
     * @dev Fungsi untuk memverifikasi apakah wallet valid dan aktif saat mencoba login
     * @param walletAddress Alamat wallet Ethereum klien yang mencoba koneksi
     * @return bool Mengembalikan true jika valid, false jika tidak
     */
    function verifyIdentity(address walletAddress) public view returns (bool) {
        if(users[walletAddress].isRegistered && users[walletAddress].isActive) {
            return true;
        }
        return false;
    }

    /**
     * @dev Fungsi untuk mencabut hak akses jaringan dari wallet tertentu (Blacklist)
     * @param walletAddress Alamat wallet Ethereum klien
     */
    function revokeAccess(address walletAddress) public onlyAdmin {
        require(users[walletAddress].isRegistered, "Gagal: Wallet address tidak ditemukan di sistem");
        
        // Menonaktifkan status user
        users[walletAddress].isActive = false;
    }
}
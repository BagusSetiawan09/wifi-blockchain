<?php

namespace App\Filament\Widgets;

use App\Models\ActiveSession;
use App\Models\Wallet;
use App\Models\AuthLog;
use App\Models\Router;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    // Mengatur urutan tampilan widget di halaman Dashboard
    protected static ?int $sort = 1;

    // FITUR ENTERPRISE: Dashboard akan me-refresh data secara otomatis 
    // setiap 15 detik tanpa perlu menekan F5/Refresh browser.
    // Sangat cocok untuk sistem monitoring jaringan!
    protected static ?string $pollingInterval = '15s';

    /**
     * Mengumpulkan dan menghitung data untuk ditampilkan di Widget.
     */
    protected function getStats(): array
    {
        // 1. Mengambil data Sesi Aktif
        $activeSessionsCount = ActiveSession::where('status', 'Connected')->count();
        
        // 2. Mengambil data Wallet (Total & Whitelisted)
        $totalWallets = Wallet::count();
        $whitelistedWallets = Wallet::where('is_whitelisted', true)->count();
        
        // 3. Mengambil data Log Keamanan HARI INI
        $todayFailedLogs = AuthLog::whereDate('created_at', today())
                                 ->where('status', 'Failed')
                                 ->count();
        $todayTotalLogs = AuthLog::whereDate('created_at', today())->count();

        // 4. Mengambil status Router (Berapa yang online/aktif)
        $activeRouters = Router::where('is_active', true)->count();
        $totalRouters = Router::count();

        return [
            // Widget 1: Pantauan Sesi Jaringan
            Stat::make('Sesi Jaringan Aktif', $activeSessionsCount)
                ->description('Perangkat sedang terhubung ke internet')
                ->descriptionIcon('heroicon-m-wifi')
                ->color('success')
                // Menambahkan array dummy untuk memunculkan efek Sparkline Chart yang elegan
                ->chart([2, 3, 5, 4, 8, 5, $activeSessionsCount]),

            // Widget 2: Pantauan Autentikasi Pengguna
            Stat::make('Total Wallet Terdaftar', $totalWallets)
                ->description($whitelistedWallets . ' dompet berada dalam Whitelist')
                ->descriptionIcon('heroicon-m-identification')
                ->color('info'),

            // Widget 3: Pantauan Keamanan & Ancaman
            Stat::make('Ancaman Keamanan Hari Ini', $todayFailedLogs)
                ->description('Dari total ' . $todayTotalLogs . ' percobaan autentikasi')
                ->descriptionIcon($todayFailedLogs > 0 ? 'heroicon-m-shield-exclamation' : 'heroicon-m-shield-check')
                // Logika Dinamis: Jika ada percobaan ilegal, warna berubah merah (danger)
                ->color($todayFailedLogs > 0 ? 'danger' : 'gray')
                ->chart([0, 1, 0, 2, 1, 0, $todayFailedLogs]),
                
            // Widget 4: Pantauan Infrastruktur
            Stat::make('Router Aktif', $activeRouters . ' / ' . $totalRouters)
                ->description('Node pemancar yang beroperasi')
                ->descriptionIcon('heroicon-m-server-stack')
                ->color($activeRouters === $totalRouters ? 'success' : 'warning'),
        ];
    }
}
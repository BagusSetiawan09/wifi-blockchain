<?php

namespace App\Filament\Widgets;

use App\Models\Wallet;
use Filament\Widgets\ChartWidget;

class RoleDistributionChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Hak Akses Pengguna';
    
    protected static ?int $sort = 3; 

    // 1. KUNCI TINGGI GRAFIK: Menyamakan tinggi maksimal dengan Line Chart di sebelahnya
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $mahasiswa = Wallet::where('role', 'Mahasiswa')->count();
        $dosen = Wallet::where('role', 'Dosen')->count();
        $staf = Wallet::where('role', 'Staf')->count();
        $tamu = Wallet::where('role', 'Tamu')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pengguna',
                    'data' => [$mahasiswa, $dosen, $staf, $tamu],
                    'backgroundColor' => [
                        '#3b82f6', // Biru
                        '#10b981', // Hijau
                        '#f59e0b', // Kuning
                        '#6b7280', // Abu-abu
                    ],
                    'borderColor' => '#18181b', // Border menyesuaikan warna Dark Mode Filament
                    'hoverOffset' => 4
                ],
            ],
            'labels' => ['Mahasiswa', 'Dosen', 'Staf / Karyawan', 'Tamu / VIP'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; 
    }

    // 2. OVERRIDE OPTIONS: Mengatur tampilan spesifik Chart.js
    protected function getOptions(): array
    {
        return [
            // Mematikan garis ukur (grid lines) sumbu X dan Y yang tidak perlu di chart donat
            'scales' => [
                'x' => [
                    'display' => false, 
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            // Mengatur rasio agar mengikuti maxHeight yang kita tentukan di atas
            'maintainAspectRatio' => false,
            // Membuat donat sedikit lebih tipis (elegan)
            'cutout' => '70%', 
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom', // Memindahkan posisi legenda ke bawah agar chart bisa lebih besar
                ],
            ],
        ];
    }
}
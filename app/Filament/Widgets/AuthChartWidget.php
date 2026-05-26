<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AuthChartWidget extends ChartWidget
{
    // Judul grafik
    protected static ?string $heading = 'Tren Autentikasi (7 Hari Terakhir)';
    
    // Mengatur urutan agar berada di bawah Stats Overview
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Login Berhasil (Akses Diberikan)',
                    'data' => [12, 19, 15, 22, 18, 25, 30], // Data simulasi
                    'borderColor' => '#10b981', // Warna Hijau (Success)
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Login Gagal (Akses Ditolak)',
                    'data' => [2, 5, 1, 8, 3, 1, 4], // Data simulasi
                    'borderColor' => '#ef4444', // Warna Merah (Danger)
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        ];
    }

    protected function getType(): string
    {
        // Menggunakan tipe 'line' untuk grafik garis
        return 'line';
    }
}
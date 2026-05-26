<?php

namespace App\Filament\Widgets;

use App\Models\AuthLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAuthLogsWidget extends BaseWidget
{
    // Mengatur urutan agar berada di paling bawah (setelah grafik)
    protected static ?int $sort = 4;
    
    // Membuat tabel membentang penuh (full width) dari kiri ke kanan layar
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            // Mengambil 5 data log paling baru dari database
            ->query(AuthLog::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('wallet_address')
                    ->label('Wallet Address')
                    ->fontFamily('mono')
                    ->limit(15),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Success' => 'success',
                        'Failed' => 'danger',
                    }),
                    
                Tables\Columns\TextColumn::make('message')
                    ->label('Keterangan')
                    ->limit(40),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            // Mematikan pagination (nomor halaman) karena ini hanya ringkasan 5 data teratas
            ->paginated(false); 
    }
}
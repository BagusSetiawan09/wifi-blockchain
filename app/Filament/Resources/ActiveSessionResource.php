<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActiveSessionResource\Pages;
use App\Models\ActiveSession;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ActiveSessionResource extends Resource
{
    protected static ?string $model = ActiveSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationLabel = 'Sesi Jaringan Aktif';
    protected static ?string $modelLabel = 'Sesi Jaringan';
    protected static ?string $navigationGroup = 'Pemantauan (Monitoring)';

    // Menonaktifkan tombol "Create" karena data ini diisi otomatis oleh sistem Captive Portal
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        // Form dikosongkan karena tidak ada fitur Create/Edit
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Sesi Pengguna')
                    ->schema([
                        Infolists\Components\TextEntry::make('wallet.nama_pemilik')
                            ->label('Nama Pengguna')
                            ->weight('bold'),
                            
                        Infolists\Components\TextEntry::make('wallet.wallet_address')
                            ->label('Wallet Address')
                            ->copyable()
                            ->fontFamily('mono'),
                            
                        Infolists\Components\TextEntry::make('mac_address')
                            ->label('MAC Address Perangkat')
                            ->fontFamily('mono'),
                            
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address Lokal')
                            ->copyable(),
                            
                        Infolists\Components\TextEntry::make('start_time')
                            ->label('Waktu Mulai Terhubung')
                            ->dateTime('d M Y, H:i:s'),
                            
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status Jaringan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Connected' => 'success',
                                'Disconnected' => 'danger',
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Mengurutkan data terbaru di paling atas secara default
            ->defaultSort('start_time', 'desc') 
            ->columns([
                Tables\Columns\TextColumn::make('wallet.nama_pemilik')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->fontFamily('mono'),
                    
                Tables\Columns\TextColumn::make('mac_address')
                    ->label('MAC Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Mulai Terhubung')
                    ->dateTime('H:i, d M Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Connected' => 'success',
                        'Disconnected' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Connected' => 'Connected',
                        'Disconnected' => 'Disconnected',
                    ])
                    ->label('Filter Status'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    // Aksi kustom untuk Kick (Memutus) user
                    Tables\Actions\Action::make('kick_user')
                        ->label('Putus Koneksi')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Putus Koneksi Pengguna?')
                        ->modalDescription('Tindakan ini akan mengubah status menjadi Disconnected. Nantinya ini akan memicu API ke MikroTik untuk memutus koneksi perangkat.')
                        ->action(function (ActiveSession $record) {
                            $record->update([
                                'status' => 'Disconnected',
                                'end_time' => now(),
                            ]);
                            // Note: Kode integrasi MikroTik API untuk menghapus MAC Address dari Walled Garden/Hotspot Active akan diletakkan di sini.
                        })
                        // Tombol Kick hanya muncul jika status masih Connected
                        ->visible(fn (ActiveSession $record): bool => $record->status === 'Connected'),
                ])
                ->label('Options')
                ->button()
                ->color('gray')
                ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            // Hanya ada index (List). Halaman Create dan Edit dihapus.
            'index' => Pages\ListActiveSessions::route('/'),
        ];
    }
}
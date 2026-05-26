<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RouterResource\Pages;
use App\Models\Router;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RouterResource extends Resource
{
    protected static ?string $model = Router::class;

    // Mengatur ikon menu di sidebar Filament
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    
    // Penamaan menu di sidebar
    protected static ?string $navigationLabel = 'Manajemen Router';
    protected static ?string $modelLabel = 'Router';
    protected static ?string $navigationGroup = 'Infrastruktur Jaringan';

    /**
     * Skema Formulir (Form Schema) untuk Create & Edit Router.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar Router')
                    ->description('Masukkan identitas dan alamat IP perangkat MikroTik.')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nama_router')
                                ->label('Nama Router')
                                ->placeholder('Contoh: MikroTik Gedung A')
                                ->required()
                                ->maxLength(255),
                                
                            Forms\Components\TextInput::make('ip_address')
                                ->label('IP Address API')
                                ->placeholder('Contoh: 192.168.88.1')
                                ->required()
                                ->ipv4() // Validasi khusus format IPv4
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Kredensial API RouterOS')
                    ->description('Digunakan oleh sistem untuk mengeksekusi perintah bypass MAC Address.')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('api_port')
                                ->label('API Port')
                                ->required()
                                ->numeric()
                                ->default(8728), // Port default MikroTik
                                
                            Forms\Components\TextInput::make('api_username')
                                ->label('API Username')
                                ->required()
                                ->maxLength(255),
                                
                            Forms\Components\TextInput::make('api_password')
                                ->label('API Password')
                                ->password() // Menyembunyikan input (tipe password)
                                ->revealable() // Fitur ikon mata untuk melihat password
                                ->maxLength(255),
                        ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Matikan toggle ini jika router sedang dalam perbaikan (maintenance).')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    /**
     * Skema Infolist (View Detail Schema) standar Enterprise.
     * Ditampilkan saat tombol View diklik, menyajikan data secara read-only dengan UI yang rapi.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dasar Router')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_router')
                            ->label('Nama Router')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                            
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address API')
                            ->icon('heroicon-m-globe-alt')
                            ->copyable()
                            ->copyMessage('IP Address berhasil disalin!'),
                    ])->columns(2),

                Infolists\Components\Section::make('Konfigurasi API & Status')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Infolists\Components\TextEntry::make('api_port')
                            ->label('Port API'),
                            
                        Infolists\Components\TextEntry::make('api_username')
                            ->label('Username API'),
                            
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean(),
                            
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Ditambahkan Pada')
                            ->dateTime('d M Y, H:i'),
                    ])->columns(2),
                    
                // Catatan Keamanan: Password API sengaja tidak ditampilkan di Infolist
                // untuk mencegah kebocoran kredensial dari bahu (shoulder surfing) saat admin sedang memantau.
            ]);
    }

    /**
     * Skema Tabel (Table Schema) untuk menampilkan daftar Router.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_router')
                    ->label('Nama Router')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->icon('heroicon-m-globe-alt') 
                    ->copyable() 
                    ->copyMessage('IP Address berhasil disalin!'),
                    
                Tables\Columns\TextColumn::make('api_port')
                    ->label('Port')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->sortable(), 
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i') 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Filter Status Aktif'),
            ])
            ->actions([
                // Mengelompokkan aksi ke dalam satu tombol dropdown "Options"
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Options')
                ->button() // Mengubah icon default menjadi tampilan tombol penuh
                ->color('gray') // Memberikan warna abu-abu sesuai permintaan
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRouters::route('/'),
            'create' => Pages\CreateRouter::route('/create'),
            'edit' => Pages\EditRouter::route('/{record}/edit'),
            // Halaman view tidak didaftarkan sebagai route terpisah
            // agar Filament otomatis membukanya dalam bentuk Modal Dialog yang modern.
        ];
    }
}
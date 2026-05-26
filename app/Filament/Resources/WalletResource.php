<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    protected static ?string $modelLabel = 'Dompet Kripto (Wallet)';
    protected static ?string $navigationGroup = 'Autentikasi & Keamanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Pengguna Jaringan')
                    ->description('Daftarkan alamat dompet kripto (Web3) yang diizinkan mengakses jaringan WiFi.')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('wallet_address')
                                ->label('Wallet Address (0x...)')
                                ->placeholder('Contoh: 0x71C...976F')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->startsWith('0x') // Validasi standar dompet kripto (EVM)
                                ->maxLength(42)
                                ->columnSpanFull(), // Membuat inputan panjang penuh karena format wallet cukup panjang
                                
                            Forms\Components\TextInput::make('nama_pemilik')
                                ->label('Nama Pemilik')
                                ->placeholder('Masukkan nama asli pengguna')
                                ->required()
                                ->maxLength(255),
                                
                            Forms\Components\Select::make('role')
                                ->label('Grup / Peran')
                                ->options([
                                    'Mahasiswa' => 'Mahasiswa',
                                    'Dosen' => 'Dosen',
                                    'Staf' => 'Staf / Karyawan',
                                    'Tamu' => 'Tamu / VIP',
                                ])
                                ->required()
                                ->native(false),
                        ]),
                        
                        Forms\Components\Toggle::make('is_whitelisted')
                            ->label('Izinkan Akses (Whitelist)')
                            ->helperText('Jika dimatikan, dompet ini tidak akan bisa login ke WiFi meskipun valid di Blockchain.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Hak Akses Dompet')
                    ->icon('heroicon-o-wallet')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_pemilik')
                            ->label('Nama Pemilik')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                            
                        Infolists\Components\TextEntry::make('wallet_address')
                            ->label('Wallet Address')
                            ->copyable()
                            ->copyMessage('Address disalin!')
                            ->fontFamily('mono'),
                            
                        Infolists\Components\TextEntry::make('role')
                            ->label('Grup Peran')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Mahasiswa' => 'info',
                                'Dosen' => 'success',
                                'Staf' => 'warning',
                                'Tamu' => 'gray',
                            }),
                            
                        Infolists\Components\IconEntry::make('is_whitelisted')
                            ->label('Status Akses')
                            ->boolean(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('wallet_address')
                    ->label('Wallet Address')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(15) // Menyingkat tampilan wallet di tabel agar tidak terlalu panjang
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state; // Menampilkan full address saat di-hover
                    }),
                    
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mahasiswa' => 'info',
                        'Dosen' => 'success',
                        'Staf' => 'warning',
                        'Tamu' => 'gray',
                    }),
                    
                Tables\Columns\ToggleColumn::make('is_whitelisted')
                    ->label('Whitelist')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Peran')
                    ->options([
                        'Mahasiswa' => 'Mahasiswa',
                        'Dosen' => 'Dosen',
                        'Staf' => 'Staf',
                        'Tamu' => 'Tamu',
                    ]),
                Tables\Filters\TernaryFilter::make('is_whitelisted')
                    ->label('Filter Status Whitelist'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
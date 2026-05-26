<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthLogResource\Pages;
use App\Models\AuthLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;

class AuthLogResource extends Resource
{
    protected static ?string $model = AuthLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Log Autentikasi';
    protected static ?string $modelLabel = 'Log Keamanan';
    protected static ?string $navigationGroup = 'Autentikasi & Keamanan';

    // MENGUNCI MENU AGAR READ-ONLY
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Audit Login')
                    ->icon('heroicon-o-finger-print')
                    ->schema([
                        Infolists\Components\TextEntry::make('wallet_address')
                            ->label('Wallet Address')
                            ->fontFamily('mono')
                            ->copyable(),
                            
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Success' => 'success',
                                'Failed' => 'danger',
                            }),
                            
                        Infolists\Components\TextEntry::make('message')
                            ->label('Pesan Sistem')
                            ->color(fn ($record) => $record->status === 'Failed' ? 'danger' : 'gray'),
                            
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Waktu Percobaan')
                            ->dateTime('d M Y, H:i:s'),
                            
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address'),
                            
                        Infolists\Components\TextEntry::make('mac_address')
                            ->label('MAC Address')
                            ->fontFamily('mono'),
                            
                        Infolists\Components\TextEntry::make('signature')
                            ->label('Web3 Digital Signature')
                            ->fontFamily('mono')
                            ->columnSpanFull()
                            ->copyable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('wallet_address')
                    ->label('Wallet Address')
                    ->searchable()
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
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Success' => 'Success',
                        'Failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->button()
                    ->color('gray'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthLogs::route('/'),
        ];
    }
}
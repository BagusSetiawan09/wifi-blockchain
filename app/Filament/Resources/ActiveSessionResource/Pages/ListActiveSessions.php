<?php

namespace App\Filament\Resources\ActiveSessionResource\Pages;

use App\Filament\Resources\ActiveSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActiveSessions extends ListRecords
{
    protected static string $resource = ActiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

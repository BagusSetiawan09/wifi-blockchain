<?php

namespace App\Filament\Resources\ActiveSessionResource\Pages;

use App\Filament\Resources\ActiveSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActiveSession extends EditRecord
{
    protected static string $resource = ActiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

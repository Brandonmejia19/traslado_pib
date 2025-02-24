<?php

namespace App\Filament\Resources\ISSListadoResource\Pages;

use App\Filament\Resources\ISSListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditISSListado extends EditRecord
{
    protected static string $resource = ISSListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

        ];
    }
}

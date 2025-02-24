<?php

namespace App\Filament\Resources\PrivadoListadoResource\Pages;

use App\Filament\Resources\PrivadoListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrivadoListado extends EditRecord
{
    protected static string $resource = PrivadoListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

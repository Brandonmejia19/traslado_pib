<?php

namespace App\Filament\Resources\UnidadListadoResource\Pages;

use App\Filament\Resources\UnidadListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnidadListado extends EditRecord
{
    protected static string $resource = UnidadListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TipoTrasladoResource\Pages;

use App\Filament\Resources\TipoTrasladoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoTraslado extends EditRecord
{
    protected static string $resource = TipoTrasladoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

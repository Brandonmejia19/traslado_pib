<?php

namespace App\Filament\Resources\TipoTrasladoResource\Pages;

use App\Filament\Resources\TipoTrasladoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoTraslados extends ListRecords
{
    protected static string $resource = TipoTrasladoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

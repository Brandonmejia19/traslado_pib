<?php

namespace App\Filament\Resources\PrivadoListadoResource\Pages;

use App\Filament\Resources\PrivadoListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\PrivadoListadoImporter;
use Filament\Actions\ImportAction;

class ListPrivadoListados extends ListRecords
{
    protected static string $resource = PrivadoListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
            ->importer(PrivadoListadoImporter::class)
        ];
    }
}

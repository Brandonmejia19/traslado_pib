<?php

namespace App\Filament\Resources\UnidadListadoResource\Pages;

use App\Filament\Resources\UnidadListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\UnidadListadoImporter;
use Filament\Actions\ImportAction;
class ListUnidadListados extends ListRecords
{
    protected static string $resource = UnidadListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(UnidadListadoImporter::class)
        ];
    }
}

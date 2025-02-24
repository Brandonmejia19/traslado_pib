<?php

namespace App\Filament\Resources\ISSListadoResource\Pages;

use App\Filament\Resources\ISSListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\ISSListadoImporter;
use Filament\Actions\ImportAction;

class ListISSListados extends ListRecords
{
    protected static string $resource = ISSListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(ISSListadoImporter::class)
        ];
    }
}

<?php

namespace App\Filament\Resources\HospitalListadoResource\Pages;

use App\Filament\Resources\HospitalListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\HospitalListadoImporter;
use Filament\Actions\ImportAction;

class ListHospitalListados extends ListRecords
{
    protected static string $resource = HospitalListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
            ->importer(HospitalListadoImporter::class)
        ];
    }
}

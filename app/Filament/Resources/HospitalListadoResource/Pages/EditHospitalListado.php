<?php

namespace App\Filament\Resources\HospitalListadoResource\Pages;

use App\Filament\Resources\HospitalListadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHospitalListado extends EditRecord
{
    protected static string $resource = HospitalListadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

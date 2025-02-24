<?php

namespace App\Filament\Resources\TrasladoSecundarioResource\Pages;

use App\Filament\Resources\TrasladoSecundarioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrasladoSecundario extends EditRecord
{
    protected static string $resource = TrasladoSecundarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}

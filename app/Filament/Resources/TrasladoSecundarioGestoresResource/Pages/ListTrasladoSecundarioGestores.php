<?php

namespace App\Filament\Resources\TrasladoSecundarioGestoresResource\Pages;

use App\Filament\Resources\TrasladoSecundarioGestoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrasladoSecundarioGestores extends ListRecords
{
    protected static string $resource = TrasladoSecundarioGestoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
        
        ];
    }
}

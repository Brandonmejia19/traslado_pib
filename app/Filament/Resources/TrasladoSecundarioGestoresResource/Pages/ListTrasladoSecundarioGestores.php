<?php

namespace App\Filament\Resources\TrasladoSecundarioGestoresResource\Pages;

use App\Filament\Resources\TrasladoSecundarioGestoresResource;
use App\Filament\Resources\TrasladoSecundarioGestoresResource\Widgets\GestionRecursos;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrasladoSecundarioGestores extends ListRecords
{
    protected static string $resource = TrasladoSecundarioGestoresResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            GestionRecursos::class,
        ];
    }
}

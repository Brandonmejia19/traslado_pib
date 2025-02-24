<?php

namespace App\Filament\Resources\TrasladoSecundarioPropiosResource\Pages;

use App\Filament\Resources\TrasladoSecundarioPropiosResource;
use App\Filament\Resources\TrasladoSecundarioPropiosResource\Widgets\TrasladosSecundariosPropios;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListTrasladoSecundarioPropios extends ListRecords
{
    protected static string $resource = TrasladoSecundarioPropiosResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            TrasladosSecundariosPropios::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

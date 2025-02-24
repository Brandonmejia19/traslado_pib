<?php

namespace App\Filament\Resources\TrasladoSecundarioResource\Pages;

use App\Filament\Resources\TrasladoSecundarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundarioResource\Widgets\TrasladosSecundarios;
class ListTrasladoSecundarios extends ListRecords
{
    protected static string $resource = TrasladoSecundarioResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            TrasladosSecundarios::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

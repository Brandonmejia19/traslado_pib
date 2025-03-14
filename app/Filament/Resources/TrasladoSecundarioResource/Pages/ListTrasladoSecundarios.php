<?php

namespace App\Filament\Resources\TrasladoSecundarioResource\Pages;

use App\Filament\Resources\TrasladoSecundarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundarioResource\Widgets\TrasladosSecundarios;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;

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

        ];
    }
}

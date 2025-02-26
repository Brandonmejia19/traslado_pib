<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Pages;

use App\Filament\Resources\TrasladoSecundario24Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundario24Resource\Widgets\TrasladosSecundarios24;

class ListTrasladoSecundario24s extends ListRecords
{
    protected static string $resource = TrasladoSecundario24Resource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            TrasladosSecundarios24::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

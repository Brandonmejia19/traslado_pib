<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Pages;

use App\Filament\Resources\TrasladoSecundario24Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundario24Resource\Widgets\TrasladosSecundarios24;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;
class ListTrasladoSecundario24s extends ListRecords
{
    protected static string $resource = TrasladoSecundario24Resource::class;
    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make(),
            'Prioridad 1' => Tab::make()
                ->modifyQueryUsing(callback: fn(Builder $query) => $query->where('prioridad', 1)),
            'Prioridad 2' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('prioridad', 2)),
            'Prioridad 3' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('prioridad', 3)),
            'Prioridad 4' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('prioridad', 4)),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TrasladosSecundarios24::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
         //   Actions\CreateAction::make(),
        ];
    }
}

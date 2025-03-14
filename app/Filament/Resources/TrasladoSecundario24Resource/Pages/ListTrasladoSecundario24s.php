<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Pages;

use App\Filament\Resources\TrasladoSecundario24Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundario24Resource\Widgets\TrasladosSecundarios24HH;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;
class ListTrasladoSecundario24s extends ListRecords
{
    protected static string $resource = TrasladoSecundario24Resource::class;
    public function getTabs(): array
    {
        return [
            'En Curso' => Tab::make()
                ->icon('heroicon-s-x-circle')
                ->badgeColor('warning')
                ->icon('heroicon-s-information-circle')
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('estado', 'En curso')),
            'Finalizado' => Tab::make()
                ->icon('heroicon-s-x-circle')
                ->badgeColor('success')
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('estado', 'Finalizado')),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TrasladosSecundarios24HH::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            //   Actions\CreateAction::make(),
        ];
    }
}

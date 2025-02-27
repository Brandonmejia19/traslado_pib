<?php

namespace App\Filament\Resources\TrasladoSecundarioPropiosResource\Pages;

use App\Filament\Resources\TrasladoSecundarioPropiosResource;
use App\Filament\Resources\TrasladoSecundarioPropiosResource\Widgets\TrasladosSecundariosPropios;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\Alignment;

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
            Actions\CreateAction::make('Crear')
                ->label(
                    'Crear Traslado Secundario'
                )->color('primary')
                ->modalWidth(
                    MaxWidth::SixExtraLarge
                )->icon(
                    'healthicons-o-mobile-clinic'
                )->modalIcon('healthicons-o-mobile-clinic')
                ->modalAlignment(Alignment::Center)
                ->modalHeading('Traslados Secundarios')
        ];
    }
}

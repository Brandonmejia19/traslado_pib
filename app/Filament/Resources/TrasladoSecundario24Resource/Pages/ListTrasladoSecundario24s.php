<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Pages;

use App\Filament\Resources\TrasladoSecundario24Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrasladoSecundario24Resource\Widgets\TrasladosSecundarios24HH;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;
use Carbon\Carbon;
class ListTrasladoSecundario24s extends ListRecords
{
    protected static string $resource = TrasladoSecundario24Resource::class;
    public function getTabs(): array
    {
        return [
            // 🔸 Tab de Casos "En Curso" (Siempre visibles sin importar fecha)
            'En Curso' => Tab::make()
                ->icon('heroicon-s-information-circle')
                ->badgeColor('warning')
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('estado', 'En curso')
                ),

            // ✅ Tab de Casos "Finalizados" en las últimas 24h
            'Finalizado' => Tab::make()
                ->icon('heroicon-s-check-circle')
                ->badgeColor('success')
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('estado', 'Finalizado')
                          ->where(function ($subquery) {
                              $subquery->where('created_at', '>=', Carbon::now()->subDay())
                                       ->orWhere('updated_at', '>=', Carbon::now()->subDay());
                          })
                ),
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

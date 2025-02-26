<?php

namespace App\Filament\Resources\TrasladoSecundarioPropiosResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\TrasladoSecundario;

class TrasladosSecundariosPropios extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        Carbon::setLocale('es');

        $startDate = Carbon::now()->startOfDay()->toDateString();
        $endDate = Carbon::now()->startOfDay()->toDateString();
        $venticuatrostart = Carbon::now()->subDay();

        $totaldia = TrasladoSecundario::query()
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
            ->where('user_id', auth()->id()) // Solo los del usuario autenticado
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        $totalactivos = TrasladoSecundario::query()
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
            ->where('user_id', auth()->id()) // Solo los del usuario autenticado
            ->where('created_at', '>=', Carbon::now()->subDay()) // Últimas 24 horas
            ->where('estado', operator: 'En curso')
            ->count();

        $totalpendientes = TrasladoSecundario::query()
            ->where('user_id', auth()->id()) // Solo los del usuario autenticado
            ->where('created_at', '>=', Carbon::now()->subDay())// Últimas 24 horas
            ->whereNull('ambulancia')

            ->count();

        return [
            Stat::make('Total de Traslados en el dia', $totaldia)
                //>description('Traslados críticos pendientes de asignación de recurso')
                ->chartColor('success')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Traslados Activos del día', $totalactivos)
                //->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->chartColor('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Pendientes de Asignacion de Recursos', $totalpendientes)
                //  ->description('Traslados En curso')
                ->chartColor('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17])

        ];
    }
}

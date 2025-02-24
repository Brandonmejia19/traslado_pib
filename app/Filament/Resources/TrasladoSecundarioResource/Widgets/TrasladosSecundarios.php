<?php

namespace App\Filament\Resources\TrasladoSecundarioResource\Widgets;

use App\Models\TrasladoSecundario;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TrasladosSecundarios extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        Carbon::setLocale('es');
        $startDate = Carbon::now()->startOfDay()->toDateString();
        $endDate = Carbon::now()->startOfDay()->toDateString();

        $totaldia = TrasladoSecundario::query()
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $totalactivos = TrasladoSecundario::query()
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->where('estado', operator: 'En curso')
            ->count();

        $totalpendientes = TrasladoSecundario::query()
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->whereNull('ambulancia')
            ->count();

        $totalcriticospendientes = TrasladoSecundario::query()
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->where('tipo_paciente', 'Critico')
            ->whereNull('ambulancia')
            ->count();

        return [
            Stat::make('Pendientes de asignación de recurso', $totalcriticospendientes)
                ->description('Traslados críticos pendientes de asignación de recurso')
                ->chartColor('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Traslados pendientes de asignación de recurso', $totalpendientes)
                ->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->chartColor('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Traslados Activos', $totalactivos)
                ->description('Traslados En curso')
                ->chartColor('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-ambulance'),
            Stat::make('Traslados en el día', $totaldia)
                ->description('Cantidad de Traslados Registrados el día: ' . Carbon::now()->format('d/m/Y'))
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('success')
                ->descriptionIcon('healthicons-o-hospitalized'),
        ];
    }
}

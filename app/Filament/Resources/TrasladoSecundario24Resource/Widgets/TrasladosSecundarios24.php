<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\TrasladoSecundario;

class TrasladosSecundarios24 extends BaseWidget
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
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $totalactivos = TrasladoSecundario::query()
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '<=', $venticuatrostart))
            ->orWhere('estado', operator: 'En curso')
            ->count();

        $totalpendientes = TrasladoSecundario::query()
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
            ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '<=', $venticuatrostart))
            ->whereNull('ambulancia')
            ->count();

        $totalcriticospendientes = TrasladoSecundario::query()
         //   ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
           // ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '<=', $venticuatrostart))
            ->where('tipo_paciente', 'Critico')
         //   ->where('estado','En curso')
            ->whereNull('ambulancia')
            ->count();

        return [
            Stat::make('Pendientes de agnación de recurso', $totalcriticospendientes)
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

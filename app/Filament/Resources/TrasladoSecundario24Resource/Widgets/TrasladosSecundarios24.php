<?php

namespace App\Filament\Resources\TrasladoSecundario24Resource\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
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

            ->orWhere('estado', operator: 'En curso')
            ->count();

        $totalpendientes = TrasladoSecundario::query()
            ->orWhere('estado', operator: 'En curso')

            ->whereNull('ambulancia')
            ->count();

        $totalcriticospendientes = TrasladoSecundario::query()
            //   ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '>=', $venticuatrostart))
            // ->when($venticuatrostart, fn($query) => $query->whereDate('created_at', '<=', $venticuatrostart))
            ->where('tipo_paciente', 'Critico')
            ->where('estado', 'En curso')
            //   ->where('estado','En curso')
            ->whereNull('ambulancia')
            ->count();

        return [
            Stat::make('Traslados críticos: ', $totalcriticospendientes)
                ->icon('healthicons-o-accident-and-emergency')->progress($totalcriticospendientes)
                ->description('Traslados críticos pendientes de asignación de recurso')
                ->descriptionIcon('healthicons-o-accident-and-emergency')
                ->iconColor('danger')
                ->iconBackgroundColor('danger')
                ->progressBarColor('danger')
                ->iconPosition('start')
                ->chartColor('danger'),
            Stat::make('Traslados pendientes: ', $totalpendientes)
                ->icon('healthicons-o-critical-care')
                ->iconBackgroundColor('danger')
                ->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->descriptionIcon('healthicons-o-critical-care')
                ->progress($totalpendientes)
                ->iconColor('danger')
                ->progressBarColor('danger')
                ->iconPosition('start')
                ->chartColor('danger'),
            Stat::make('Traslados Activos', $totalactivos)->progress($totalactivos)
                ->progressBarColor('primary')
                ->description('Traslados En curso')
                ->descriptionIcon('healthicons-o-ambulance')
                ->iconBackgroundColor('primary')
                ->iconPosition('start')
                ->icon('healthicons-o-ambulance')
                ->iconColor('primary')
                ->chartColor('primary'),
            Stat::make('Traslados en el día', $totaldia)
                ->progress($totaldia)->iconColor('success')
                ->iconBackgroundColor('success')
                ->iconPosition('start')
                ->description('Cantidad de Traslados Registrados el día: ' . Carbon::now()->format('d/m/Y'))
                ->progressBarColor('success')
                ->chartColor('success')
                ->descriptionIcon('healthicons-o-hospitalized', 'after')
                ->icon('healthicons-o-hospitalized'),
        ];
    }
}

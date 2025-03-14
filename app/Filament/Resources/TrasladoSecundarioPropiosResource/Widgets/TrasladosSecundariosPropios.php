<?php

namespace App\Filament\Resources\TrasladoSecundarioPropiosResource\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
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
                ->icon('healthicons-o-clinical-fe')
                ->progressBarColor('success')
                ->progress($totaldia)
                ->iconColor('success')
                //>description('Traslados críticos pendientes de asignación de recurso')
                ->iconBackgroundColor('success')
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Traslados Activos del día', $totalactivos)
                //->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->progressBarColor('primary')
                ->iconColor('primary')
                ->iconBackgroundColor('primary')
                ->progress($totalactivos)
                ->icon('healthicons-o-emergency-post'),
            Stat::make('Pendientes de Asignacion de Recursos', $totalpendientes)
                //  ->description('Traslados En curso')
                ->progressBarColor('warning')
                ->iconColor('warning')
                ->iconBackgroundColor('warning')
                ->icon('healthicons-o-ambulance')
                ->progress($totalpendientes)

        ];
    }
}

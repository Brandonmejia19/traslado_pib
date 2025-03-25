<?php

namespace App\Filament\Resources\TrasladoSecundarioGestoresResource\Widgets;


use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\TrasladoSecundario;
class GestionRecursos extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static bool $isLazy = false;
    protected function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        Carbon::setLocale('es');
        $startDate = Carbon::now()->startOfDay()->toDateString();
        $endDate = Carbon::now()->startOfDay()->toDateString();
        $venticuatrostart = Carbon::now()->subDay();


        $totaltraslados = TrasladoSecundario::query()
            ->where('asunto_traslado', operator: 'Transporte de Paciente')
            ->where('estado', operator: 'En curso')
            ->whereNull('ambulancia_id')
            ->count();

        $totalpendientes = TrasladoSecundario::query()
            ->where('asunto_traslado', 'Traslado de Paciente')
            ->where('estado', 'En curso')
            ->whereNull('ambulancia_id')
            ->count();

        return [
            Stat::make('Traslados de Paciente: ', $totalpendientes)
                ->icon('healthicons-o-ambulance')->progress($totalpendientes)
                ->description('Traslados pendientes de asignación de recurso')
                ->descriptionIcon('healthicons-o-ambulance')
                ->iconColor('danger')
                ->iconBackgroundColor('danger')
                ->progressBarColor('danger')
                ->iconPosition('start')
                ->chartColor('danger'),
            Stat::make('Transporte de Paciente: ', $totaltraslados)
                ->icon('healthicons-o-mobile-clinic')
                ->iconBackgroundColor('primary')
                ->description('Transportes pendientes de asignación de recurso')
                ->descriptionIcon('healthicons-o-mobile-clinic')
                ->progress($totaltraslados)
                ->iconColor('primary')
                ->progressBarColor('primary')
                ->iconPosition('start')
                ->chartColor('primary'),
        ];
    }
}

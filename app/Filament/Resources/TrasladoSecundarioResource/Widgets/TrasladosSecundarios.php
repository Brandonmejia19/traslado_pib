<?php

namespace App\Filament\Resources\TrasladoSecundarioResource\Widgets;

use App\Models\TrasladoSecundario;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
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
        $venticuatrostart = Carbon::now()->subDay();

        $total = TrasladoSecundario::query()
            ->where('estado', 'Finalizado')
            ->count();

        $totalestables = TrasladoSecundario::query()
            ->where('estado', 'Finalizado')
            ->where('tipo_paciente', operator: 'Estable')
            ->count();

        $totalcriticos = TrasladoSecundario::query()
            ->where('estado', 'Finalizado')
            ->where('tipo_paciente', operator: 'Critico')
            ->count();

        $totaltransporte = TrasladoSecundario::query()
            ->where('estado', 'Finalizado')
            ->where('asunto_traslado', 'Trasporte de Paciente')
            ->count();

        return [
            Stat::make('Traslado de Pacientes Criticos - Finalizados', $totalcriticos)
                ->iconColor('danger')
                ->progressBarColor('danger')
                ->iconBackgroundColor('danger')
                ->progress($totalcriticos)
                //  ->description('Traslados críticos pendientes de asignación de recurso')
                ->icon('healthicons-o-rural-post'),
            Stat::make('Traslado de Pacientes Estables - Finalizados', $totalestables)
                //  ->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->iconColor('warning')
                ->progressBarColor('warning')
                ->iconBackgroundColor('warning')
                ->icon('healthicons-o-rural-post')
                ->progress($totalestables),
            Stat::make('Total de Transporte de pacientes - Finalizados', $totaltransporte)
                //   ->description('Traslados En curso')
                ->iconColor('primary')
                ->iconBackgroundColor('primary')
                ->progressBarColor('primary')
                ->progress($totaltransporte)
                ->icon('healthicons-o-ambulance'),
            Stat::make('Total de Traslados Finalizados', $total)
                ->iconBackgroundColor('success')
                ->iconColor('success')
                ->progressBarColor('success')
                ->progress($total)
                //   ->description('Cantidad de Traslados Registrados el día: ' . Carbon::now()->format('d/m/Y'))
                ->icon('healthicons-o-hospitalized'),
        ];
    }
}

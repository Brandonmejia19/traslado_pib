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
              //  ->description('Traslados críticos pendientes de asignación de recurso')
                ->chartColor('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Traslado de Pacientes Estables - Finalizados', $totalestables)
              //  ->description('Traslados Programados/Pendientes de asignacion de recurso')
                ->chartColor('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-rural-post'),
            Stat::make('Total de Transporte de pacientes - Finalizados', $totaltransporte)
             //   ->description('Traslados En curso')
                ->chartColor('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('healthicons-o-ambulance'),
            Stat::make('Total de Traslados Finalizados', $total)
             //   ->description('Cantidad de Traslados Registrados el día: ' . Carbon::now()->format('d/m/Y'))
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('success')
                ->descriptionIcon('healthicons-o-hospitalized'),
        ];
    }
}

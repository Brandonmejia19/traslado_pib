<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\TrasladoSecundario;

class EstadisticasDia extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        Carbon::setLocale('es');
        $startDate = Carbon::now()->subDay();
        $endDate = Carbon::now();
        $venticuatrostart = Carbon::now()->subDay();

        $totaldia = TrasladoSecundario::query()

            ->where('estado', 'En curso')
            ->count();

        $totalactivos = TrasladoSecundario::query()

            ->when($startDate, fn($query) => $query->whereDate('updated_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('updated_at', '<=', $endDate))
            ->where('estado', operator: 'Finalizado')
            ->count();

        $totalpendientes = TrasladoSecundario::query()

            ->when($startDate, fn($query) => $query->whereDate('updated_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('updated_at', '<=', $endDate))
            ->where('estado', operator: 'Finalizado')
            ->where('tipo_paciente', 'Estable')
            ->count();

        $totalcriticospendientes = TrasladoSecundario::query()

            ->when($startDate, fn($query) => $query->whereDate('updated_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('updated_at', '<=', $endDate))
            ->where('estado', operator: 'Finalizado')
            ->where('tipo_paciente', 'Critico')
            ->count();

        return [
            Stat::make('Traslados críticos Finalizados: ', $totalcriticospendientes)
                ->icon('healthicons-o-bandaged')->progress($totalcriticospendientes)
                ->description('Traslados críticos Finalizados en las ultimas 24 horas')
                ->descriptionIcon('healthicons-o-bandaged')
                ->iconColor('danger')
                ->iconBackgroundColor('danger')
                ->progressBarColor('danger')
                ->iconPosition('start')
                ->chartColor('danger'),
            Stat::make('Traslados estables Finalizados: ', $totalpendientes)
                ->icon('healthicons-o-happy')
                ->iconBackgroundColor('success')
                ->description('Traslados de Pacientes Estables Finalizados en las ultimas 24 horas')
                ->descriptionIcon('healthicons-o-happy')
                ->progress($totalpendientes)
                ->iconColor('success')
                ->progressBarColor('success')
                ->iconPosition('start')
                ->chartColor('success'),
            Stat::make('Traslados Finalizados', $totalactivos)->progress($totalactivos)
                ->progressBarColor('primary')
                ->description('Traslados Finalizados en las ultimas 24 horas')
                ->descriptionIcon('heroicon-o-archive-box-x-mark')
                ->iconBackgroundColor('primary')
                ->iconPosition('start')
                ->icon('heroicon-o-archive-box-x-mark')
                ->iconColor('primary')
                ->chartColor('primary'),
            Stat::make('Traslados en Curso', $totaldia)
                ->progress($totaldia)->iconColor('warning')
                ->iconBackgroundColor('warning')
                ->iconPosition('start')
                ->description('Cantidad de Traslados en curso')
                ->progressBarColor('warning')
                ->chartColor('warning')
                ->descriptionIcon('healthicons-o-hospitalized', 'after')
                ->icon('healthicons-o-ambulance'),
        ];
    }
}

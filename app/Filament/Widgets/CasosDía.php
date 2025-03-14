<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TrasladoSecundario;

class CasosDía extends AdvancedChartWidget
{
    protected static string $color = 'primary';
    protected static ?string $icon = 'healthicons-o-clinical-fe';
    protected static ?string $iconColor = 'primary';
    protected static ?string $iconBackgroundColor = 'primary';
    protected static ?string $label = 'Traslados Secundarios';
    protected static ?string $heading = 'Casos en las últimas 24H';
    protected static ?string $badge = 'Total';
    protected static ?string $badgeColor = 'success';
    protected static ?string $badgeIcon = 'heroicon-o-check-circle';
    protected static ?string $badgeIconPosition = 'after';
    protected static ?string $badgeSize = 'xs';
    protected static bool $isLazy = false;


    protected function getData(): array
    {
        $now = Carbon::now();
        $start = $now->copy()->subDay();

        // Consulta: Agrupar por hora y contar los casos
        $data = TrasladoSecundario::query()
            ->whereBetween('created_at', [$start, $now])
            ->select(
                DB::raw("to_char(created_at, 'HH24') || ':00' as hour"),
                DB::raw('count(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            $total = TrasladoSecundario::whereBetween('created_at', [$start, $now])
            ->count();

        $labels = [];
        $dataset = [];
        $current = $start->copy();

        // Generar etiquetas para cada hora y asignar el valor contado
        for ($i = 0; $i < 24; $i++) {
            $hourLabel = $current->format('H:00');
            $labels[] = $hourLabel;
            $record = $data->firstWhere('hour', $hourLabel);
            $dataset[] = $record ? $record->count : 0;
            $current->addHour();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Traslados Secundarios en las últimas 24H',
                    'data' => $dataset,
                    'borderColor' => '#1B3C71',
                    'backgroundColor' => '#ebf2fc',
                    'fill' => 'false',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    public function getColumnSpan(): int|string|array
    {
        return 2;
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TrasladoSecundario;
use EightyNine\FilamentAdvancedWidget\AdvancedBarChartWidget;

class GestoresControl extends AdvancedBarChartWidget
{
    protected static string $color = 'danger';
    protected static ?string $icon = 'healthicons-o-ambulance';
    protected static ?string $iconColor = 'danger';
    protected static ?string $iconBackgroundColor = 'danger';
    protected static ?string $label = 'Traslados Secundarios';
    protected static ?string $heading = 'Control Gestores';
    protected static ?string $badge = 'Total';
    protected static ?string $badgeColor = 'danger';
    protected static ?string $badgeIcon = 'heroicon-o-check-circle';
    protected static ?string $badgeIconPosition = 'after';
    protected static ?string $badgeSize = 'xs';

    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $now = Carbon::now();
        $start = $now->copy()->subDay();

        $query = TrasladoSecundario::query()
            ->select(DB::raw("COALESCE(gestor_nombre, 'Sin asignar') as gestor_nombre"), DB::raw('count(*) as total'))
            ->where('estado', 'Finalizado' || 'En curso')
           // ->where('estado', 'En curso')
            ->where(function ($q) use ($start, $now) {
                $q->whereBetween('created_at', [$start, $now])
                    ->orWhereBetween('updated_at', [$start, $now]);
            })
            ->groupBy('gestor_nombre')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($query as $record) {
            $labels[] = $record->gestor_nombre; // Ya está asegurado que no es NULL
            $data[] = $record->total;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Recursos Asignados',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.6)',   // rojo pastel
                        'rgba(54, 162, 235, 0.6)',   // azul pastel
                        'rgba(255, 206, 86, 0.6)',   // amarillo pastel
                        'rgba(75, 192, 192, 0.6)',   // verde pastel
                        'rgba(153, 102, 255, 0.6)',  // morado pastel
                        'rgba(255, 159, 64, 0.6)',   // naranja pastel
                        'rgba(199, 199, 199, 0.6)',  // gris claro
                        'rgba(255, 99, 71, 0.6)',    // tomate pastel
                        'rgba(135, 206, 250, 0.6)',  // celeste pastel
                        'rgba(255, 182, 193, 0.6)',  // rosa pastel
                        'rgba(144, 238, 144, 0.6)',  // verde claro pastel
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(255, 99, 71, 1)',
                        'rgba(135, 206, 250, 1)',
                        'rgba(255, 182, 193, 1)',
                        'rgba(144, 238, 144, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }


    protected function getType(): string
    {
        // Puedes cambiar 'bar' por 'pie' o 'line' según tus preferencias
        return 'doughnut';
    }
    public function getColumnSpan(): int|string|array
    {
        return '2';
    }
}

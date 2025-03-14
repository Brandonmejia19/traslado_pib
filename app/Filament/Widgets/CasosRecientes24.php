<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\TrasladoSecundario;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Enums\Alignment;

class CasosRecientes24 extends BaseWidget
{
    protected static bool $isLazy = false;


    public function table(Table $table): Table
    {
        return $table
        ->heading('Ultimos Traslados Creados')
        ->description('Listado de los últimos traslados creados')
            ->paginated(false)
            ->query(
                TrasladoSecundario::query()
                    ->limit(5)
                    ->orderBy('created_at', 'desc')
                // Limita a 5 registros
            )
            ->columns([
                Tables\Columns\TextColumn::make('tipo_paciente')
                    ->label('Estado P.')
                    ->default('---')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $tipo_paciente = $record->tipo_paciente;
                        if ($tipo_paciente === "Critico") {
                            return 'danger';
                        }
                        if ($tipo_paciente === "Estable") {
                            return 'success';
                        }
                    })
                    ->icon(function ($record) {
                        $tipo_paciente = $record->tipo_paciente;
                        if ($tipo_paciente === "Critico") {
                            return 'healthicons-o-bandaged';
                        }
                        if ($tipo_paciente === "Estable") {
                            return 'healthicons-o-happy';
                        }
                    }),
                Tables\Columns\TextColumn::make('correlativo')
                    ->default('---')
                    ->sortable()
                    ->badge()->alignment(Alignment::Center)
                    ->label('Correlativo'),
                Tables\Columns\TextColumn::make('diagnostico_paciente')
                    ->icon('healthicons-o-clinical-f')
                    ->default('---')
                    ->sortable()
                    ->limit(15)
                    ->alignment(Alignment::Center)
                    ->label('Diagnóstico'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()->alignment(Alignment::Center)
                    ->label('Fecha de Creación'),
            ]);

    }
    public function getColumnSpan(): int|string|array
    {
        return 2;
    }
}

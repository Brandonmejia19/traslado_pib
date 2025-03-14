<?php

namespace App\Filament\Exports;

use App\Models\TrasladoSecundarioHistorico;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TrasladoSecundarioHistoricoExporter extends Exporter
{
    protected static ?string $model = TrasladoSecundarioHistorico::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user_id'),
            ExportColumn::make('fecha'),
            ExportColumn::make('hora'),
            ExportColumn::make('ambulancia'),
            ExportColumn::make('tipo_ambulancia'),
            ExportColumn::make('operador_nombre'),
            ExportColumn::make('operador_numero'),
            ExportColumn::make('tipoTraslado.id'),
            ExportColumn::make('tipo_traslado'),
            ExportColumn::make('nombre_medico_solicitante'),
            ExportColumn::make('telefono_medico_solicitante'),
            ExportColumn::make('destino_institucion'),
            ExportColumn::make('destino_traslado'),
            ExportColumn::make('origen_traslado'),
            ExportColumn::make('origen_institucion'),
            ExportColumn::make('nombre_medico_recibe'),
            ExportColumn::make('telefono_medico_recibe'),
            ExportColumn::make('nombres_paciente'),
            ExportColumn::make('apellidos_paciente'),
            ExportColumn::make('edad_paciente'),
            ExportColumn::make('sexo_paciente'),
            ExportColumn::make('registro_expediente'),
            ExportColumn::make('diagnostico_paciente'),
            ExportColumn::make('tipo_paciente'),
            ExportColumn::make('tipo_critico'),
            ExportColumn::make('antecendetes_clinicos'),
            ExportColumn::make('formula_obstetrica'),
            ExportColumn::make('edad_gestacional'),
            ExportColumn::make('fecha_probable_parto'),
            ExportColumn::make('signos_vitales'),
            ExportColumn::make('dilatacion'),
            ExportColumn::make('borramiento'),
            ExportColumn::make('hora_obstetrica'),
            ExportColumn::make('fcf'),
            ExportColumn::make('membranas_integras'),
            ExportColumn::make('mov_fetales'),
            ExportColumn::make('trabajo_parto'),
            ExportColumn::make('contracciones'),
            ExportColumn::make('frecuencia'),
            ExportColumn::make('datos_rn_neonato'),
            ExportColumn::make('requerimientos_oxigenoterapia'),
            ExportColumn::make('asistencia_ventilatoria'),
            ExportColumn::make('bombas_infusion'),
            ExportColumn::make('servicio_origen'),
            ExportColumn::make('numero_cama_origen'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('prioridad'),
            ExportColumn::make('color'),
            ExportColumn::make('componente_edad'),
            ExportColumn::make('programado'),
            ExportColumn::make('posicion'),
            ExportColumn::make('fio2'),
            ExportColumn::make('modo_ventilacion'),
            ExportColumn::make('vt'),
            ExportColumn::make('volmin'),
            ExportColumn::make('relacion_ie'),
            ExportColumn::make('fr'),
            ExportColumn::make('peep'),
            ExportColumn::make('trigger'),
            ExportColumn::make('fecha_traslado'),
            ExportColumn::make('asunto_traslado'),
            ExportColumn::make('justificacion_cierre'),
            ExportColumn::make('razon_cierre'),
            ExportColumn::make('usuario_cierre'),
            ExportColumn::make('estado'),
            ExportColumn::make('jvpe_medico_recibe'),
            ExportColumn::make('numero_llamada'),
            ExportColumn::make('correlativo'),
            ExportColumn::make('servicio_destino'),
            ExportColumn::make('numero_cama_destino'),
            ExportColumn::make('jvpe_medico_entrega'),
            ExportColumn::make('notas_seguimiento'),
            ExportColumn::make('gestor_numero'),
            ExportColumn::make('gestor_nombre'),
            ExportColumn::make('observaciones_origen'),
            ExportColumn::make('observaciones_destino'),
            ExportColumn::make('tipo_unidad_sugerida'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your traslado secundario historico export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}

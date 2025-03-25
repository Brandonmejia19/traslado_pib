<?php

namespace App\Filament\Exports;

use App\Models\TrasladoSecundario;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TrasladoSecundarioExporter extends Exporter
{
    protected static ?string $model = TrasladoSecundario::class;

    public static function getColumns(): array
    {
        return [
            //  ExportColumn::make('id')
            //      ->label('ID'),
            //   ExportColumn::make('user_id'),

            ExportColumn::make('created_at')->label('Fecha de Creación'),
            ExportColumn::make('correlativo')->label('Correlativo'),
            ExportColumn::make('numero_llamada')->label('Número de Llamada'),
            ExportColumn::make('prioridad')->label('Prioridad'),
            ExportColumn::make('operador_nombre')->label('Operador Nombre'),
            ExportColumn::make('operador_numero')->label(label: 'PP Operador'),
            ExportColumn::make('asunto_traslado')->label('Asunto Traslado'),
            ExportColumn::make('tipo_traslado')->label('Tipo Traslado'),

            ExportColumn::make('nombre_medico_solicitante'),
            ExportColumn::make('telefono_medico_solicitante'),
            ExportColumn::make('jvpe_medico_entrega'),
            ExportColumn::make('origen_traslado_nombre')->label('Origen Traslado'),
            ExportColumn::make('origen_institucion')->label('Origen Institución'),
            ExportColumn::make('observaciones_origen'),
            ExportColumn::make('servicio_origen')->label('Servicio Origen'),
            ExportColumn::make('numero_cama_origen')->label('Número Cama Origen'),

            ExportColumn::make('nombre_medico_recibe'),
            ExportColumn::make('telefono_medico_recibe'),
            ExportColumn::make('jvpe_medico_recibe'),
            ExportColumn::make('destino_traslado_nombre')->label('Destino Traslado'),
            ExportColumn::make('destino_institucion')->label('Destino Institución'),
            ExportColumn::make('observaciones_destino')->label('Observaciones Destino'),
            ExportColumn::make('servicio_destino')->label('Servicio Destino'),
            ExportColumn::make('numero_cama_destino')->label('Número Cama Destino'),
            ////////////////////////

            ExportColumn::make('nombres_paciente'),
            ExportColumn::make('apellidos_paciente'),
            ExportColumn::make('edad_paciente'),
            ExportColumn::make('componente_edad')->label('Complememto'),
            ExportColumn::make('sexo_paciente')->label('Sexo'),
            ExportColumn::make('registro_expediente')->label('Registro Expediente'),
            ExportColumn::make('diagnostico_paciente')->label('Diagnóstico'),
            ExportColumn::make('tipo_paciente')->label('Tipo Paciente'),
            ExportColumn::make('tipo_critico')->label('Tipo de Critico'),
            ExportColumn::make('antecendetes_clinicos'),


            ExportColumn::make('ambulancia')->label('Recurso Asignado'),
            ExportColumn::make('tipo_unidad_sugerida')->label('Tipo Unidad Sugerida'),
            ExportColumn::make('tipo_ambulancia')->label('Tipo Unidad'),
            ExportColumn::make('programado'),
            ExportColumn::make('fecha_traslado'),
            ExportColumn::make('gestor_numero')->label('PP Gestor'),
            ExportColumn::make('gestor_nombre')->label('Gestor Asignado'),



            ExportColumn::make('formula_obstetrica')->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state),
            ExportColumn::make('edad_gestacional'),
            ExportColumn::make('fecha_probable_parto'),
            ExportColumn::make('signos_vitales')->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state),
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

            //     ExportColumn::make('updated_at'),
            ExportColumn::make('fio2'),
            ExportColumn::make('modo_ventilacion'),
            ExportColumn::make('vt'),
            ExportColumn::make('volmin'),
            ExportColumn::make('relacion_ie'),
            ExportColumn::make('fr'),
            ExportColumn::make('peep'),
            ExportColumn::make('trigger'),
            ExportColumn::make('justificacion_cierre'),
            ExportColumn::make('razon_cierre'),
            ExportColumn::make('usuario_cierre')->label('Médico de Cierre'),
            ExportColumn::make('notas_seguimiento')->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state),
            ExportColumn::make('estado'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Listado de Traslados Secundadrios exportado correctamente, ' . number_format($export->successful_rows) . ' ' . str('filas')->plural($export->successful_rows) . ' incluidas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('filas')->plural($failedRowsCount) . ' Fallidas';
        }

        return $body;
    }
}

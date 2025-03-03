<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TipoTraslado;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TrasladoSecundario extends Model
{

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asunto_traslado',
                'fecha',
                'hora',
                'ambulancia',
                'tipo_ambulancia',
                'operador_nombre',
                'operador_numero',
                'numero_llamada',
                'tipo_traslado_id',
                'tipo_traslado',

                'nombre_medico_solicitante',
                'telefono_medico_solicitante',
                'jvpe_medico_entrega',

                'destino_traslado',
                'destino_institucion',
                'origen_traslado',
                'origen_institucion',

                'nombre_medico_recibe',
                'telefono_medico_recibe',

                'nombres_paciente',
                'apellidos_paciente',
                'edad_paciente',
                'sexo_paciente',
                'registro_expediente',
                'diagnostico_paciente',

                'tipo_paciente',
                'tipo_critico',

                'antecendetes_clinicos',

                'formula_obstetrica',//json
                'edad_gestacional',
                'fecha_probable_parto',
                'signos_vitales',//json

                'jvpe_medico_recibe',

                'dilatacion',
                'borramiento',
                'hora_obstetrica',
                'FCF',
                'membranas_integras',
                'mov_fetales',
                'trabajo_parto',
                'contracciones',
                'frecuencia',

                'datos_rn_neonato',
                'requerimientos_oxigenoterapia',

                'asistencia_ventilatoria',
                'bombas_infusion',
                'servicio_origen',
                'numero_cama_orige',
                'servicio_destino',
                'numero_cama_destino',

                ///NUEVOS DATOS
                'prioridad',
                'color',
                'componente_edad',
                'programado',
                'fecha_traslado',
                'posicion',
                'fio2',
                'VM',
                'modo_ventilacion',
                'vt',
                'volmin',
                'relacion_ie',
                'FR',
                'PEEP',
                'TRIGGER',

                'justificacion_cierre',
                'razon_cierre',
                'usuario_cierre',
                'notas_seguimiento',
                'estado',
                'correlativo',
                'formula_obstetrica' => 'array',
                'signos_vitales' => 'array',

                'tipo_unidad_sugerida',
            ]);
    }
    use LogsActivity;
    protected $fillable = [
        'asunto_traslado',
        'fecha',
        'hora',
        'ambulancia',
        'tipo_ambulancia',
        'operador_nombre',
        'operador_numero',
        'numero_llamada',
        'tipo_traslado_id',
        'tipo_traslado',

        'nombre_medico_solicitante',
        'telefono_medico_solicitante',

        'destino_traslado',
        'destino_institucion',
        'origen_traslado',
        'origen_institucion',

        'nombre_medico_recibe',
        'telefono_medico_recibe',

        'nombres_paciente',
        'apellidos_paciente',
        'edad_paciente',
        'sexo_paciente',
        'registro_expediente',
        'diagnostico_paciente',

        'tipo_paciente',
        'tipo_critico',

        'antecendetes_clinicos',

        'formula_obstetrica',//json
        'edad_gestacional',
        'fecha_probable_parto',
        'signos_vitales',//json

        'jvpe_medico_recibe',

        'dilatacion',
        'borramiento',
        'hora_obstetrica',
        'FCF',
        'membranas_integras',
        'mov_fetales',
        'trabajo_parto',
        'contracciones',
        'frecuencia',

        'datos_rn_neonato',
        'requerimientos_oxigenoterapia',

        'asistencia_ventilatoria',
        'bombas_infusion',
        'servicio',
        'numero_cama',

        ///NUEVOS DATOS
        'prioridad',
        'color',
        'componente_edad',
        'programado',
        'fecha_traslado',
        'posicion',
        'fio2',
        'VM',
        'modo_ventilacion',
        'vt',
        'volmin',
        'relacion_ie',
        'FR',
        'PEEP',
        'TRIGGER',

        'justificacion_cierre',
        'razon_cierre',
        'usuario_cierre',
        'notas_seguimiento',
        'estado',
        'correlativo',
        'user_id',
    ];
    protected $casts = [
        'formula_obstetrica' => 'array',
        'signos_vitales' => 'array',
    ];
    protected static function boot()
    {
        parent::boot();
        static::created(function ($llamada) {
            $fecha = now()->format('dmY');//His
            $usuario = auth()->user()->id;
            $tipoCasoInicial = strtoupper(substr('Traslado', 0, 2));
            $contador = self::whereDate('created_at', now()->format('Y-m-d'))->count();
            $numeroSecuencial = str_pad($contador, 4, '0', STR_PAD_LEFT);
            $correlativo = "{$fecha}{$tipoCasoInicial}{$numeroSecuencial}";
            $llamada->update([
                'user_id' => $usuario,
                'estado' => 'En curso',
                'correlativo' => $correlativo,
            ]);

        });

    }
    public function tipotraslado(): BelongsTo
    {
        return $this->belongsTo(TipoTraslado::class);
    }
}

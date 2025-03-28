<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TipoTraslado;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable;

class TrasladoSecundarioPropios extends Model implements Auditable
{
    use LogsActivity, HasFilamentComments,\OwenIt\Auditing\Auditable;

    protected $table = 'traslado_secundarios';
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asunto_traslado',
                'doctor_numero',
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
                'servicio_origen',
                'numero_cama_origen',
                'servicio_destino',
                'numero_cama_destino',
                'observaciones_destino',
                'observaciones_origen',
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
                'jvpe_medico_entrega',

                'dilatacion',
                'borramiento',
                'hora_obstetrica',
                'fcf',
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
                //    'numero_cama',

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
                'fr',
                'peep',
                'tipo_unidad_sugerida',
                'trigger',

                'justificacion_cierre',
                'razon_cierre',
                'usuario_cierre',
                'notas_seguimiento',
                'estado',
                'correlativo',
                'notas_seguimiento',
                'user_id',
                'gestor_numero',
                'gestor_nombre',
                'observaciones_origen',
                'observaciones_destino',
                'razon_fallecido',

                //ID FK
                'ambulancia_id',
                'hospital_listado_origen_id',
                'isslistados_origen_id',
                'privado_listados_origen_id',
                'unidad_listados_origen_id',
                'hospital_listado_destino_id',
                'isslistados_destino_id',
                'privado_listados_destino_id',
                'unidad_listados_destinos_id',
                'destino_traslado_nombre',
                'origen_traslado_nombre',
            ]);
    }
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
        'doctor_numero',

        'nombre_medico_solicitante',
        'telefono_medico_solicitante',

        'destino_traslado',
        'destino_institucion',
        'origen_traslado',
        'origen_institucion',
        'servicio_origen',
        'numero_cama_origen',
        'servicio_destino',
        'numero_cama_destino',
        'observaciones_destino',
        'observaciones_origen',
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
        'jvpe_medico_entrega',

        'dilatacion',
        'borramiento',
        'hora_obstetrica',
        'fcf',
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
        //    'numero_cama',

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
        'fr',
        'peep',
        'tipo_unidad_sugerida',
        'trigger',

        'justificacion_cierre',
        'razon_cierre',
        'usuario_cierre',
        'notas_seguimiento',
        'estado',
        'correlativo',
        'notas_seguimiento',
        'user_id',
        'gestor_numero',
        'gestor_nombre',
        'observaciones_origen',
        'observaciones_destino',
        'razon_fallecido',


        //ID FK
        'ambulancia_id',
        'hospital_listado_origen_id',
        'isslistados_origen_id',
        'privado_listados_origen_id',
        'unidad_listados_origen_id',
        'hospital_listado_destino_id',
        'isslistados_destino_id',
        'privado_listados_destino_id',
        'unidad_listados_destinos_id',
        'destino_traslado_nombre',
        'origen_traslado_nombre',
    ];
    protected $casts = [
        'formula_obstetrica' => 'array',
        'signos_vitales' => 'array',
        'notas_seguimiento' => 'array',
    ];
    protected static function boot()
    {
        parent::boot();
        static::created(function ($llamada) {
            $fecha = now()->format('dmY');//His
            $usuario = auth()->user()->id;
            $asunto = $llamada->asunto_traslado;

            $tipoCasoInicial = match ($asunto) {
                'Transporte de Paciente' => 'TP',
                'Traslado de Paciente' => 'TR',
                default => 'ND', // 'ND' en caso de que no coincida con ninguno
            };
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
    public function ambulancia(): BelongsTo
    {
        return $this->belongsTo(Ambulancias::class);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traslado_secundarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('ambulancia')->nullable();
            $table->string('tipo_ambulancia')->nullable();
            $table->string('operador_nombre')->nullable();
            $table->string('operador_numero')->nullable();

            // Llave foránea de ejemplo, ajustar si corresponde
            $table->unsignedBigInteger('tipo_traslado_id')->nullable();
            $table->string('tipo_traslado')->nullable();

            $table->string('nombre_medico_solicitante')->nullable();
            $table->string('telefono_medico_solicitante')->nullable();

            $table->string('destino_institucion')->nullable();
            $table->string('destino_traslado')->nullable();
            $table->string('origen_traslado')->nullable();
            $table->string('origen_institucion')->nullable();

            $table->string('nombre_medico_recibe')->nullable();
            $table->string('telefono_medico_recibe')->nullable();

            $table->string('nombres_paciente')->nullable();
            $table->string('apellidos_paciente')->nullable();
            $table->integer('edad_paciente')->nullable();
            $table->string('sexo_paciente')->nullable();
            $table->string('registro_expediente')->nullable();
            $table->text('diagnostico_paciente')->nullable();

            $table->string('tipo_paciente')->nullable();
            $table->string('tipo_critico')->nullable();

            $table->text('antecendetes_clinicos')->nullable();

            // Campos JSON
            $table->json('formula_obstetrica')->nullable();
            $table->integer('edad_gestacional')->nullable();
            $table->date('fecha_probable_parto')->nullable();
            $table->json('signos_vitales')->nullable();

            $table->string('dilatacion')->nullable();
            $table->string('borramiento')->nullable();
            $table->time('hora_obstetrica')->nullable();
            $table->string('FCF')->nullable();
            $table->string('membranas_integras')->nullable();
            $table->string('mov_fetales')->nullable();
            $table->string('trabajo_parto')->nullable();
            $table->string('contracciones')->nullable();
            $table->string('frecuencia')->nullable();

            $table->text('datos_rn_neonato')->nullable();
            $table->text('requerimientos_oxigenoterapia')->nullable();

            // 'asistencia_ventilatoria' aparece dos veces; conservamos solo una
            $table->text('asistencia_ventilatoria')->nullable();

            $table->string('bombas_infusion')->nullable();
            $table->string('servicio')->nullable();
            $table->string('numero_cama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslado_secundarios');
    }
};

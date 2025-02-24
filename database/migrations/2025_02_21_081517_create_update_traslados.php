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
        Schema::table('traslado_secundarios', function (Blueprint $table) {
            $table->char('prioridad')->nullable();
            $table->char('color')->nullable();
            $table->char('componente_edad')->nullable();
            $table->dateTime('programado')->nullable();
            $table->char('posicion')->nullable();
            $table->char('fio2')->nullable();
            $table->char('VM')->nullable();
            $table->char('modo_ventilacion')->nullable();
            $table->char('vt')->nullable();
            $table->char('volmin')->nullable();
            $table->char('relacion_ie')->nullable();
            $table->char('FR')->nullable();
            $table->char('PEEP')->nullable();
            $table->char('TRIGGER')->nullable();
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

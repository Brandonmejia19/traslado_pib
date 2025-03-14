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
            $table->char('gestor_numero')->nullable();
            $table->char('gestor_nombre')->nullable();
            $table->char('observaciones_origen')->nullable();
            $table->char('observaciones_destino')->nullable();

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

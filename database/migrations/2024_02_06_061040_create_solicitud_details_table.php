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
        Schema::create('solicitud_details', function (Blueprint $table) {
            $table->id();
            $table->enum('via_administracion', ['Central', 'Periférica']);
            $table->integer('tiempo_infusion_min');
            $table->decimal('sobrellenado_ml');
            $table->decimal('volumen_total');
            $table->enum('npt', ['RNPT', 'LACT', 'INF', 'ADOL', 'ADUL']);
            $table->string('nombre_medico');
            $table->string('cedula');
            $table->date('fecha_entrega');
            $table->time('hora_entrega');
            $table->string('observaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_details');
    }
};

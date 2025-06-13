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
        Schema::create('inspeccion_mezclas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->onDelete('cascade');
            $table->boolean('es_limpia');
            $table->boolean('es_libre');
            $table->date('fecha_inspeccion');
            $table->time('hora_inspeccion');
            $table->enum('tipo_contenedor', ['Frasco', 'Bolsa', 'Jeringa', 'Otro'])->nullable();
            $table->string('tipo_contenedor_otro');
            $table->boolean('esta_rotulado');
            $table->boolean('numero_lote');
            $table->boolean('medicamento');
            $table->boolean('dosis_volumen_total');
            $table->boolean('volumen_medicamento');
            $table->boolean('rubrica_preparador');
            $table->boolean('sello_seguridad');
            $table->boolean('presenta_grietas');
            $table->boolean('presenta_fugas');
            $table->boolean('esta_roto');
            $table->boolean('coloracion_apropiada');
            $table->boolean('contenido_homogeneo');
            $table->boolean('presenta_particulas');
            $table->boolean('presenta_turbidez');
            $table->boolean('volumen_correcto');
            $table->boolean('aprueba_contenido');
            $table->boolean('aprueba_contenedor');
            $table->decimal('dosis_volumen', 8, 2);
            $table->decimal('peso_mezcla', 8, 2);
            $table->boolean('mezcla_aprobada');
            $table->text('observaciones');
            $table->string('reviso_nombre');
            $table->string('aprobo_nombre');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccion_mezclas');
    }
};

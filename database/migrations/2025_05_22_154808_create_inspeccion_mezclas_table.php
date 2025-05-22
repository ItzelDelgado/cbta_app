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
            $table->boolean('es_limpia')->nullable();
            $table->boolean('es_libre')->nullable();
            $table->date('fecha_inspeccion')->nullable();
            $table->time('hora_inspeccion')->nullable();
            $table->enum('tipo_contenedor', ['Frasco', 'Bolsa', 'Jeringa', 'Otro'])->nullable();
            $table->string('tipo_contenedor_otro')->nullable();
            $table->boolean('esta_rotulado')->nullable();
            $table->boolean('numero_lote')->nullable();
            $table->boolean('medicamento')->nullable();
            $table->boolean('dosis_volumen_total')->nullable();
            $table->boolean('volumen_medicamento')->nullable();
            $table->boolean('rubrica_preparador')->nullable();
            $table->boolean('sello_seguridad')->nullable();
            $table->boolean('presenta_grietas')->nullable();
            $table->boolean('presenta_fugas')->nullable();
            $table->boolean('esta_roto')->nullable();
            $table->boolean('coloracion_apropiada')->nullable();
            $table->boolean('contenido_homogeneo')->nullable();
            $table->boolean('presenta_particulas')->nullable();
            $table->boolean('presenta_turbidez')->nullable();
            $table->boolean('volumen_correcto')->nullable();
            $table->boolean('aprueba_contenido')->nullable();
            $table->boolean('aprueba_contenedor')->nullable();
            $table->decimal('dosis_volumen', 8, 2)->nullable();
            $table->decimal('peso_mezcla', 8, 2)->nullable();
            $table->boolean('mezcla_aprobada')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('reviso_nombre')->nullable();
            $table->string('aprobo_nombre')->nullable();
            $table->timestamp('created_at')->useCurrent();
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

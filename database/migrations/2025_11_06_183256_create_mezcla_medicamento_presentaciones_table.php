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
        Schema::create('mezcla_medicamento_presentaciones', function (Blueprint $table) {
            $table->id();

            // Cabecera del renglón en la mezcla (ya existe en tu sistema)
            $table->foreignId('mezcla_medicamento_id')
                ->constrained('mezcla_medicamentos')
                ->cascadeOnDelete();

            // Batch/lote seleccionado al momento de preparar
            $table->foreignId('medicine_batch_id')
                ->constrained('medicine_batches')
                ->restrictOnDelete(); // no permitir borrar un lote ya usado

            // Cuántos frascos de esa presentación se usaron (sin stock real)
            $table->integer('unidades_usadas')->default(1);

            // SNAPSHOT inmutable para documentos: lo que se usó en ese momento
            $table->string('lote_usado', 100);
            $table->date('caducidad_usada');
            $table->decimal('precio_frasco_snapshot', 12, 4)->nullable();
            $table->decimal('subtotal', 12, 4)->nullable();

            $table->timestamps();

            // Búsquedas
            $table->index(['mezcla_medicamento_id']);
            $table->index(['medicine_batch_id']);
            $table->index(['lote_usado']);
            $table->index(['caducidad_usada']);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezcla_medicamento_presentaciones');
    }
};

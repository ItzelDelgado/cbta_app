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
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();

            // FK a la presentación
            $table->foreignId('medicine_presentation_id')
                ->constrained('medicine_presentations')
                ->cascadeOnDelete();

            // Lote/caducidad vigentes (sin manejar cantidades)
            $table->string('lote', 100);
            $table->date('caducidad');

            // Marcador de lote vigente por defecto para esa presentación
            $table->boolean('is_current')->default(true);

            $table->timestamps();

            // Evitar duplicar mismo lote en la misma presentación
            $table->unique(['medicine_presentation_id', 'lote'], 'uq_batch_per_presentation');

            // Búsquedas
            $table->index(['medicine_presentation_id', 'is_current']);
            $table->index(['caducidad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};

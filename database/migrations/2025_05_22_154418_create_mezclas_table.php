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
        Schema::create('mezclas', function (Blueprint $table) {
               $table->id();
                $table->foreignId('solicitud_id')->constrained('solicitud_oncos')->onDelete('cascade');
                $table->string('nombre_mezcla')->nullable();
                $table->enum('estado', ['pendiente', 'preparado', 'terminado', 'entregado'])->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->string('remision', 50)->nullable();
                $table->string('lote', 50)->nullable();
                $table->decimal('volumen_dilucion', 8, 2)->nullable();
                $table->string('tiempo_infusion', 100)->nullable();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezclas');
    }
};

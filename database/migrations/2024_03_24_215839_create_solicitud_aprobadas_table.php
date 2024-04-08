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
        Schema::create('solicitud_aprobadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained();
            $table->string('lote')->nullable();
            $table->dateTime('fecha_hora_preparacion')->nullable();
            $table->dateTime('fecha_hora_limite_uso')->nullable();
            $table->dateTime('fecha_hora_envio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_aprobadas');
    }
};

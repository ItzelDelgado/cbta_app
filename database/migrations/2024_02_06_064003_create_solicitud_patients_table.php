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
        Schema::create('solicitud_patients', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_paciente');
            $table->string('servicio');
            $table->string('cama');
            $table->string('piso');
            $table->string('registro');
            $table->string('diagnostico');
            $table->date('edad');
            $table->decimal('peso');
            $table->enum('sexo', ['Femenino', 'Masculino']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_patients');
    }
};

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
        Schema::create('solicitud_oncos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('servicio')->nullable();
            $table->string('nombre_paciente')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->integer('edad')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->string('cama')->nullable();
            $table->string('registro_paciente')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->text('diagnostico')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->time('horario_entrega')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('nombre_medico')->nullable();
            $table->string('cedula_medico', 50)->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_oncos');
    }
};

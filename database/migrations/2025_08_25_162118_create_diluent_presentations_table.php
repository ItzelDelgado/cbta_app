<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diluent_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diluent_id')->constrained('diluents')->onDelete('cascade');

            // Campos explícitos solicitados 👇
            $table->string('presentacion');                      // Ej: "Bolsa 500 mL"
            $table->decimal('volume_ml', 10, 2);                 // Ej: 500.00
            $table->string('denominacion_comercial')->nullable();// Ej: "NaCl 0.9% B. Braun"

            // Lote y caducidad a nivel de presentación
            $table->string('lote', 100)->nullable();
            $table->date('caducidad')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Evita duplicados por diluyente+presentacion
            $table->unique(['diluent_id', 'presentacion']);

            // Índices auxiliares
            $table->index('caducidad');
            $table->index('lote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diluent_presentations');
    }
};

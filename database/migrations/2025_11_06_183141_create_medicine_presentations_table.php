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
        Schema::create('medicine_presentations', function (Blueprint $table) {
            $table->id();
            // FK al catálogo de medicamentos (tipo: Paracetamol, etc.)
            $table->foreignId('catalog_id')
                ->constrained('medicines_catalog')
                ->cascadeOnDelete();

            // Datos de la presentación
            $table->string('presentacion', 255);         // ej. "frasco 500 mg", "vial 300 mg"
            $table->decimal('contenido_valor', 10, 2);   // ej. 500, 300
            $table->enum('contenido_unidad', ['mg', 'g', 'ml', 'UI'])->default('mg');
            $table->string('marca', 255)->nullable();

            // Disponibilidad simulada (sin inventario real)
            $table->boolean('is_available')->default(true);
            $table->integer('virtual_stock')->nullable(); // opcional: límite blando (si no lo usarás, puedes quitarlo)

            $table->timestamps();

            // Índices útiles
            $table->index(['catalog_id']);
            $table->index(['is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_presentations');
    }
};

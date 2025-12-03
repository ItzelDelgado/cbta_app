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
        Schema::create('mezcla_medicamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->onDelete('cascade');
            $table->foreignId('medicamento_id')->nullable()->constrained('medicine_oncos')->onDelete('set null');
            $table->string('nombre_medicamento')->nullable();
            $table->decimal('dosis', 8, 2)->nullable();
            $table->decimal('dosis_ml', 8, 2)->nullable();
            $table->foreignId('diluyente_id')->nullable()->constrained('diluents')->onDelete('set null');
            $table->foreignId('via_administracion_id')->nullable()->constrained('administration_routes')->onDelete('set null');
            $table->enum('charge_by', ['mg', 'frasco'])->default('mg');
            $table->decimal('precio_mg_snapshot', 12, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezcla_medicamentos');
    }
};

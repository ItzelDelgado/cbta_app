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
        Schema::create('medicines_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('denominacion');
            $table->string('denominacion_comercial');
            $table->string('presentacion');
            $table->boolean('state')->default(true); // true = activo, false = deshabilitado
            $table->decimal('cantidad_medicamento', 10, 2)->nullable(); // mg
            $table->decimal('volumen_diluyente', 10, 2)->nullable();
            $table->decimal('conc_min', 8, 2)->nullable();
            $table->decimal('conc_max', 8, 2)->nullable();
            $table->text('legend')->nullable();
            $table->string('lote', 100)->nullable();
            $table->date('caducidad')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines_catalog');
    }
};

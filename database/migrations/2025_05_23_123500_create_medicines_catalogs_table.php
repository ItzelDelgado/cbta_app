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
            $table->string('presentacion');
            $table->boolean('state')->default(true); // true = activo, false = deshabilitado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines_catalogs');
    }
};

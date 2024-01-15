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
        Schema::table('medicines', function (Blueprint $table) {
            $table->renameColumn('name','nombre_comercial');
            $table->string('denominacion_generica');
            $table->string('descripcion');
            $table->decimal('presentacion_ml');
            $table->boolean('is_active');
            $table->foreignId('category_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->renameColumn('nombre_comercial','name');
            $table->dropColumn('denominacion_generica');
            $table->dropColumn('descripcion');
            $table->dropColumn('presentacion_ml');
            $table->dropColumn('is_active');
            $table->dropColumn('category_id');
        });
    }
};

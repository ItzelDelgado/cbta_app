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
        Schema::create('medicine_diluents', function (Blueprint $table) {
            $table->foreignId('medicamento_id')->nullable()->constrained('medicine_oncologicos')->onDelete('set null');
            $table->foreignId('diluent_id')->constrained('diluents')->onDelete('cascade');
            $table->primary(['medicine_id', 'diluent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_diluents');
    }
};

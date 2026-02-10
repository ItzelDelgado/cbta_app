<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_lists', function (Blueprint $table) {
            $table->id();

            // ✅ 1 hospital = 1 lista (forzado por unique)
            $table->foreignId('hospital_id')
                ->constrained('hospitals')
                ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active_brands')->default(true);
            $table->enum('charge_by', ['mg', 'frasco'])->default('mg');
            $table->timestamps();

            // ✅ garantiza que no puedas crear 2 listas para el mismo hospital
            $table->unique('hospital_id', 'uq_medicine_lists_hospital');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_lists');
    }
};

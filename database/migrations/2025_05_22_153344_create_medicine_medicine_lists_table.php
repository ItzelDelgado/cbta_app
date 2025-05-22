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
        Schema::create('medicine_medicine_list', function (Blueprint $table) {
        $table->foreignId('medicine_list_id')->constrained('medicine_lists')->onDelete('cascade');
        $table->foreignId('medicine_id')->constrained('medicines')->onDelete('cascade');
        $table->primary(['medicine_list_id', 'medicine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_medicine_lists');
    }
};

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
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('diluent_id');
            $table->primary(['medicine_id', 'diluent_id']);
            $table->timestamps();
            $table->foreign('medicine_id')->references('id')->on('medicine_oncos')->onDelete('cascade');
            $table->foreign('diluent_id')->references('id')->on('diluents')->onDelete('cascade');

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

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
        Schema::create('medicine_routes', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('route_id');
            $table->primary(['medicine_id', 'route_id']);
            $table->foreign('medicine_id')->references('id')->on('medicine_oncos')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('administration_routes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_routes');
    }
};

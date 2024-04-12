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
        Schema::table('solicitud_aprobadas', function (Blueprint $table) {
            $table->dropColumn('fecha_hora_envio');
            $table->dateTime('fecha_hora_preparacion')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_aprobadas', function (Blueprint $table) {
            $table->dateTime('fecha_hora_envio')->nullable();
            $table->dateTime('fecha_hora_preparacion')->nullable()->change();
        });
    }
};

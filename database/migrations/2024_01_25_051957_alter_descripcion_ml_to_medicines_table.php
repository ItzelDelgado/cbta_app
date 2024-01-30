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
            $table->dropColumn('descripcion');
            $table->double('osmolaridad')
                ->after('category_id')
                ->nullable();
            $table->foreignId('input_id')->constrained();//Era la descripcion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('descripcion');
            $table->dropColumn('osmolaridad');
            $table->dropColumn('input_id');

        });
    }
};

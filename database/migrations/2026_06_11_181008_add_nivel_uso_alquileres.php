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
        Schema::table('trajes', function (Blueprint $table) {
            // Contador de veces que el traje (base) fue alquilado.
            // Usamos este campo para categorizar: 1ra, 2da, 3ra puesta.
            $table->unsignedInteger('nivel_uso_alquileres')->default(0)->after('pre_alquiler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajes', function (Blueprint $table) {
            $table->dropColumn('nivel_uso_alquileres');
        });
    }
};

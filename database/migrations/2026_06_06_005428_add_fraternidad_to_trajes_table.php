<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Creamos la columna como nullable temporalmente para que Postgres no salte
        Schema::table('trajes', function (Blueprint $table) {
            $table->string('fraternidad')->nullable()->after('nom_traje');
        });

        // Paso 2: Rellenamos los trajes antiguos que quedaron con NULL
        DB::table('trajes')->whereNull('fraternidad')->update([
            'fraternidad' => 'Folklore General'
        ]);

        // Paso 3: Ahora que todos tienen datos, cambiamos la columna a obligatoria (NOT NULL)
        Schema::table('trajes', function (Blueprint $table) {
            $table->string('fraternidad')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajes', function (Blueprint $table) {
            $table->dropColumn('fraternidad');
        });
    }
};
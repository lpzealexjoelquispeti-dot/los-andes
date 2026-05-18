<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario_unidades', function (Blueprint $table) {
            // text es ideal para notas largas, y nullable() evita que falle con registros antiguos
            $table->text('observaciones')->nullable()->after('estado_fisico');
        });
    }

    public function down(): void
    {
        Schema::table('inventario_unidades', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
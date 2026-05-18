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
            // Eliminamos la columna ya que las tallas ahora viven en 'inventario_unidades'
            $table->dropColumn('talla_traje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajes', function (Blueprint $table) {
            // Por si necesitas hacer un rollback en el futuro
            $table->string('talla_traje')->nullable();
        });
    }
};
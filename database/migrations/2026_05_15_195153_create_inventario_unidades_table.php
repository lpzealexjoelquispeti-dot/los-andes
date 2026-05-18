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
        // Tabla: inventario_unidades
            Schema::create('inventario_unidades', function (Blueprint $table) {
                $table->id('cod_unidad');
                $table->unsignedBigInteger('cod_traje_base');
                $table->string('nro_serie_interno')->unique(); // ID físico o código QR
                $table->enum('estado_fisico', ['Nuevo', 'Buen Estado', 'Desgastado', 'En Reparación'])->default('Nuevo');
                $table->boolean('disponibilidad')->default(true);
                
                $table->foreign('cod_traje_base')->references('cod_traje')->on('trajes')->onDelete('cascade');
                $table->softDeletes();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_unidades');
    }
};

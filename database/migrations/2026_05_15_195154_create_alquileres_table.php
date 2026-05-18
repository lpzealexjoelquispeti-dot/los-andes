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
        // Tabla: alquileres
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id('cod_alquiler');
            $table->unsignedBigInteger('cod_usuario_cli');
            $table->unsignedBigInteger('cod_unidad_alq');
            $table->unsignedBigInteger('cod_evento_alq');
            
            $table->date('fec_salida');
            $table->date('fec_retorno_prev');
            $table->date('fec_retorno_real')->nullable();
            $table->decimal('monto_total', 10, 2);
            $table->decimal('garantia', 10, 2)->default(0);
            $table->enum('est_alquiler', ['Reservado', 'Entregado', 'Devuelto', 'En Mora', 'Cancelado'])->default('Reservado');

            $table->foreign('cod_usuario_cli')->references('id')->on('users');
            $table->foreign('cod_unidad_alq')->references('cod_unidad')->on('inventario_unidades');
            $table->foreign('cod_evento_alq')->references('cod_evento')->on('eventos_folcloricos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};

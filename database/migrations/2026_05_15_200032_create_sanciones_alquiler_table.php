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
        Schema::create('sanciones_alquiler', function (Blueprint $table) {
            $table->id('cod_sancion');
            $table->unsignedBigInteger('cod_alquiler_ref');
            $table->enum('tipo_sancion', ['Retraso', 'Daño', 'Perdida', 'Limpieza']);
            $table->decimal('monto_sancion', 10, 2);
            $table->text('descripcion')->nullable();
            $table->boolean('pagada')->default(false);
            
            $table->foreign('cod_alquiler_ref')->references('cod_alquiler')->on('alquileres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanciones_alquiler');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danzas', function (Blueprint $table) {
            $table->id('cod_danza');
            $table->string('nom_danza')->unique(); // Ej: "Morenada"
            
            // Columna para categorizar (Ayuda al Tesauro y Filtros)
            $table->enum('clasificacion', [
                'Pesada',     // Morenada, Rey Moreno
                'Liviana',    // Caporales, Tobas
                'Autóctona',  // Tinkus, Tarqueada
                'Sátira',     // Diablada, Waka Tokoris
                'Salón'       // Kullawada, Llamerada
            ])->default('Pesada');

            // Descripción y catálogo
            $table->text('descripcion')->nullable();
            $table->string('imagen_danza')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Control de borrado lógico
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danzas');
    }
};
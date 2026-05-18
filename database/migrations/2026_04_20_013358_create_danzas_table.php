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
            
            // Nueva columna para categorizar (Ayuda al Tesauro y Filtros)
            $table->enum('clasificacion', [
                'Pesada',     // Morenada, Rey Moreno
                'Liviana',    // Caporales, Tobas
                'Autóctona',   // Tinkus, Tarqueada
                'Sátira',     // Diablada, Waka Tokoris
                'Salón'       // Kullawada, Llamerada
            ])->default('Pesada');

            // Descripción para que el Admin o el Cliente entiendan el origen
            $table->text('descripcion')->nullable();

            // Imagen referencial de la danza (Para el catálogo público)
            $table->string('imagen_danza')->nullable();

            // Control de borrado lógico para el Administrador
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danzas');
    }
};
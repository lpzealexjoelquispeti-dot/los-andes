<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesauro', function (Blueprint $table) {
            $table->id('cod_termino');
            $table->string('termino_usuario'); // Ej: "Mureniada", "Danza Pesada"
            
            // Clasificación para que el buscador sepa qué hacer
            $table->enum('tipo', ['ortografia', 'sinonimo', 'referencia']);
            
            // Atributo consolidado del parche
            $table->unsignedInteger('veces_buscado')->default(0); 

            // Relación con la Danza Maestra
            $table->unsignedBigInteger('cod_danza_ref');
            $table->foreign('cod_danza_ref')
                  ->references('cod_danza')
                  ->on('danzas')
                  ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tesauro');
    }
};
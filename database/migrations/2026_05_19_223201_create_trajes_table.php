<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trajes', function (Blueprint $table) {
            $table->id('cod_traje');
            
            // Es estrictamente nullable() y se ubica estratégicamente al inicio
            $table->unsignedBigInteger('cod_traje_padre')->nullable();
            
            $table->string('nom_traje');
            $table->text('des_traje');
            $table->decimal('pre_alquiler', 10, 2);
            $table->string('color_traje');
            
            $table->enum('genero', ['Masculino', 'Femenino', 'Unisex'])->default('Masculino');

            // Relaciones base
            $table->unsignedBigInteger('cod_tienda_traje');
            $table->unsignedBigInteger('cod_danza_traje');

            $table->foreign('cod_tienda_traje')->references('cod_tienda')->on('tiendas')->onDelete('cascade');
            $table->foreign('cod_danza_traje')->references('cod_danza')->on('danzas');
            
            // Clave Foránea Auto-referencial Blindada
            $table->foreign('cod_traje_padre')
                ->references('cod_traje')
                ->on('trajes')
                ->onDelete('set null'); // Si borras el padre, el hijo no muere; solo se vuelve independiente (null)
            
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trajes');
    }
};
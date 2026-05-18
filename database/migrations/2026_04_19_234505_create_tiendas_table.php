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
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id('cod_tienda'); // Llave primaria
            
            $table->string('nom_tie'); // Nombre
            $table->string('dir_tie'); // Dirección
            $table->string('tel_tie'); // WhatsApp
            
            // --- AQUÍ ESTÁ LO QUE FALTABA ---
            $table->string('foto_ref'); // Ruta de la foto JPG
            $table->decimal('latitud', 10, 8); // Coordenada Lat
            $table->decimal('longitud', 11, 8); // Coordenada Lng
            
            $table->boolean('est_tie')->default(false); // Estado (Pendiente/Aprobada)
            
            // Llave foránea al Vendedor
            $table->unsignedBigInteger('cod_usuario_tie'); 
            $table->foreign('cod_usuario_tie')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};

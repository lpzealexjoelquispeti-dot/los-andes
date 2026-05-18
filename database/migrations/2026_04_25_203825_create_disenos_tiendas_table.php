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
        Schema::create('disenos_tiendas', function (Blueprint $table) {
            $table->id('cod_diseno');
            $table->unsignedBigInteger('cod_tienda_dis');
            // Atributos de diseño
            $table->string('color_primario')->default('#000000'); // Color de botones/acentos
            $table->string('color_fondo')->default('#ffffff');
            $table->string('banner_path')->nullable(); // Imagen de cabecera
            $table->string('tipografia')->default('font-sans');
            $table->boolean('modo_oscuro')->default(false);
            
            $table->foreign('cod_tienda_dis')->references('cod_tienda')->on('tiendas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disenos_tiendas');
    }
};

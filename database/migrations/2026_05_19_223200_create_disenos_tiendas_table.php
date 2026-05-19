<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disenos_tiendas', function (Blueprint $table) {
            $table->id('cod_diseno');
            
            // Llave foránea
            $table->unsignedBigInteger('cod_tienda_dis');
            $table->foreign('cod_tienda_dis')->references('cod_tienda')->on('tiendas')->onDelete('cascade');
            
            // Atributos de diseño y estructura original
            $table->string('color_primario')->default('#000000'); 
            $table->string('color_fondo')->default('#ffffff');
            $table->string('banner_path')->nullable(); 
            $table->string('tipografia')->default('font-sans');
            $table->boolean('modo_oscuro')->default(false);
            
            // Atributos consolidados (Branding y Horarios)
            $table->string('logo_path')->nullable();
            $table->string('slogan')->nullable();
            $table->string('horario_tie')->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_whatsapp')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Baja lógica global integrada
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disenos_tiendas');
    }
};
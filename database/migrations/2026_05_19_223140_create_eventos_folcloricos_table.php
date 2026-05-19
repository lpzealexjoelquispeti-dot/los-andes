<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_folcloricos', function (Blueprint $table) {
            $table->id('cod_evento');
            $table->string('nom_evento');
            $table->date('fec_inicio');
            $table->date('fec_fin')->nullable();
            $table->string('ubicacion')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Baja lógica global integrada
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_folcloricos');
    }
};
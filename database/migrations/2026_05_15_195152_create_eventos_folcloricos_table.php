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
        // Tabla: eventos_folcloricos
            Schema::create('eventos_folcloricos', function (Blueprint $table) {
                $table->id('cod_evento');
                $table->string('nom_evento');
                $table->date('fec_inicio');
                $table->date('fec_fin')->nullable();
                $table->string('ubicacion')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });

           
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_folcloricos');
    }
};

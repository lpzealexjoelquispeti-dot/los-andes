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
         // Tabla Pivot: danza_evento
            Schema::create('danza_evento', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cod_danza_ref');
                $table->unsignedBigInteger('cod_evento_ref');

                $table->foreign('cod_danza_ref')->references('cod_danza')->on('danzas')->onDelete('cascade');
                $table->foreign('cod_evento_ref')->references('cod_evento')->on('eventos_folcloricos')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danza_evento');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danza_evento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cod_danza_ref');
            $table->unsignedBigInteger('cod_evento_ref');

            // Llaves foráneas
            $table->foreign('cod_danza_ref')->references('cod_danza')->on('danzas')->onDelete('cascade');
            $table->foreign('cod_evento_ref')->references('cod_evento')->on('eventos_folcloricos')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // Baja lógica global integrada
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danza_evento');
    }
};
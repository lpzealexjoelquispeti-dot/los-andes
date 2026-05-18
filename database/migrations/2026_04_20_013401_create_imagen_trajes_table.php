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
        Schema::create('imagen_trajes', function (Blueprint $table) {
            $table->id('cod_imagen');
            $table->string('ruta_img');
            $table->unsignedBigInteger('cod_traje_img');
            $table->foreign('cod_traje_img')->references('cod_traje')->on('trajes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagen_trajes');
    }
};

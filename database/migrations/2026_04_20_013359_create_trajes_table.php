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
        Schema::create('trajes', function (Blueprint $table) {
            $table->id('cod_traje');
            $table->string('nom_traje');
            $table->text('des_traje');
            $table->decimal('pre_alquiler', 10, 2);
            $table->string('talla_traje');
            $table->string('color_traje');

            // Relaciones
            $table->unsignedBigInteger('cod_tienda_traje');
            $table->unsignedBigInteger('cod_danza_traje');

            $table->foreign('cod_tienda_traje')->references('cod_tienda')->on('tiendas')->onDelete('cascade');
            $table->foreign('cod_danza_traje')->references('cod_danza')->on('danzas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajes');
    }
};

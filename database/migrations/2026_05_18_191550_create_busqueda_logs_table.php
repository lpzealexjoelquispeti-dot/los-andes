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
    Schema::create('busqueda_logs', function (Blueprint $table) {
        $table->id();
        $table->string('termino_buscado');         // Lo que escribió el usuario
        $table->string('tipo_resultado');           // 'tesauro', 'danza', 'texto', 'sin_resultado'
        $table->unsignedBigInteger('cod_danza_ref')->nullable(); // Danza encontrada
        $table->foreign('cod_danza_ref')->references('cod_danza')->on('danzas')->nullOnDelete();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        $table->timestamps(); // created_at = fecha exacta de búsqueda
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('busqueda_logs');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id('cod_valoracion');
            $table->unsignedBigInteger('cod_usuario_val');
            $table->unsignedBigInteger('cod_traje_val');
            $table->integer('puntuacion'); // 1 a 5
            $table->text('comentario')->nullable();

            $table->foreign('cod_usuario_val')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cod_traje_val')->references('cod_traje')->on('trajes')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('valoraciones');
    }
};
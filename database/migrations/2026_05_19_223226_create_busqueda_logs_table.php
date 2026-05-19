<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('busqueda_logs', function (Blueprint $table) {
            $table->id();
            $table->string('termino_buscado');         
            $table->string('tipo_resultado');           
            $table->unsignedBigInteger('cod_danza_ref')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->foreign('cod_danza_ref')->references('cod_danza')->on('danzas')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            $table->timestamps(); 
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('busqueda_logs');
    }
};
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id('cod_auditoria');
            $table->unsignedBigInteger('cod_usuario_aud')->nullable();
            $table->string('tabla_afectada');
            $table->string('accion'); // CREATE, UPDATE, DELETE
            $table->json('valor_anterior')->nullable();
            $table->json('valor_nuevo')->nullable();
            $table->string('ip_address')->nullable();
            
            $table->foreign('cod_usuario_aud')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('auditoria');
    }
};
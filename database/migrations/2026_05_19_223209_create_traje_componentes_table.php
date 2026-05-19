<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traje_componentes', function (Blueprint $table) {
            $table->id('cod_componente');
            $table->string('nom_componente'); // Ej: "Matraca de madera", "Penacho de plumas"
            $table->text('des_componente')->nullable();
            
            // Relación con el Traje específico
            $table->unsignedBigInteger('cod_traje_ref');
            $table->foreign('cod_traje_ref')
                  ->references('cod_traje')
                  ->on('trajes')
                  ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traje_componentes');
    }
};
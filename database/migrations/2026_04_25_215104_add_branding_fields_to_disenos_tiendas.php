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
    Schema::table('disenos_tiendas', function (Blueprint $table) {
        // Quitamos banner_path porque ya existe
        // Solo dejamos lo que NO estaba en la tabla original
        if (!Schema::hasColumn('disenos_tiendas', 'logo_path')) {
            $table->string('logo_path')->nullable();
        }
        if (!Schema::hasColumn('disenos_tiendas', 'slogan')) {
            $table->string('slogan')->nullable();
        }
        if (!Schema::hasColumn('disenos_tiendas', 'link_facebook')) {
            $table->string('link_facebook')->nullable();
        }
        if (!Schema::hasColumn('disenos_tiendas', 'link_whatsapp')) {
            $table->string('link_whatsapp')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disenos_tiendas', function (Blueprint $table) {
            //
        });
    }
};

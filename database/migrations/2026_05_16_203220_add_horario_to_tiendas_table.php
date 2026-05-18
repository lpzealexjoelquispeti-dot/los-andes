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
    Schema::table('tiendas', function (Blueprint $table) {
        $table->string('horario_tie')->nullable()->after('tel_tie');
        // Ejemplo: "Lun-Vie 9:00-18:00 / Sab 9:00-13:00"
    });
}

public function down(): void
{
    Schema::table('tiendas', function (Blueprint $table) {
        $table->dropColumn('horario_tie');
    });
}
};

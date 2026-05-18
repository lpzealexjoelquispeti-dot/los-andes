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
    // Lista de tus tablas principales
    $tables = ['users', 'tiendas', 'danzas', 'imagen_trajes', 'disenos_tiendas'];

    foreach ($tables as $tableName) {
        // Agregamos esta validación de ingeniería para evitar el error
        if (!Schema::hasColumn($tableName, 'deleted_at')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }
}

public function down(): void
{
    $tables = ['users', 'tiendas', 'danzas', 'imagen_trajes', 'disenos_tiendas'];

    foreach ($tables as $tableName) {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
};

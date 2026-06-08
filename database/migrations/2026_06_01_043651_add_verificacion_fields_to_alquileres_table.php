<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // En PostgreSQL modificamos el tipo de la columna a VARCHAR para albergar los nuevos estados sin conflictos
        DB::statement("ALTER TABLE alquileres ALTER COLUMN est_alquiler TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE alquileres ALTER COLUMN est_alquiler SET DEFAULT 'Pendiente_Pago'");

        Schema::table('alquileres', function (Blueprint $table) {
            $table->string('nro_celular_cliente', 20)->nullable()->after('garantia');
            $table->string('nombre_garante')->nullable()->after('nro_celular_cliente');
            $table->string('ci_garante', 20)->nullable()->after('nombre_garante');
            $table->string('comprobante_pago_path')->nullable()->after('ci_garante');
            $table->decimal('monto_sena', 10, 2)->default(0)->after('comprobante_pago_path');
            $table->timestamp('fecha_limite_pago')->nullable()->after('monto_sena');
            $table->text('motivo_rechazo')->nullable()->after('fecha_limite_pago');
        });
    }

    public function down(): void
    {
        Schema::table('alquileres', function (Blueprint $table) {
            $table->dropColumn([
                'nro_celular_cliente', 'nombre_garante', 'ci_garante',
                'comprobante_pago_path', 'monto_sena',
                'fecha_limite_pago', 'motivo_rechazo',
            ]);
        });

        DB::statement("ALTER TABLE alquileres ALTER COLUMN est_alquiler TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE alquileres ALTER COLUMN est_alquiler SET DEFAULT 'Reservado'");
    }
};
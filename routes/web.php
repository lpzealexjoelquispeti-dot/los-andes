<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\Vendedor\TrajeController;
use App\Http\Controllers\Vendedor\InformeEstadisticoController;
use App\Http\Controllers\Vendedor\ReporteSancionesController;
use App\Http\Controllers\Vendedor\AlquilerController;
use App\Http\Controllers\Vendedor\TrajeUnidadController;
use App\Http\Controllers\Vendedor\TrajeImpresionController;
// ====================================================================
// 1. ZONA PÚBLICA (Accesible para todos)
// ====================================================================
Route::get('/', function () {
    // Jalamos las últimas 4 danzas registradas en el sistema central
    $danzas = \App\Models\Danza::latest()->take(4)->get();
    
    // Jalamos los últimos 4 trajes principales en vitrina (excluyendo variantes/hijos)
    $trajes = \App\Models\Traje::whereNull('cod_traje_padre')
        ->orWhere('cod_traje_padre', 0)
        ->latest()
        ->take(4)
        ->get();

    return view('welcome', compact('danzas', 'trajes'));
})->name('inicio');


// ====================================================================
// 2. ZONA COMPARTIDA (Requiere estar logueado, sin importar el rol)
// ====================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard Genérico (Principalmente para el Cliente)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gestión del Perfil (Por defecto de Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mis-alquileres', [\App\Http\Controllers\AlquilerController::class, 'index'])
        ->name('cliente.alquileres.index');
    Route::post('/alquileres', [\App\Http\Controllers\AlquilerController::class, 'store'])
        ->name('cliente.alquileres.store');
    Route::patch('/mis-alquileres/{alquiler}/cancelar', [\App\Http\Controllers\AlquilerController::class, 'cancelar'])
        ->name('cliente.alquileres.cancelar');
    Route::post('/mis-alquileres/{alquiler}/valorar', [\App\Http\Controllers\AlquilerController::class, 'valorar'])
        ->name('cliente.alquileres.valorar');

    Route::get('/notificaciones', [\App\Http\Controllers\NotificacionController::class, 'index'])
        ->name('notificaciones.index');
    Route::patch('/notificaciones/marcar-todas', [\App\Http\Controllers\NotificacionController::class, 'marcarTodas'])
        ->name('notificaciones.marcar-todas');
    Route::patch('/notificaciones/{notificacion}/leida', [\App\Http\Controllers\NotificacionController::class, 'marcarLeida'])
        ->name('notificaciones.leida');
    Route::delete('/notificaciones/{notificacion}', [\App\Http\Controllers\NotificacionController::class, 'destroy'])
        ->name('notificaciones.destroy');
    Route::get('/reservar/{unidad}', [App\Http\Controllers\Public\TrajeController::class, 'reservar'])
    ->name('public.reservar');
    
});


// ====================================================================
// 3. ZONA SUPER ADMIN (Solo acceso para el rol 'SuperAdmin')
// ====================================================================

/*
|--------------------------------------------------------------------------
| PANEL ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:SuperAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'header' => 'Panel de Administración Central'
        ]);
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE TIENDAS
    |--------------------------------------------------------------------------
    */
    Route::get('/tiendas-pendientes', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'index'])
        ->name('tiendas.index');

    Route::get('/tiendas/{id}/detalle', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'show'])
        ->name('tiendas.show');

    // Cambiado put por patch según el estándar semántico usado en tus métodos
    Route::patch('/tiendas/{id}/aprobar', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'aprobar'])
        ->name('tiendas.aprobar');

    Route::delete('/tiendas/{id}/rechazar', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'rechazar'])
        ->name('tiendas.rechazar');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO DANZAS
    |--------------------------------------------------------------------------
    */
    Route::get('/danzas', [\App\Http\Controllers\Admin\DanzaController::class, 'index'])
        ->name('danzas.index');

    Route::get('/danzas/crear', [\App\Http\Controllers\Admin\DanzaController::class, 'create'])
        ->name('danzas.create');

    // Corrección semántica a POST para almacenamiento estándar
    Route::post('/danzas', [\App\Http\Controllers\Admin\DanzaController::class, 'store'])
        ->name('danzas.store');

    Route::get('/danzas/{id}', [\App\Http\Controllers\Admin\DanzaController::class, 'show'])
        ->name('danzas.show');

    Route::get('/danzas/{id}/editar', [\App\Http\Controllers\Admin\DanzaController::class, 'edit'])
        ->name('danzas.edit');

    Route::patch('/danzas/{id}', [\App\Http\Controllers\Admin\DanzaController::class, 'update'])
        ->name('danzas.update');

    Route::delete('/danzas/{id}/eliminar', [\App\Http\Controllers\Admin\DanzaController::class, 'destroy'])
        ->name('danzas.destroy');

    Route::patch('/danzas/{id}/restaurar', [\App\Http\Controllers\Admin\DanzaController::class, 'restore'])
        ->name('danzas.restore');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO TESAURO
    |--------------------------------------------------------------------------
    */
    Route::get('/tesauro', [\App\Http\Controllers\Admin\TesauroController::class, 'index'])
        ->name('tesauro.index');

    Route::get('/tesauro/crear', [\App\Http\Controllers\Admin\TesauroController::class, 'create'])
        ->name('tesauro.create');

    Route::post('/tesauro', [\App\Http\Controllers\Admin\TesauroController::class, 'store'])
        ->name('tesauro.store');

    Route::get('/tesauro/{id}/editar', [\App\Http\Controllers\Admin\TesauroController::class, 'edit'])
        ->name('tesauro.edit');

    Route::patch('/tesauro/{id}', [\App\Http\Controllers\Admin\TesauroController::class, 'update'])
        ->name('tesauro.update');

    Route::delete('/tesauro/{id}/eliminar', [\App\Http\Controllers\Admin\TesauroController::class, 'destroy'])
        ->name('tesauro.destroy');

    Route::patch('/tesauro/{id}/restaurar', [\App\Http\Controllers\Admin\TesauroController::class, 'restore'])
        ->name('tesauro.restore');

    /*
    |--------------------------------------------------------------------------
    | REPORTES CENTRALES
    |--------------------------------------------------------------------------
    */
    Route::get('/reportes/tesauro', [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class, 'index'])->name('reportes.tesauro');
    Route::get('/reportes/tesauro/pdf', [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class, 'exportPdf'])->name('reportes.tesauro.pdf');
    Route::get('/reportes/tesauro/excel', [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class, 'exportExcel'])->name('reportes.tesauro.excel');
    Route::get('/reportes/vendedores', [\App\Http\Controllers\Admin\Reportes\VendedorReporteController::class, 'index'])->name('reportes.vendedores');

    /*
    |--------------------------------------------------------------------------
    | USUARIOS Y ROLES
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios', [\App\Http\Controllers\Admin\UserController::class, 'index'])
        ->name('users.index');

    Route::get('/usuarios/crear', [\App\Http\Controllers\Admin\UserController::class, 'create'])
        ->name('users.create');

    Route::post('/usuarios/guardar', [\App\Http\Controllers\Admin\UserController::class, 'store'])
        ->name('users.store');

    Route::get('/usuarios/{id}/editar', [\App\Http\Controllers\Admin\UserController::class, 'edit'])
        ->name('users.edit');

    Route::put('/usuarios/{id}/actualizar', [\App\Http\Controllers\Admin\UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/usuarios/{id}/dar-de-baja', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->name('users.destroy');

    Route::patch('/usuarios/{id}/restaurar', [\App\Http\Controllers\Admin\UserController::class, 'restore'])
        ->name('users.restore');

    Route::patch('/usuarios/{id}/regenerar-clave', [\App\Http\Controllers\Admin\UserController::class, 'regeneratePassword'])
        ->name('users.password.regenerate');
});


// ====================================================================
// 4. ZONA VENDEDOR (Solo acceso para el rol 'Vendedor')
// ====================================================================
Route::middleware(['auth', 'role:Vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard', ['header' => 'Mi Tienda y Negocio']);
    })->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE LA TIENDA
    |--------------------------------------------------------------------------
    */
    Route::get('/mi-tienda', [TiendaController::class, 'index'])->name('tienda.index');
    Route::get('/mi-tienda/nueva', [TiendaController::class, 'create'])->name('tienda.create');
    Route::post('/mi-tienda', [TiendaController::class, 'store'])->name('tienda.store');
    Route::get('/mi-tienda/{id}/editar', [TiendaController::class, 'edit'])->name('tienda.edit');
    Route::put('/mi-tienda/{id}', [TiendaController::class, 'update'])->name('tienda.update');
    
    Route::get('/tienda/diseno', [TiendaController::class, 'diseno'])->name('tienda.diseno');
    Route::post('/tienda/diseno', [TiendaController::class, 'storeDiseno'])->name('tienda.diseno.store');

    /*
    |--------------------------------------------------------------------------
    | CATÁLOGO MAESTRO DE TRAJES
    |--------------------------------------------------------------------------
    */
    // Ruta restore colocada estratégicamente sobre el resource
    Route::post('/trajes/{id}/restore', [TrajeController::class, 'restore'])->name('trajes.restore');
    Route::post('/trajes/{id}/destroy-total', [TrajeController::class, 'destroyTotal'])->name('trajes.destroyTotal');
    Route::post('/trajes/{id}/restore-total', [TrajeController::class, 'restoreTotal'])->name('trajes.restoreTotal');
    
    // Resource de Trajes
    Route::resource('trajes', TrajeController::class);

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE INVENTARIO (UNIDADES FÍSICAS DE LOS TRAJES)
    |--------------------------------------------------------------------------
    */
    Route::get('/trajes/{cod_traje}/unidades', [TrajeUnidadController::class, 'index'])->name('trajes.unidades.index');
    Route::get('/trajes/{cod_traje}/unidades/nueva', [TrajeUnidadController::class, 'create'])->name('trajes.unidades.create');
    Route::post('/trajes/{cod_traje}/unidades', [TrajeUnidadController::class, 'store'])->name('trajes.unidades.store');
    Route::get('/trajes/{cod_traje}/unidades/danos', [TrajeUnidadController::class, 'danos'])->name('trajes.unidades.danos');
    
    Route::get('/unidades/{id}/editar', [TrajeUnidadController::class, 'edit'])->name('unidades.edit');
    Route::put('/unidades/{id}', [TrajeUnidadController::class, 'update'])->name('unidades.update');
    Route::delete('/unidades/{id}', [TrajeUnidadController::class, 'destroy'])->name('unidades.destroy');
    Route::post('/unidades/{id}/restore', [TrajeUnidadController::class, 'restore'])->name('unidades.restore');

    /*
    |--------------------------------------------------------------------------
    | FLUJO OPERATIVO DE ALQUILERES, DEVOLUCIONES Y SANCIONES
    |--------------------------------------------------------------------------
    */
    Route::get('/alquileres', [AlquilerController::class, 'index'])->name('alquileres.index');
    Route::get('/alquileres/{alquiler}', [AlquilerController::class, 'show'])->name('alquileres.show');
    
    // Acciones de estado del Alquiler
    Route::patch('/alquileres/{alquiler}/aprobar', [AlquilerController::class, 'aprobar'])->name('alquileres.aprobar');
    Route::patch('/alquileres/{alquiler}/rechazar', [AlquilerController::class, 'rechazar'])->name('alquileres.rechazar');
    Route::patch('/alquileres/{alquiler}/entregar', [AlquilerController::class, 'entregar'])->name('alquileres.entregar');
    Route::patch('/alquileres/{alquiler}/devolver', [AlquilerController::class, 'devolver'])->name('alquileres.devolver');
    Route::patch('/alquileres/{alquiler}/cancelar', [AlquilerController::class, 'cancelar'])->name('alquileres.cancelar');
    
    // Gestión de Sanciones financieras por Daños o Mora
    Route::post('/alquileres/{alquiler}/sanciones', [AlquilerController::class, 'sancionar'])->name('alquileres.sanciones.store');
    Route::patch('/sanciones/{sancion}/pagar', [AlquilerController::class, 'pagarSancion'])->name('sanciones.pagar');

    /*
    |--------------------------------------------------------------------------
    | INFORMES Y REPORTES PARAMETRIZADOS (Corrección Unificada)
    |--------------------------------------------------------------------------
    */
    Route::get('/informes-estadisticos', [InformeEstadisticoController::class, 'index'])->name('informes.index');
    Route::get('/reportes', [InformeEstadisticoController::class, 'index'])->name('reportes.index');

    Route::prefix('reportes/sanciones-entregas')->name('reportes.')->group(function () {
        Route::get('/', [ReporteSancionesController::class, 'index'])->name('sanciones_entregas');
        Route::get('/pdf', [ReporteSancionesController::class, 'descargarPdf'])->name('sanciones_entregas.pdf');
    });

    // ─────────────────────────────────────────────────────────────
    // Reporte: Trajes más gastados (por nivel_uso_alquileres)
    // ─────────────────────────────────────────────────────────────
    Route::get('/reportes/trajes-mas-gastados', [\App\Http\Controllers\Vendedor\ReporteTrajesMasGastadosController::class, 'index'])
        ->name('vendedor.reportes.trajes_mas_gastados');

    Route::get('/reportes/trajes-mas-gastados/pdf', [\App\Http\Controllers\Vendedor\ReporteTrajesMasGastadosController::class, 'descargarPdf'])
        ->name('vendedor.reportes.trajes_mas_gastados.pdf');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO INDUSTRIAL DE IMPRESIÓN Y REPOSICIÓN DE ACCESORIOS
    |--------------------------------------------------------------------------
    */
    Route::get('/trajes/{id}/impresion', [TrajeImpresionController::class, 'panelImpresion'])->name('trajes.impresion.panel');
    Route::post('/trajes/{id}/impresion/pdf', [TrajeImpresionController::class, 'descargarPdf'])->name('trajes.impresion.pdf');
    Route::get('/unidades/{id}/reimprimir/{pieza}', [TrajeImpresionController::class, 'reimprimirPieza'])->name('unidades.reimprimir.pieza');

});


// ====================================================================
// 5. ZONA USUARIO COMÚN (Marketplace Público)
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/explorar-tiendas', [App\Http\Controllers\Public\TiendaPublicController::class, 'index'])->name('public.tiendas.index');
    Route::get('/tienda/{id}', [App\Http\Controllers\Public\TiendaPublicController::class, 'show'])->name('public.tiendas.show');
    
    // --- Rutas del Catálogo de Trajes (Capa Cliente) ---
    Route::get('/catalogo', [App\Http\Controllers\Public\TrajeController::class, 'index'])
        ->name('public.catalogo.index');

    Route::get('/traje/{id}', [App\Http\Controllers\Public\TrajeController::class, 'show'])
        ->name('public.trajes.show');

    // Autocompletado del Tesauro
    Route::get('/api/tesauro/autocomplete', function(\Illuminate\Http\Request $request) {
        $q = $request->input('q', '');
        if (strlen($q) < 2) return response()->json([]);
        $terminos = \App\Models\Tesauro::whereRaw('LOWER(termino_usuario) LIKE LOWER(?)', ["%{$q}%"])
            ->with('danza:cod_danza,nom_danza')
            ->limit(6)
            ->get(['cod_termino', 'termino_usuario', 'cod_danza_ref', 'tipo']);
        return response()->json($terminos);
    })->name('public.tesauro.autocomplete');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



// ====================================================================
// 1. ZONA PÚBLICA (Accesible para todos)
// ====================================================================
Route::get('/', function () {
    return view('welcome');
})->name('inicio'); // <- Le damos el nombre 'inicio' para el controlador


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
| REPORTES
|--------------------------------------------------------------------------
*/
Route::get('/reportes/tesauro',       [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class,  'index'])->name('reportes.tesauro');
    Route::get('/reportes/tesauro/pdf',   [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class,  'exportPdf'])->name('reportes.tesauro.pdf');
    Route::get('/reportes/tesauro/excel', [\App\Http\Controllers\Admin\Reportes\TesauroReporteController::class,  'exportExcel'])->name('reportes.tesauro.excel');
    Route::get('/reportes/vendedores',    [\App\Http\Controllers\Admin\Reportes\VendedorReporteController::class, 'index'])->name('reportes.vendedores');
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
    
    // RUTAS DE LA TIENDA
   // RUTAS DE LA TIENDA (VENDEDOR)
    Route::get('/mi-tienda', [\App\Http\Controllers\TiendaController::class, 'index'])->name('tienda.index'); // <-- NUEVA RUTA PRINCIPAL
    Route::get('/mi-tienda/nueva', [\App\Http\Controllers\TiendaController::class, 'create'])->name('tienda.create');
    Route::post('/mi-tienda', [\App\Http\Controllers\TiendaController::class, 'store'])->name('tienda.store');
    Route::get('/mi-tienda/{id}/editar', [\App\Http\Controllers\TiendaController::class, 'edit'])->name('tienda.edit');
    Route::put('/mi-tienda/{id}', [\App\Http\Controllers\TiendaController::class, 'update'])->name('tienda.update');
    Route::post('/trajes/{id}/restore', [App\Http\Controllers\Vendedor\TrajeController::class, 'restore'])->name('trajes.restore');

    // Tu ruta de resource que ya tenías
    Route::resource('trajes', App\Http\Controllers\Vendedor\TrajeController::class);
    Route::get('/tienda/diseno', [App\Http\Controllers\TiendaController::class, 'diseno'])->name('tienda.diseno');
    Route::post('/tienda/diseno', [App\Http\Controllers\TiendaController::class, 'storeDiseno'])->name('tienda.diseno.store');
    // ── GESTIÓN DE INVENTARIO (UNIDADES FÍSICAS DE LOS TRAJES) ──
    Route::get('/trajes/{cod_traje}/unidades/danos',  [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'danos'])
     ->name('trajes.unidades.danos');
 
// Listado de unidades de un traje específico
Route::get('/trajes/{cod_traje}/unidades',        [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'index'])
     ->name('trajes.unidades.index');
 
// Formulario para añadir una nueva unidad física
Route::get('/trajes/{cod_traje}/unidades/nueva',  [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'create'])
     ->name('trajes.unidades.create');
 // ── MÓDULO INDUSTRIAL DE IMPRESIÓN Y REPOSICIÓN DE ACCESORIOS ──
    Route::get('/trajes/{id}/impresion', [App\Http\Controllers\Vendedor\TrajeImpresionController::class, 'panelImpresion'])->name('trajes.impresion.panel');
    Route::post('/trajes/{id}/impresion/pdf', [App\Http\Controllers\Vendedor\TrajeImpresionController::class, 'descargarPdf'])->name('trajes.impresion.pdf');
    Route::get('/unidades/{id}/reimprimir/{pieza}', [App\Http\Controllers\Vendedor\TrajeImpresionController::class, 'reimprimirPieza'])->name('unidades.reimprimir.pieza');
// Guardar la nueva unidad física
Route::post('/trajes/{cod_traje}/unidades',       [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'store'])
     ->name('trajes.unidades.store');
Route::post('/trajes/{id}/destroy-total', [App\Http\Controllers\Vendedor\TrajeController::class, 'destroyTotal'])->name('trajes.destroyTotal');
    Route::post('/trajes/{id}/restore-total', [App\Http\Controllers\Vendedor\TrajeController::class, 'restoreTotal'])->name('trajes.restoreTotal');
     
// Editar una unidad específica
Route::get('/unidades/{id}/editar',               [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'edit'])
     ->name('unidades.edit');
 
// Actualizar datos de la unidad física
Route::put('/unidades/{id}',                      [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'update'])
     ->name('unidades.update');
 
// Dar de baja (Soft Delete)
Route::delete('/unidades/{id}',                   [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'destroy'])
     ->name('unidades.destroy');
 
// Reactivar una unidad dada de baja
Route::post('/unidades/{id}/restore',             [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'restore'])
     ->name('unidades.restore');
     
});

// Rutas de Autenticación de Breeze

// ====================================================================
// 4. ZONA USUARIO COMUN (Solo acceso para el rol 'COMUN')
// ====================================================================
// Rutas públicas (accesibles tras loguearse como usuario común)
// Rutas para el Marketplace Público
Route::middleware(['auth'])->group(function () {
    Route::get('/explorar-tiendas', [App\Http\Controllers\Public\TiendaPublicController::class, 'index'])->name('public.tiendas.index');
    Route::get('/tienda/{id}', [App\Http\Controllers\Public\TiendaPublicController::class, 'show'])->name('public.tiendas.show');
    // --- Rutas del Catálogo de Trajes (Capa Cliente) ---
Route::get('/catalogo', [App\Http\Controllers\Public\TrajeController::class, 'index'])
    ->name('public.catalogo.index');

Route::get('/traje/{id}', [App\Http\Controllers\Public\TrajeController::class, 'show'])
    ->name('public.trajes.show');
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
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
Route::middleware(['auth', 'role:SuperAdmin'])->prefix('admin')->name('admin.')->group(function () {
    
    // URL: /admin/dashboard  |  Nombre de ruta: admin.dashboard
    Route::get('/dashboard', function () {
        return view('dashboard', ['header' => 'Panel de Administración Central']);
    })->name('dashboard');
    
    // MÁS ADELANTE PONDREMOS AQUÍ:
    // Route::get('/tiendas-pendientes', ...)->name('tiendas.pendientes');
    // Route::get('/tesauro', ...)->name('tesauro.index');
    Route::get('/dashboard', function () {
        return view('dashboard', ['header' => 'Panel de Administración Central']);
    })->name('dashboard');

    // Gestión de Tiendas Pendientes
    Route::get('/tiendas-pendientes', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'index'])->name('tiendas.index');
    Route::patch('/tiendas/{id}/aprobar', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'aprobar'])->name('tiendas.aprobar');
    Route::get('/tiendas/{id}/detalle', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'show'])->name('tiendas.show');
    Route::delete('/tiendas/{id}/rechazar', [\App\Http\Controllers\Admin\AdminTiendaController::class, 'rechazar'])->name('tiendas.rechazar');
    // CRUD de Danzas para el Administrador
    // --- Módulo de Gestión de Danzas (Vistas Independientes) ---

// 1. Listado principal
Route::get('/danzas', [\App\Http\Controllers\Admin\DanzaController::class, 'index'])->name('danzas.index');

// 2. Formulario de creación (Nueva ruta para la vista create)
Route::get('/danzas/crear', [\App\Http\Controllers\Admin\DanzaController::class, 'create'])->name('danzas.create');

// 3. Procesar el guardado de la nueva danza
Route::post('/danzas', [\App\Http\Controllers\Admin\DanzaController::class, 'store'])->name('danzas.store');

// 4. Formulario de edición
Route::get('/danzas/{id}/editar', [\App\Http\Controllers\Admin\DanzaController::class, 'edit'])->name('danzas.edit');

// 5. Procesar la actualización
Route::patch('/danzas/{id}', [\App\Http\Controllers\Admin\DanzaController::class, 'update'])->name('danzas.update');

// 6. Baja lógica (Soft Delete)
Route::delete('/danzas/{id}/eliminar', [\App\Http\Controllers\Admin\DanzaController::class, 'destroy'])->name('danzas.destroy');

// 7. Restauración de baja lógica
Route::patch('/danzas/{id}/restaurar', [\App\Http\Controllers\Admin\DanzaController::class, 'restore'])->name('danzas.restore');
Route::get('/danzas/{id}', [\App\Http\Controllers\Admin\DanzaController::class, 'show'])->name('danzas.show');
//TESAURO
// --- Módulo de Inteligencia de Búsqueda (Tesauro) ---
// --- Módulo Tesauro ---
Route::get('/tesauro', [\App\Http\Controllers\Admin\TesauroController::class, 'index'])->name('tesauro.index');
Route::get('/tesauro/crear', [\App\Http\Controllers\Admin\TesauroController::class, 'create'])->name('tesauro.create');
Route::post('/tesauro', [\App\Http\Controllers\Admin\TesauroController::class, 'store'])->name('tesauro.store');
Route::get('/tesauro/{id}/editar', [\App\Http\Controllers\Admin\TesauroController::class, 'edit'])->name('tesauro.edit');
Route::patch('/tesauro/{id}', [\App\Http\Controllers\Admin\TesauroController::class, 'update'])->name('tesauro.update');
Route::delete('/tesauro/{id}/eliminar', [\App\Http\Controllers\Admin\TesauroController::class, 'destroy'])->name('tesauro.destroy');
Route::patch('/tesauro/{id}/restaurar', [\App\Http\Controllers\Admin\TesauroController::class, 'restore'])->name('tesauro.restore');

//------Usuarios-----//
// -------------------------------------------------------------------------
// MÓDULO: CONTROL DE USUARIOS Y ROLES (Rutas Explícitas para el Administrador)
// -------------------------------------------------------------------------
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Vista principal de la tabla (La que estás cargando ahora)
    Route::get('/usuarios', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    
    // Formulario de creación
    Route::get('/usuarios/crear', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    
    // Acción de guardar en la DB (Envía el correo)
    Route::post('/usuarios/guardar', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    
    // Formulario de edición
    Route::get('/usuarios/{id}/editar', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    
    // Acción de actualizar datos en la DB
    Route::put('/usuarios/{id}/actualizar', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    
    // Acción de baja lógica (Soft Delete)
    Route::delete('/usuarios/{id}/dar-de-baja', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Acción de restauración de la papelera (Tu estándar PATCH)
    
   // Cambia el name a 'users.password.regenerate'
Route::patch('/usuarios/{id}/regenerar-clave', [\App\Http\Controllers\Admin\UserController::class, 'regeneratePassword'])->name('users.password.regenerate');
});
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
 
// Guardar la nueva unidad física
Route::post('/trajes/{cod_traje}/unidades',       [\App\Http\Controllers\Vendedor\TrajeUnidadController::class, 'store'])
     ->name('trajes.unidades.store');
 
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
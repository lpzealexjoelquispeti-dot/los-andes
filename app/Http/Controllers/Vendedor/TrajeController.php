<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Traje;        // <--- AÑADE ESTA
use App\Models\ImagenTraje;  // <--- AÑADE ESTA TAMBIÉN (la usas en la línea 41)
use App\Models\Danza;
 // Asegúrate de importar el modelo de imágenes
use Illuminate\Support\Facades\Storage; // Importar Storage
class TrajeController extends Controller
{
    //
    public function create() {
    $tienda = auth()->user()->tiendas()->first();
    
    // BLOQUEO DE SEGURIDAD: Si la tienda no está aprobada, no entra.
    if (!$tienda || !$tienda->est_tie) {
        return redirect()->route('vendedor.dashboard')
            ->with('error', 'Tu tienda debe ser aprobada por el Admin antes de publicar trajes.');
    }

    $danzas = \App\Models\Danza::all();
    return view('vendedor.trajes.create', compact('danzas'));
}

public function store(Request $request) 
{
    // 1. Validar (Cambiamos 'talla_traje' por el array 'tallas')
    $request->validate([
        'nom_traje' => 'required|min:5|max:100',
        'des_traje' => 'required|min:10',
        'pre_alquiler' => 'required|numeric|min:0',
        'color_traje' => 'required',
        'cod_danza_traje' => 'required|exists:danzas,cod_danza',
        
        // Reglas para el array de tallas
        'tallas' => 'required|array|min:1',
        'tallas.*' => 'in:S,M,L,XL,Personalizado',
        
        'fotos' => 'required|array|min:1|max:7',
        'fotos.*' => 'image|mimes:jpeg,jpg|max:5120',
    ], [
        'tallas.required' => 'Debes seleccionar al menos una talla para crear el inventario de este traje.',
    ]);

    $tienda = Auth::user()->tiendas()->first();

    // 2. Crear el traje maestro (SIN el campo talla_traje)
    $traje = \App\Models\Traje::create($request->only([
        'nom_traje', 'des_traje', 'pre_alquiler', 'color_traje', 'cod_danza_traje'
    ]) + ['cod_tienda_traje' => $tienda->cod_tienda]);

    // 3. Crear las unidades físicas en el Inventario para cada talla seleccionada
    // 3. Crear las unidades físicas en el Inventario para cada talla seleccionada
foreach ($request->tallas as $talla) {
    // Generamos un código inicial único para que PostgreSQL no rechace el NOT NULL
    $serial = 'TJ-' . $traje->cod_traje . '-' . strtoupper($talla) . '-01';

    $traje->unidades()->create([
        'talla' => $talla,
        'nro_serie_interno' => $serial,    // Requerido por tu migración
        'estado_fisico' => 'Nuevo',        // Tu ENUM real en lugar de 'estado'
        'disponibilidad' => true,          // Tu BOOLEAN real
    ]);
}

    // 4. Subir fotos
    if($request->hasFile('fotos')) {
        foreach($request->file('fotos') as $index => $foto) {
            $nombreArchivo = time() . '_' . $index . '.jpg';
            $ruta = $foto->storeAs('trajes', $nombreArchivo, 'public');
            
            \App\Models\ImagenTraje::create([
                'ruta_img' => $ruta,
                'cod_traje_img' => $traje->cod_traje
            ]);
        }
    }

    return redirect()->route('vendedor.trajes.index')->with('success', '¡Traje e inventario publicados con éxito!');
}
public function index()
{
    $user = Auth::user();
    $tienda = $user->tiendas()->first();

    // MODIFICACIÓN AQUÍ: Añadimos 'unidades' al método with()
    $trajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
                    ->withTrashed() 
                    ->with(['imagenes', 'danza', 'unidades']) // <--- ¡AQUÍ AGREGAMOS 'unidades'!
                    ->latest()
                    ->get();

    return view('vendedor.trajes.index', compact('trajes'));
}
public function edit($id)
{
    // Cargamos el traje incluyendo sus unidades de inventario asociadas (Eager Loading)
    $traje = Traje::withTrashed()->with('unidades')->findOrFail($id);
    $danzas = Danza::all(); 

    return view('vendedor.trajes.edit', compact('traje', 'danzas'));
}
public function update(Request $request, $id)
{
    $traje = Traje::withTrashed()->with('unidades')->findOrFail($id);

    $request->validate([
        'nom_traje' => ['required', 'string', 'min:5', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
        'pre_alquiler' => 'required|numeric|min:0',
        'color_traje' => ['required', 'string', 'max:50', 'regex:/^[\pL\s]+$/u'],
        'cod_danza_traje' => 'required|exists:danzas,cod_danza',
        'des_traje' => 'nullable|string|max:1000',
        
        // Validación del array de tallas en edición
        'tallas' => 'required|array|min:1',
        'tallas.*' => 'in:S,M,L,XL,Personalizado',
        
        'fotos_nuevas' => 'nullable|array|max:7',
        'fotos_nuevas.*' => 'image|mimes:jpg,jpeg|max:5120',
    ], [
        'nom_traje.regex' => 'El nombre del traje solo debe contener letras.',
        'color_traje.regex' => 'El color solo debe contener letras.',
        'tallas.required' => 'El traje debe tener al menos una talla en inventario.',
    ]);

    // 1. Actualizar datos principales (Removido talla_traje)
    $traje->update($request->only([
        'nom_traje', 'pre_alquiler', 'color_traje', 'des_traje', 'cod_danza_traje'
    ]));

    // 2. Sincronizar de forma inteligente las Tallas / Unidades de Inventario
   // ... código anterior de actualización del traje

// 2. Sincronizar las Tallas / Unidades de Inventario
$tallasActuales = $traje->unidades->pluck('talla')->toArray();
$tallasSeleccionadas = $request->tallas;

// Tallas que el usuario desmarcó -> Se eliminan
$tallasAEliminar = array_diff($tallasActuales, $tallasSeleccionadas);
if (!empty($tallasAEliminar)) {
    $traje->unidades()->whereIn('talla', $tallasAEliminar)->delete();
}

// Tallas nuevas -> Se crean con la estructura correcta
$tallasAAgregar = array_diff($tallasSeleccionadas, $tallasActuales);
foreach ($tallasAAgregar as $nuevaTalla) {
    $traje->unidades()->create([
        'talla' => $nuevaTalla,
        'nro_serie_interno' => 'TJ-' . $traje->cod_traje . '-' . $nuevaTalla . '-' . rand(1000, 9999),
        'estado_fisico' => 'Nuevo',
        'disponibilidad' => true,
    ]);
}

// ... código posterior de fotos
    // 3. Procesar eliminación de fotos marcadas
    if ($request->has('fotos_borrar') && !empty($request->fotos_borrar)) {
        $ids = explode(',', $request->fotos_borrar);
        $imagenes = $traje->imagenes()->whereIn('cod_imagen', $ids)->get();
        
        foreach ($imagenes as $img) {
            \Storage::disk('public')->delete($img->ruta_img);
            $img->delete();
        }
    }

    // 4. Guardar nuevas fotos
    if ($request->hasFile('fotos_nuevas')) {
        foreach ($request->file('fotos_nuevas') as $file) {
            $path = $file->store('trajes', 'public');
            $traje->imagenes()->create(['ruta_img' => $path]);
        }
    }

    return redirect()->route('vendedor.trajes.index')->with('success', '¡El traje y su inventario se actualizaron con éxito!');
}
// Borrado Lógico
public function destroy($id)
{
    $traje = Traje::findOrFail($id);
    $traje->delete(); // Laravel automáticamente llena 'deleted_at'

    return back()->with('success', 'El traje ha sido desactivado (borrado lógico)');
}

// Restaurar un traje (Para volver a mostrarlo)
public function restore($id)
{
    // Usamos withTrashed() para que pueda encontrar el traje que tiene deleted_at
    $traje = \App\Models\Traje::withTrashed()->findOrFail($id);
    
    $traje->restore();

    return back()->with('success', '¡El traje "' . $traje->nom_traje . '" ha vuelto a la vitrina!');
}
}

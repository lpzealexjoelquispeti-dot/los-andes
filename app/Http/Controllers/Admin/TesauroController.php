<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tesauro;
use App\Models\Danza;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // No olvides importar esto arriba


class TesauroController extends Controller
{
    /**
     * Listado principal del Tesauro
     */
    public function index()
{
    // 1. Cargamos las danzas con sus tesauros para el carrusel superior
    $danzas = \App\Models\Danza::with('tesauros')->orderBy('nom_danza', 'asc')->get();
    
    // 2. Cargamos el listado general para la tabla de abajo
    // Usamos la versión completa que ya tenías con withTrashed()
    $terminos = Tesauro::with(['danza' => function($query) {
            $query->withTrashed(); 
        }])
        ->withTrashed()
        ->orderBy('termino_usuario', 'asc')
        ->get();

    return view('admin.tesauro.index', compact('danzas', 'terminos'));
}

    /**
     * Formulario de creación
     */
    public function create()
    {
        // Necesitamos las danzas para el select del formulario
        $danzas = Danza::orderBy('nom_danza', 'asc')->get();
        return view('admin.tesauro.create', compact('danzas'));
    }

    /**
     * Guardar nuevo término en el Tesauro
     */
    public function store(Request $request)
    {
        $this->validarTesauro($request);

        Tesauro::create([
            'termino_usuario' => mb_strtoupper($request->termino_usuario), // Normalizamos a mayúsculas
            'cod_danza_ref'   => $request->cod_danza_ref,
            'tipo'            => $request->tipo,
        ]);

        return redirect()->route('admin.tesauro.index')
                         ->with('success', 'El término ha sido vinculado con éxito al buscador.');
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $tesauro = Tesauro::withTrashed()->findOrFail($id);
        $danzas = Danza::orderBy('nom_danza', 'asc')->get();
        
        return view('admin.tesauro.edit', compact('tesauro', 'danzas'));
    }

    /**
     * Actualizar término existente
     */
    public function update(Request $request, $id)
{
    // 1. IMPORTANTE: Pasamos el $id a la función de validación
    $this->validarTesauro($request, $id);

    $tesauro = Tesauro::withTrashed()->findOrFail($id);
    
    $tesauro->update([
        'termino_usuario' => mb_strtoupper($request->termino_usuario),
        'cod_danza_ref'   => $request->cod_danza_ref,
        'tipo'            => $request->tipo,
    ]);

    return redirect()->route('admin.tesauro.index')
                     ->with('success', 'Vínculo actualizado correctamente.');
}

    /**
     * Baja lógica (Soft Delete)
     */
    public function destroy($id)
    {
        $tesauro = Tesauro::findOrFail($id);
        $tesauro->delete();

        return back()->with('info', 'El término ha sido desactivado del buscador.');
    }

    /**
     * Restaurar término dado de baja
     */
    public function restore($id)
    {
        $tesauro = Tesauro::withTrashed()->findOrFail($id);
        $tesauro->restore();

        return back()->with('success', 'El término vuelve a estar activo para el cliente común.');
    }

    /**
     * Lógica de validación centralizada
     */
   private function validarTesauro(Request $request, $id = null)
{
    return $request->validate([
        'termino_usuario' => [
            'required',
            'string',
            'max:255',
            // Aquí está el truco: le decimos que sea único pero que ignore el ID actual
            Rule::unique('tesauro', 'termino_usuario')->ignore($id, 'cod_termino')
        ],
        'cod_danza_ref'   => 'required|exists:danzas,cod_danza',
        'tipo'            => 'required|in:ortografia,sinonimo,referencia'
    ], [
        'termino_usuario.unique'   => 'Este término ya existe en el Tesauro.',
        'termino_usuario.required' => 'El término es obligatorio.',
    ]);
}
}
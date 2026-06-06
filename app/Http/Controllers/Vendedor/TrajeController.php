<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Traje;        
use App\Models\ImagenTraje;  
use App\Models\Danza;
use App\Models\InventarioUnidad;
use Illuminate\Support\Facades\Storage; 

class TrajeController extends Controller
{
    public function create() 
    {
        $tienda = auth()->user()->tiendas()->first();
        
        // BLOQUEO DE SEGURIDAD: Si la tienda no está aprobada, no entra.
        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'Tu tienda debe ser aprobada por el Admin antes de publicar trajes.');
        }

        $danzas = Danza::orderBy('nom_danza', 'asc')->get();
        return view('vendedor.trajes.create', compact('danzas'));
    }

    public function store(Request $request) 
    {
        // 1. Validar Campos Compartidos Base (🌟 Adicionado: Fraternidad Obligatoria)
        $request->validate([
            'nom_traje_base'  => 'required|min:5|max:100',
            'fraternidad'     => 'required|string|max:255', // Campo Folclórico Requerido
            'pre_alquiler'    => 'required|numeric|min:0',
            'color_traje'     => 'required|string|max:50',
            'cod_danza_traje' => 'required|exists:danzas,cod_danza',
            'modalidad'       => 'required|in:variantes,unisex',
        ], [
            'fraternidad.required' => 'La fraternidad es fundamental para identificar la paleta de colores oficial del bloque.',
            'nom_traje_base.required' => 'El nombre base o colección del traje es obligatorio.'
        ]);

        $tienda = Auth::user()->tiendas()->first();

        // Rescatamos el nombre de la danza para armar el prefijo unificado (ej: REY, TIN, MOR)
        $danzaNombre = Danza::find($request->cod_danza_traje)->nom_danza ?? 'TJ';
        $prefijoDanza = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));

        // ==========================================
        // CASO INTERNO A: MODALIDAD VARIANTES (PAREJA)
        // ==========================================
        if ($request->modalidad === 'variantes') {
            
            $request->validate([
                'des_varon'       => 'required|min:10',
                'tallas_varon'    => 'required|array|min:1',
                'tallas_varon.*'  => 'in:S,M,L,XL,Personalizado',
                'fotos_varon'     => 'required|array|min:4|max:7',
                'fotos_varon.*'   => 'image|mimes:jpeg,jpg|max:5120',

                'des_mujer'       => 'required|min:10',
                'tallas_mujer'    => 'required|array|min:1',
                'tallas_mujer.*'  => 'in:S,M,L,XL,Personalizado',
                'fotos_mujer'     => 'required|array|min:4|max:7',
                'fotos_mujer.*'   => 'image|mimes:jpeg,jpg|max:5120',
            ], [
                'tallas_varon.required' => 'Debes marcar al menos una talla para el bloque de varones.',
                'tallas_mujer.required' => 'Debes marcar al menos una talla para el bloque de mujeres.',
                'fotos_varon.min'       => 'El bloque de varones exige un mínimo de 4 fotos.',
                'fotos_mujer.min'       => 'El bloque de mujeres exige un mínimo de 4 fotos.',
            ]);

            // REGISTRO 1: Traje Masculino (Padre - 🌟 Fraternidad Sincronizada)
            $varon = Traje::create([
                'cod_traje_padre'  => null,
                'nom_traje'        => $request->nom_traje_base . ' - Varón',
                'fraternidad'      => $request->fraternidad,
                'des_traje'        => $request->des_varon,
                'pre_alquiler'     => $request->pre_alquiler,
                'color_traje'      => $request->color_traje,
                'genero'           => 'Masculino',
                'cod_tienda_traje' => $tienda->cod_tienda,
                'cod_danza_traje'  => $request->cod_danza_traje,
            ]);
            $this->procesarFotos($request->file('fotos_varon'), $varon->cod_traje);
            
            // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Varón (M)
            foreach ($request->tallas_varon as $talla) {
                $tallaLimpia = strtoupper($talla);
                $serialM = $prefijoDanza . '-' . $varon->cod_traje . '-M-' . $tallaLimpia . '-01';
                
                $varon->unidades()->create([
                    'talla'             => $talla,
                    'nro_serie_interno' => $serialM,
                    'estado_fisico'     => 'Nuevo',
                    'observaciones'     => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                    'disponibilidad'    => true,
                ]);
            }

            // REGISTRO 2: Traje Femenino (Hijo - 🌟 Fraternidad Sincronizada)
            $mujer = Traje::create([
                'cod_traje_padre'  => $varon->cod_traje,
                'nom_traje'        => $request->nom_traje_base . ' - Damas',
                'fraternidad'      => $request->fraternidad,
                'des_traje'        => $request->des_mujer,
                'pre_alquiler'     => $request->pre_alquiler,
                'color_traje'      => $request->color_traje,
                'genero'           => 'Femenino',
                'cod_tienda_traje' => $tienda->cod_tienda,
                'cod_danza_traje'  => $request->cod_danza_traje,
            ]);
            $this->procesarFotos($request->file('fotos_mujer'), $mujer->cod_traje);
            
            // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Damas (F)
            foreach ($request->tallas_mujer as $talla) {
                $tallaLimpia = strtoupper($talla);
                $serialF = $prefijoDanza . '-' . $mujer->cod_traje . '-F-' . $tallaLimpia . '-01';
                
                $mujer->unidades()->create([
                    'talla'             => $talla,
                    'nro_serie_interno' => $serialF,
                    'estado_fisico'     => 'Nuevo',
                    'observaciones'     => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                    'disponibilidad'    => true,
                ]);
            }

        } else {
            // ==========================================
            // CASO INTERNO B: MODALIDAD UNISEX / ÚNICO
            // ==========================================
            $request->validate([
                'des_unisex'       => 'required|min:10',
                'tallas_unisex'    => 'required|array|min:1',
                'tallas_unisex.*'  => 'in:S,M,L,XL,Personalizado',
                'fotos_unisex'     => 'required|array|min:4|max:7',
                'fotos_unisex.*'   => 'image|mimes:jpeg,jpg|max:5120',
            ], [
                'tallas_unisex.required' => 'Debes seleccionar al menos una talla para el traje unisex.',
                'fotos_unisex.min'       => 'El traje unisex exige un mínimo de 4 fotos.',
            ]);

            $unisex = Traje::create([
                'cod_traje_padre'  => null,
                'nom_traje'        => $request->nom_traje_base . ' (Unisex)',
                'fraternidad'      => $request->fraternidad, // 🌟 Sincronizado
                'des_traje'        => $request->des_unisex,
                'pre_alquiler'     => $request->pre_alquiler,
                'color_traje'      => $request->color_traje,
                'genero'           => 'Unisex',
                'cod_tienda_traje' => $tienda->cod_tienda,
                'cod_danza_traje'  => $request->cod_danza_traje,
            ]);
            $this->procesarFotos($request->file('fotos_unisex'), $unisex->cod_traje);
            
            // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Unisex (U)
            foreach ($request->tallas_unisex as $talla) {
                $tallaLimpia = strtoupper($talla);
                $serialU = $prefijoDanza . '-' . $unisex->cod_traje . '-U-' . $tallaLimpia . '-01';
                
                $unisex->unidades()->create([
                    'talla'             => $talla,
                    'nro_serie_interno' => $serialU,
                    'estado_fisico'     => 'Nuevo',
                    'observaciones'     => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                    'disponibilidad'    => true,
                ]);
            }
        }

        return redirect()->route('vendedor.trajes.index')->with('success', '¡Colección e inventario inicial publicados con éxito!');
    }

    public function index()
    {
        $tienda = auth()->user()->tiendas()->first();

        $trajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
                        ->whereNull('cod_traje_padre') 
                        ->withTrashed() 
                        ->with(['imagenes', 'danza', 'unidades', 'varianteFemenina' => function($query) {
                            $query->withTrashed()->with(['imagenes', 'unidades']);
                        }]) 
                        ->latest()
                        ->get();

        return view('vendedor.trajes.index', compact('trajes'));
    }

    public function edit($id)
    {
        $traje = Traje::withTrashed()->with('unidades')->findOrFail($id);
        $danzas = Danza::orderBy('nom_danza', 'asc')->get(); 

        return view('vendedor.trajes.edit', compact('traje', 'danzas'));
    }

    public function update(Request $request, $id)
    {
        $traje = Traje::withTrashed()->with('unidades')->findOrFail($id);

        $request->validate([
            'nom_traje'       => ['required', 'string', 'min:5', 'max:100'],
            'fraternidad'     => ['required', 'string', 'max:255'], // 🌟 Validado en edición
            'pre_alquiler'    => 'required|numeric|min:0',
            'color_traje'     => ['required', 'string', 'max:50'],
            'cod_danza_traje' => 'required|exists:danzas,cod_danza',
            'des_traje'       => 'nullable|string|max:1000',
            'tallas'          => 'required|array|min:1',
            'tallas.*'        => 'in:S,M,L,XL,Personalizado',
            'fotos_nuevas'    => 'nullable|array|max:7',
            'fotos_nuevas.*'  => 'image|mimes:jpg,jpeg|max:5120',
        ]);

        // Actualización explícita y segura mapeando los campos exactos de tu modelo
        $traje->update([
            'nom_traje'       => $request->nom_traje,
            'fraternidad'     => $request->fraternidad, // 🌟 Actualizado
            'pre_alquiler'    => $request->pre_alquiler,
            'color_traje'     => $request->color_traje,
            'des_traje'       => $request->des_traje,
            'cod_danza_traje' => $request->cod_danza_traje,
        ]);

        // Sincronizar Existencia de Tallas
        $tallasActuales = $traje->unidades->pluck('talla')->toArray();
        $tallasSeleccionadas = $request->tallas;

        $tallasAEliminar = array_diff($tallasActuales, $tallasSeleccionadas);
        if (!empty($tallasAEliminar)) {
            $traje->unidades()->whereIn('talla', $tallasAEliminar)->delete();
        }

        $tallasAAgregar = array_diff($tallasSeleccionadas, $tallasActuales);
        foreach ($tallasAAgregar as $nuevaTalla) {
            $traje->unidades()->create([
                'talla'             => $nuevaTalla,
                'nro_serie_interno' => 'TJ-' . $traje->cod_traje . '-' . $nuevaTalla . '-' . rand(1000, 9999),
                'estado_fisico'     => 'Nuevo',
                'disponibilidad'    => true,
            ]);
        }

        // Eliminar fotos seleccionadas
        if ($request->has('fotos_borrar') && !empty($request->fotos_borrar)) {
            $ids = explode(',', $request->fotos_borrar);
            $imagenes = $traje->imagenes()->whereIn('cod_imagen', $ids)->get();
            
            foreach ($imagenes as $img) {
                Storage::disk('public')->delete($img->ruta_img);
                $img->delete();
            }
        }

        // Agregar fotos nuevas
        if ($request->hasFile('fotos_nuevas')) {
            $this->procesarFotos($request->file('fotos_nuevas'), $traje->cod_traje);
        }

        return redirect()->route('vendedor.trajes.index')->with('success', '¡El traje y su inventario se actualizaron con éxito!');
    }

    public function destroy($id)
    {
        $traje = Traje::findOrFail($id);
        $traje->delete(); 
        return back()->with('success', 'El traje ha sido desactivado (borrado lógico)');
    }

    public function restore($id)
    {
        $traje = Traje::withTrashed()->findOrFail($id);
        $traje->restore();
        return back()->with('success', '¡El traje "' . $traje->nom_traje . '" ha vuelto a la vitrina!');
    }

    public function destroyTotal($id)
    {
        $trajePadre = Traje::findOrFail($id);

        if ($trajePadre->varianteFemenina) {
            $trajePadre->varianteFemenina->delete();
        }

        $trajePadre->delete();
        return back()->with('success', '¡La colección completa ha sido desactivada con éxito!');
    }

    public function restoreTotal($id)
    {
        $trajePadre = Traje::withTrashed()->findOrFail($id);

        if ($trajePadre->varianteFemenina()->withTrashed()->exists()) {
            $trajePadre->varianteFemenina()->withTrashed()->first()->restore();
        }

        $trajePadre->restore();
        return back()->with('success', '¡La colección completa ha vuelto a la vitrina exitosamente!');
    }

    private function procesarFotos($fotos, $codTraje)
    {
        foreach ($fotos as $index => $foto) {
            $nombreArchivo = time() . '_' . rand(100, 999) . '_' . $index . '.jpg';
            $ruta = $foto->storeAs('trajes', $nombreArchivo, 'public');
            
            ImagenTraje::create([
                'ruta_img'      => $ruta,
                'cod_traje_img' => $codTraje
            ]);
        }
    }
}
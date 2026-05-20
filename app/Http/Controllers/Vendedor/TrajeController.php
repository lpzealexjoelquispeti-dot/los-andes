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

        $danzas = Danza::all();
        return view('vendedor.trajes.create', compact('danzas'));
    }

    public function store(Request $request) 
{
    // 1. Validar Campos Compartidos Base
    $request->validate([
        'nom_traje_base' => 'required|min:5|max:100',
        'pre_alquiler' => 'required|numeric|min:0',
        'color_traje' => 'required',
        'cod_danza_traje' => 'required|exists:danzas,cod_danza',
        'modalidad' => 'required|in:variantes,unisex',
    ]);

    $tienda = Auth::user()->tiendas()->first();

    // Rescatamos el nombre de la danza para armar el prefijo unificado (ej: REY, TIN, MOR)
    $danzaNombre = \App\Models\Danza::find($request->cod_danza_traje)->nom_danza ?? 'TJ';
    $prefijoDanza = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));

    // ==========================================
    // CASO INTERNO A: MODALIDAD VARIANTES (PAREJA)
    // ==========================================
    if ($request->modalidad === 'variantes') {
        
        $request->validate([
            'des_varon' => 'required|min:10',
            'tallas_varon' => 'required|array|min:1',
            'tallas_varon.*' => 'in:S,M,L,XL,Personalizado',
            'fotos_varon' => 'required|array|min:4|max:7',
            'fotos_varon.*' => 'image|mimes:jpeg,jpg|max:5120',

            'des_mujer' => 'required|min:10',
            'tallas_mujer' => 'required|array|min:1',
            'tallas_mujer.*' => 'in:S,M,L,XL,Personalizado',
            'fotos_mujer' => 'required|array|min:4|max:7',
            'fotos_mujer.*' => 'image|mimes:jpeg,jpg|max:5120',
        ], [
            'tallas_varon.required' => 'Debes marcar al menos una talla para el bloque de varones.',
            'tallas_mujer.required' => 'Debes marcar al menos una talla para el bloque de mujeres.',
            'fotos_varon.min' => 'El bloque de varones exige un mínimo de 4 fotos.',
            'fotos_mujer.min' => 'El bloque de mujeres exige un mínimo de 4 fotos.',
        ]);

        // REGISTRO 1: Traje Masculino (Padre)
        $varon = Traje::create([
            'cod_traje_padre' => null,
            'nom_traje' => $request->nom_traje_base . ' - Varón',
            'des_traje' => $request->des_varon,
            'pre_alquiler' => $request->pre_alquiler,
            'color_traje' => $request->color_traje,
            'genero' => 'Masculino',
            'cod_tienda_traje' => $tienda->cod_tienda,
            'cod_danza_traje' => $request->cod_danza_traje,
        ]);
        $this->procesarFotos($request->file('fotos_varon'), $varon->cod_traje);
        
        // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Varón (M)
        foreach ($request->tallas_varon as $talla) {
            $tallaLimpia = strtoupper($talla);
            $serialM = $prefijoDanza . '-' . $varon->cod_traje . '-M-' . $tallaLimpia . '-01';
            
            $varon->unidades()->create([
                'talla' => $talla,
                'nro_serie_interno' => $serialM,
                'estado_fisico' => 'Nuevo',
                'observaciones' => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                'disponibilidad' => true,
            ]);
        }

        // REGISTRO 2: Traje Femenino (Hijo)
        $mujer = Traje::create([
            'cod_traje_padre' => $varon->cod_traje,
            'nom_traje' => $request->nom_traje_base . ' - Damas',
            'des_traje' => $request->des_mujer,
            'pre_alquiler' => $request->pre_alquiler,
            'color_traje' => $request->color_traje,
            'genero' => 'Femenino',
            'cod_tienda_traje' => $tienda->cod_tienda,
            'cod_danza_traje' => $request->cod_danza_traje,
        ]);
        $this->procesarFotos($request->file('fotos_mujer'), $mujer->cod_traje);
        
        // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Damas (F)
        foreach ($request->tallas_mujer as $talla) {
            $tallaLimpia = strtoupper($talla);
            $serialF = $prefijoDanza . '-' . $mujer->cod_traje . '-F-' . $tallaLimpia . '-01';
            
            $mujer->unidades()->create([
                'talla' => $talla,
                'nro_serie_interno' => $serialF,
                'estado_fisico' => 'Nuevo',
                'observaciones' => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                'disponibilidad' => true,
            ]);
        }

    } else {
        // ==========================================
        // CASO INTERNO B: MODALIDAD UNISEX / ÚNICO
        // ==========================================
        $request->validate([
            'des_unisex' => 'required|min:10',
            'tallas_unisex' => 'required|array|min:1',
            'tallas_unisex.*' => 'in:S,M,L,XL,Personalizado',
            'fotos_unisex' => 'required|array|min:4|max:7',
            'fotos_unisex.*' => 'image|mimes:jpeg,jpg|max:5120',
        ], [
            'tallas_unisex.required' => 'Debes seleccionar al menos una talla para el traje unisex.',
            'fotos_unisex.min' => 'El traje unisex exige un mínimo de 4 fotos.',
        ]);

        $unisex = Traje::create([
            'cod_traje_padre' => null,
            'nom_traje' => $request->nom_traje_base . ' (Unisex)',
            'des_traje' => $request->des_unisex,
            'pre_alquiler' => $request->pre_alquiler,
            'color_traje' => $request->color_traje,
            'genero' => 'Unisex',
            'cod_tienda_traje' => $tienda->cod_tienda,
            'cod_danza_traje' => $request->cod_danza_traje,
        ]);
        $this->procesarFotos($request->file('fotos_unisex'), $unisex->cod_traje);
        
        // FABRICACIÓN DE STOCK INICIAL INTELIGENTE: Unisex (U)
        foreach ($request->tallas_unisex as $talla) {
            $tallaLimpia = strtoupper($talla);
            $serialU = $prefijoDanza . '-' . $unisex->cod_traje . '-U-' . $tallaLimpia . '-01';
            
            $unisex->unidades()->create([
                'talla' => $talla,
                'nro_serie_interno' => $serialU,
                'estado_fisico' => 'Nuevo',
                'observaciones' => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                'disponibilidad' => true,
            ]);
        }
    }

    return redirect()->route('vendedor.trajes.index')->with('success', '¡Colección e inventario inicial publicados con éxito!');
}

    /**
     * Genera automáticamente la primera pieza física (extensión 01) 
     * para cada talla marcada por el usuario con el formato modular unificado.
     */
    private function inicializarInventario(array $tallas, Traje $traje, string $charGenero)
    {
        // 1. Extraemos el nombre de la danza cargado en la relación del modelo
        $danzaNombre = $traje->danza->nom_danza ?? 'TJ';
        $prefijo = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));

        // 2. Iteramos cada una de las tallas marcadas en el formulario
        foreach ($tallas as $talla) {
            $tallaLimpia = strtoupper($talla);

            // Estructuramos el serial maestro inicial: PREFIJO-ID-GÉNERO-TALLA-01
            // Ej: TIN-15-M-XL-01 (Varón) o TIN-15-F-M-01 (Damas)
            $serial = $prefijo . '-' . $traje->cod_traje . '-' . $charGenero . '-' . $tallaLimpia . '-01';

            // Muro preventivo anti-duplicados por consistencia de datos
            $numeroCorrelativo = 1;
            while (\App\Models\InventarioUnidad::where('nro_serie_interno', $serial)->withTrashed()->exists()) {
                $numeroCorrelativo++;
                $extension = str_pad($numeroCorrelativo, 2, '0', STR_PAD_LEFT);
                $serial = $prefijo . '-' . $traje->cod_traje . '-' . $charGenero . '-' . $tallaLimpia . '-' . $extension;
            }

            // Insertamos la prenda física inicial en el perchero
            $traje->unidades()->create([
                'talla' => $talla,
                'nro_serie_interno' => $serial,
                'estado_fisico' => 'Nuevo',
                'observaciones' => 'Prenda maestra inicial generada automáticamente al crear la colección.',
                'disponibilidad' => true,
            ]);
        }
    }

    public function index()
{
    $tienda = auth()->user()->tiendas()->first();

    // Traer SOLO los trajes principales (Padres)
    $trajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
                    ->whereNull('cod_traje_padre') // Filtra nulos reales
                    ->where('cod_traje_padre', '=', null) // Doble seguridad para Eloquent
                    ->withTrashed() 
                    ->with([
                        'imagenes', 
                        'danza', 
                        'unidades', 
                        'varianteFemenina' => function($query) {
                            $query->withTrashed()->with(['imagenes', 'unidades']);
                        }
                    ]) 
                    ->latest()
                    ->get();

    return view('vendedor.trajes.index', compact('trajes'));
}

    public function edit($id)
    {
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
            'tallas' => 'required|array|min:1',
            'tallas.*' => 'in:S,M,L,XL,Personalizado',
            'fotos_nuevas' => 'nullable|array|max:7',
            'fotos_nuevas.*' => 'image|mimes:jpg,jpeg|max:5120',
        ], [
            'nom_traje.regex' => 'El nombre del traje solo debe contener letras.',
            'color_traje.regex' => 'El color solo debe contener letras.',
            'tallas.required' => 'El traje debe tener al menos una talla en inventario.',
        ]);

        $traje->update($request->only([
            'nom_traje', 'pre_alquiler', 'color_traje', 'des_traje', 'cod_danza_traje'
        ]));

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
                'talla' => $nuevaTalla,
                'nro_serie_interno' => 'TJ-' . $traje->cod_traje . '-' . $nuevaTalla . '-' . rand(1000, 9999),
                'estado_fisico' => 'Nuevo',
                'disponibilidad' => true,
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
    // 1. Encontrar el traje principal (Padre)
    $trajePadre = Traje::findOrFail($id);

    // 2. Buscar si tiene una variante (Hijo) y apagarla primero si existe
    if ($trajePadre->varianteFemenina) {
        $trajePadre->varianteFemenina->delete();
    }

    // 3. Apagar el traje principal
    $trajePadre->delete();

    return back()->with('success', '¡La colección completa ha sido desactivada con éxito!');
}
public function restoreTotal($id)
{
    // 1. Encontrar el traje principal (Padre) que está archivado/borrado
    $trajePadre = Traje::withTrashed()->findOrFail($id);

    // 2. Buscar si tiene una variante (Hijo) borrada y devolverla a la vida
    if ($trajePadre->varianteFemenina()->withTrashed()->exists()) {
        $trajePadre->varianteFemenina()->withTrashed()->first()->restore();
    }

    // 3. Devolver a la vida al traje principal
    $trajePadre->restore();

    return back()->with('success', '¡La colección completa ha vuelto a la vitrina exitosamente!');
}
    /**
     * Métodos Auxiliares Privados para evitar duplicación de código
     */
    private function procesarFotos($fotos, $codTraje)
    {
        foreach ($fotos as $index => $foto) {
            $nombreArchivo = time() . '_' . rand(100, 999) . '_' . $index . '.jpg';
            $ruta = $foto->storeAs('trajes', $nombreArchivo, 'public');
            
            ImagenTraje::create([
                'ruta_img' => $ruta,
                'cod_traje_img' => $codTraje
            ]);
        }
    }

    
}
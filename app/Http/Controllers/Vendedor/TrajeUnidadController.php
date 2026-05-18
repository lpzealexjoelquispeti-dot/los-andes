<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Traje;
use App\Models\InventarioUnidad;

class TrajeUnidadController extends Controller
{
    /**
     * Muestra el panel matriz de inventario para un traje específico,
     * incluyendo el carrusel para alternar con los demás trajes.
     */
    public function index($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')->with('error', 'Tienda no aprobada.');
        }

        // 1. Traemos el traje activo con todo su desglose físico
        $trajeActivo = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with(['unidades' => function ($q) { $q->withTrashed(); }, 'danza'])
            ->findOrFail($cod_traje);

        // 2. Traemos TODOS los trajes de la tienda para alimentar el carrusel superior
        $todosLosTrajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with(['imagenes', 'unidades' => function ($q) { $q->withTrashed(); }])
            ->latest()
            ->get();

        // 3. Prendas dañadas del traje activo para el panel de alertas del index
        $unidadesDanadas = InventarioUnidad::whereIn('cod_traje_base', $todosLosTrajes->pluck('cod_traje'))
            ->whereIn('estado_fisico', ['Desgastado', 'En Reparación'])
            ->with('traje')
            ->get();

        return view('vendedor.unidades.index', compact('trajeActivo', 'todosLosTrajes', 'unidadesDanadas'));
    }

    /**
     * ── NUEVA VISTA SEPARADA: Centro de Control de Daños y Mantenimiento ──
     * Ruta: GET /vendedor/trajes/{traje}/unidades/danos
     * Name: vendedor.trajes.unidades.danos
     */
    public function danos($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')->with('error', 'Tienda no aprobada.');
        }

        // Mismo traje activo con sus unidades (mismo patrón que index)
        $trajeActivo = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with([
                'unidades' => function ($q) { $q->withTrashed(); },
                'imagenes',
                'danza',
            ])
            ->findOrFail($cod_traje);

        // Solo las prendas dañadas de ESTE traje específico
        $unidadesDanadas = InventarioUnidad::where('cod_traje_base', $cod_traje)
            ->whereIn('estado_fisico', ['Desgastado', 'En Reparación'])
            ->whereNull('deleted_at')
            ->get();

        return view('vendedor.unidades.danos', compact('trajeActivo', 'unidadesDanadas'));
    }

    public function create($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        $traje = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with('imagenes')
            ->findOrFail($cod_traje);

        return view('vendedor.unidades.create', compact('traje'));
    }

    /**
     * Guarda la unidad física en la base de datos.
     */
    public function store(Request $request, $cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        $traje = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with('danza')
            ->findOrFail($cod_traje);

        $request->validate([
            'talla'         => 'required|in:S,M,L,XL,Personalizado',
            'cantidad'      => 'required|integer|min:1|max:50',
            'estado_fisico' => 'required|in:Nuevo,Buen Estado,Desgastado,En Reparación',
        ]);

        $cantidad   = $request->cantidad;
        $tallaLimpia = strtoupper($request->talla);

        $danzaNombre = $traje->danza->nom_danza ?? 'TJ';
        $prefijo     = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));

        $conteoActual = InventarioUnidad::where('cod_traje_base', $traje->cod_traje)
            ->where('talla', $request->talla)
            ->withTrashed()
            ->count();

        for ($i = 0; $i < $cantidad; $i++) {

            $numeroCorrelativo = $conteoActual + $i + 1;
            $extension         = str_pad($numeroCorrelativo, 2, '0', STR_PAD_LEFT);
            $serial            = $prefijo . '-' . $traje->cod_traje . '-' . $tallaLimpia . '-' . $extension;

            while (InventarioUnidad::where('nro_serie_interno', $serial)->withTrashed()->exists()) {
                $numeroCorrelativo++;
                $extension = str_pad($numeroCorrelativo, 2, '0', STR_PAD_LEFT);
                $serial    = $prefijo . '-' . $traje->cod_traje . '-' . $tallaLimpia . '-' . $extension;
            }

            $traje->unidades()->create([
                'talla'             => $request->talla,
                'nro_serie_interno' => $serial,
                'estado_fisico'     => $request->estado_fisico,
                'observaciones'     => null,
                'disponibilidad'    => true,
            ]);
        }

        return redirect()->route('vendedor.trajes.unidades.index', $traje->cod_traje)
            ->with('success', '¡Se han fabricado ' . $cantidad . ' piezas físicas en Talla ' . $tallaLimpia . ' con el prefijo [' . $prefijo . '] con éxito!');
    }

    /**
     * Actualiza cualquier atributo de una pieza física (Talla, Código, Estado)
     */
    /**
     * Actualiza cualquier atributo de una pieza física (Talla, Estado, Observaciones de Daño)
     */
    /**
     * Actualiza cualquier atributo de una pieza física (Talla, Estado, Observaciones de Daño)
     */
    public function update(Request $request, $id)
    {
        $unidad = InventarioUnidad::findOrFail($id);

        // CAMBIO MÁGICO: Cambiamos 'required' por 'sometimes|required'
        $request->validate([
            'talla'         => 'sometimes|required|in:S,M,L,XL,Personalizado',
            'estado_fisico' => 'required|in:Nuevo,Buen Estado,Desgastado,En Reparación',
            'observaciones' => 'nullable|string' 
        ]);

        // Nuevo o Buen Estado → disponible para alquiler; cualquier daño → fuera de vitrina
        $disponibilidad = in_array($request->estado_fisico, ['Nuevo', 'Buen Estado']);

        // Armamos el payload base
        $datosUpdate = [
            'estado_fisico'  => $request->estado_fisico,
            'disponibilidad' => $disponibilidad,
            'observaciones'  => $request->observaciones
        ];

        // Solo si la petición incluye la talla, la inyectamos al update
        if ($request->has('talla')) {
            $datosUpdate['talla'] = $request->talla;
        }

        $unidad->update($datosUpdate);

        return redirect()->back()->with('success', '¡Prenda ' . $unidad->nro_serie_interno . ' actualizada correctamente!');
    }

    /**
     * Soft Delete de una prenda física del inventario
     */
    public function destroy($id)
    {
        $unidad  = InventarioUnidad::findOrFail($id);
        $codigo  = $unidad->nro_serie_interno;

        $unidad->delete();

        return redirect()->back()->with('success', '¡La prenda ' . $codigo . ' fue dada de baja definitivamente del inventario!');
    }

    /**
     * Restaura una prenda dada de baja lógica y la devuelve al stock activo.
     * Usa withTrashed() para poder encontrarla aunque tenga deleted_at.
     */
    public function restore($id)
    {
        $unidad = InventarioUnidad::withTrashed()->findOrFail($id);
        $codigo = $unidad->nro_serie_interno;

        $unidad->restore(); // Limpia el deleted_at

        // La devolvemos a "Buen Estado" y disponible para alquiler
        $unidad->update([
            'estado_fisico'  => 'Buen Estado',
            'disponibilidad' => true,
        ]);

        return redirect()->back()->with('success', '¡La prenda ' . $codigo . ' fue reactivada y devuelta al stock con éxito!');
    }
}
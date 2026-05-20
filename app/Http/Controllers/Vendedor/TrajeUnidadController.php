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
     * incluyendo el carrusel para alternar con los demás trajes de forma unificada.
     */
    public function index($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')->with('error', 'Tienda no aprobada.');
        }

        // 1. Redirección de seguridad: Si entran con el ID de un hijo (Damas),
        // lo movemos automáticamente al ID del Padre para que la vista unificada no se rompa.
        $trajeVerificacion = Traje::withTrashed()->findOrFail($cod_traje);
        if ($trajeVerificacion->cod_traje_padre !== null) {
            return redirect()->route('vendedor.trajes.unidades.index', $trajeVerificacion->cod_traje_padre);
        }

        // 2. Traemos el traje activo con todo su desglose físico (Padre + Relación del Hijo con stock)
        $trajeActivo = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with([
                'danza',
                'unidades' => function ($q) { $q->withTrashed(); },
                'varianteFemenina' => function ($q) { 
                    $q->withTrashed()->with(['unidades' => function ($sq) { $sq->withTrashed(); }]); 
                }
            ])
            ->findOrFail($cod_traje);

        // 3. CARRUSEL SUPERIOR LIMPIO: Traemos SOLO los trajes principales (whereNull)
        $todosLosTrajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->whereNull('cod_traje_padre') 
            ->withTrashed()
            ->with([
                'imagenes', 
                'danza',
                'unidades' => function ($q) { $q->withTrashed(); },
                'varianteFemenina' => function ($q) { $q->withTrashed()->with('unidades'); }
            ])
            ->latest()
            ->get();

        // 4. Prendas dañadas global: Recopilamos los IDs de padres e hijos de toda la tienda
        $idsTrajesTienda = Traje::where('cod_tienda_traje', $tienda->cod_tienda)->withTrashed()->pluck('cod_traje');

        $unidadesDanadas = InventarioUnidad::whereIn('cod_traje_base', $idsTrajesTienda)
            ->whereIn('estado_fisico', ['Desgastado', 'En Reparación'])
            ->with('traje')
            ->get();

        return view('vendedor.unidades.index', compact('trajeActivo', 'todosLosTrajes', 'unidadesDanadas'));
    }

    /**
     * Centro de Control de Daños integrado para el bloque completo (Varón + Damas)
     */
    public function danos($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')->with('error', 'Tienda no aprobada.');
        }

        // Buscamos el traje base maestro
        $trajeActivo = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with(['imagenes', 'danza', 'varianteFemenina'])
            ->findOrFail($cod_traje);

        // Agrupamos los IDs del bloque completo (Padre e Hijo) para listar los daños unificados
        $idsBloque = [$trajeActivo->cod_traje];
        if ($trajeActivo->varianteFemenina) {
            $idsBloque[] = $trajeActivo->varianteFemenina->cod_traje;
        }

        $unidadesDanadas = InventarioUnidad::whereIn('cod_traje_base', $idsBloque)
            ->whereIn('estado_fisico', ['Desgastado', 'En Reparación'])
            ->whereNull('deleted_at')
            ->with('traje')
            ->get();

        return view('vendedor.unidades.danos', compact('trajeActivo', 'unidadesDanadas'));
    }

    public function create($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        // Carga la instancia exacta de la variante seleccionada (Sea Padre o Hijo)
        $traje = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with('imagenes')
            ->findOrFail($cod_traje);

        return view('vendedor.unidades.create', compact('traje'));
    }

    /**
     * Guarda la unidad física calculando el identificador inteligente de Género
     */
    public function store(Request $request, $cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        // Trae la variante exacta donde se está agregando el stock
        $traje = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with('danza')
            ->findOrFail($cod_traje);

        $request->validate([
            'talla'         => 'required|in:S,M,L,XL,Personalizado',
            'cantidad'      => 'required|integer|min:1|max:50',
            'estado_fisico' => 'required|in:Nuevo,Buen Estado,Desgastado,En Reparación',
        ]);

        $cantidad    = $request->cantidad;
        $tallaLimpia = strtoupper($request->talla);

        // 1. Obtener Prefijo Folclórico de la Danza
        $danzaNombre = $traje->danza->nom_danza ?? 'TJ';
        $prefijo     = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));

        // 2. Determinar Identificador de Género (M = Masculino, F = Femenino)
        $identificadorGenero = ($traje->genero === 'Femenino' || $traje->cod_traje_padre !== null) ? 'F' : 'M';

        // 3. Contar existencias previas de esta talla en este traje específico para el correlativo
        $conteoActual = InventarioUnidad::where('cod_traje_base', $traje->cod_traje)
            ->where('talla', $request->talla)
            ->withTrashed()
            ->count();

        for ($i = 0; $i < $cantidad; $i++) {

            $numeroCorrelativo = $conteoActual + $i + 1;
            $extension         = str_pad($numeroCorrelativo, 2, '0', STR_PAD_LEFT);
            
            // NUEVA ESTRUCTURA INCORPORADA: PREFIJO-ID-GÉNERO-TALLA-CORRELATIVO
            $serial            = $prefijo . '-' . $traje->cod_traje . '-' . $identificadorGenero . '-' . $tallaLimpia . '-' . $extension;

            // Muro anti-duplicados por concurrencia
            while (InventarioUnidad::where('nro_serie_interno', $serial)->withTrashed()->exists()) {
                $numeroCorrelativo++;
                $extension = str_pad($numeroCorrelativo, 2, '0', STR_PAD_LEFT);
                $serial    = $prefijo . '-' . $traje->cod_traje . '-' . $identificadorGenero . '-' . $tallaLimpia . '-' . $extension;
            }

            $traje->unidades()->create([
                'talla'             => $request->talla,
                'nro_serie_interno' => $serial,
                'estado_fisico'     => $request->estado_fisico,
                'observaciones'     => null,
                'disponibilidad'    => true,
            ]);
        }

        // Redirección inteligente: Siempre volvemos al panel del Padre para mantener la consistencia visual
        $routeId = $traje->cod_traje_padre ?? $traje->cod_traje;

        return redirect()->route('vendedor.trajes.unidades.index', $routeId)
            ->with('success', '¡Se han fabricado ' . $cantidad . ' piezas físicas en Talla ' . $tallaLimpia . ' con el código de barra [' . $identificadorGenero . '] con éxito!');
    }

    /**
     * Actualiza atributos de una pieza física (Talla, Estado, Observaciones de Daño)
     */
    public function update(Request $request, $id)
    {
        $unidad = InventarioUnidad::findOrFail($id);

        $request->validate([
            'talla'         => 'sometimes|required|in:S,M,L,XL,Personalizado',
            'estado_fisico' => 'required|in:Nuevo,Buen Estado,Desgastado,En Reparación',
            'observaciones' => 'nullable|string' 
        ]);

        // Si se manda a reparación o está desgastado se le quita la disponibilidad pública de inmediato
        $disponibilidad = in_array($request->estado_fisico, ['Nuevo', 'Buen Estado']);

        $datosUpdate = [
            'estado_fisico'  => $request->estado_fisico,
            'disponibilidad' => $disponibilidad,
            'observaciones'  => $request->observaciones
        ];

        if ($request->has('talla')) {
            $datosUpdate['talla'] = $request->talla;
        }

        $unidad->update($datosUpdate);

        // DETERMINACIÓN DE RETORNO LIMPIO PARA EL CENTRO DE DAÑOS
        // Recuperamos el traje dueño de la prenda física para encontrar la raíz de la colección
        $trajeBase = Traje::withTrashed()->findOrFail($unidad->cod_traje_base);
        $idPadre = $trajeBase->cod_traje_padre ?? $trajeBase->cod_traje;

        // Forzamos la redirección explícita pasándole la ID del Padre de la fraternidad.
        // Esto refresca los arrays JSON de Alpine.js de forma obligatoria en la vista de daños.
        return redirect()->route('vendedor.trajes.unidades.danos', $idPadre)
            ->with('success', '¡Historial clínico de la prenda ' . $unidad->nro_serie_interno . ' procesado correctamente en PostgreSQL!');
    }

    /**
     * Soft Delete de una prenda física del inventario
     */
    public function destroy($id)
    {
        $unidad  = InventarioUnidad::findOrFail($id);
        $codigo  = $unidad->nro_serie_interno;

        $unidad->delete();

        return redirect()->back()->with('success', '¡La prenda ' . $codigo . ' fue dada de baja del inventario!');
    }

    /**
     * Restaura una prenda dada de baja lógica
     */
    public function restore($id)
    {
        $unidad = InventarioUnidad::withTrashed()->findOrFail($id);
        $codigo = $unidad->nro_serie_interno;

        $unidad->restore();

        $unidad->update([
            'estado_fisico'  => 'Buen Estado',
            'disponibilidad' => true,
        ]);

        return redirect()->back()->with('success', '¡La prenda ' . $codigo . ' fue reactivada exitosamente!');
    }
}
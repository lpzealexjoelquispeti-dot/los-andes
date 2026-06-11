<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traje;
use App\Models\Tesauro;
use App\Models\BusquedaLog;

class TrajeController extends Controller
{
    public function index(Request $request)
{
    $query  = $request->input('q');
    $filtroPuesta = $request->input('puesta');
    
    $trajes = Traje::query()
        ->whereNull('cod_traje_padre')
        ->with(['danza', 'tienda.diseno', 'imagenes', 'unidades', 'varianteFemenina.imagenes']);

    // Filtro por nivel de uso (primera/segunda/tercera puesta)
    // puesta: 1|2|3|all
    if ($filtroPuesta && $filtroPuesta !== 'all') {
        $trajes->where('nivel_uso_alquileres', (int) $filtroPuesta - 1);
    }

    $terminosRelacionados = collect();
    $tipoTraduccion       = null;

    if ($query) {

        // ══════════════════════════════════════════════
        // CAPA 0: Búsqueda masiva — término que mapea a varias danzas
        // Ej: "pollera" → Morenada, Caporales, Tinku, etc.
        // ══════════════════════════════════════════════
        $coincidencias = Tesauro::whereRaw('LOWER(termino_usuario) LIKE LOWER(?)', ["%{$query}%"])
            ->pluck('cod_danza_ref')
            ->unique()
            ->filter();

        if ($coincidencias->count() > 1) {
            $trajes->whereIn('cod_danza_traje', $coincidencias);

            $terminosRelacionados = Tesauro::whereIn('cod_danza_ref', $coincidencias)
                ->pluck('termino_usuario')
                ->unique();

            BusquedaLog::create([
                'termino_buscado' => $query,
                'tipo_resultado'  => 'masiva',
                'cod_danza_ref'   => null,
                'user_id'         => auth()->id(),
            ]);

        // ══════════════════════════════════════════════
        // CAPA 1: Tesauro exacto — una sola danza
        // ══════════════════════════════════════════════
        } elseif ($coincidencias->count() === 1) {
            $traduccion = Tesauro::whereRaw('LOWER(termino_usuario) = LOWER(?)', [$query])->first();

            if ($traduccion) {
                $traduccion->increment('veces_buscado');
                $tipoTraduccion = $traduccion->tipo;

                $terminosRelacionados = Tesauro::where('cod_danza_ref', $traduccion->cod_danza_ref)
                    ->where('cod_termino', '!=', $traduccion->cod_termino)
                    ->pluck('termino_usuario');

                BusquedaLog::create([
                    'termino_buscado' => $query,
                    'tipo_resultado'  => 'tesauro',
                    'cod_danza_ref'   => $traduccion->cod_danza_ref,
                    'user_id'         => auth()->id(),
                ]);
            }

            $trajes->whereIn('cod_danza_traje', $coincidencias);

        } else {
            // ══════════════════════════════════════════════
            // CAPA 2: Danza directa
            // ══════════════════════════════════════════════
            $danzaEncontrada = \App\Models\Danza::whereRaw('LOWER(nom_danza) LIKE LOWER(?)', ["%{$query}%"])->first();

            if ($danzaEncontrada) {
                $trajes->where('cod_danza_traje', $danzaEncontrada->cod_danza);

                $terminosRelacionados = Tesauro::where('cod_danza_ref', $danzaEncontrada->cod_danza)
                    ->pluck('termino_usuario');

                BusquedaLog::create([
                    'termino_buscado' => $query,
                    'tipo_resultado'  => 'danza',
                    'cod_danza_ref'   => $danzaEncontrada->cod_danza,
                    'user_id'         => auth()->id(),
                ]);

            } else {
                // ══════════════════════════════════════════════
                // CAPA 3: Texto libre
                // ══════════════════════════════════════════════
                $trajes->where(function($q) use ($query) {
                    $q->where('nom_traje', 'ilike', "%{$query}%")
                      ->orWhereHas('danza', fn($d) => $d->where('nom_danza', 'ilike', "%{$query}%"))
                      ->orWhereHas('varianteFemenina', fn($vf) => $vf->where('nom_traje', 'ilike', "%{$query}%"));
                });

                $hayResultados = $trajes->count() > 0;

                BusquedaLog::create([
                    'termino_buscado' => $query,
                    'tipo_resultado'  => $hayResultados ? 'texto' : 'sin_resultado',
                    'cod_danza_ref'   => null,
                    'user_id'         => auth()->id(),
                ]);
            }
        }
    }

    $populares = Tesauro::orderByDesc('veces_buscado')
        ->where('veces_buscado', '>', 0)
        ->limit(6)
        ->pluck('termino_usuario');

    // Ordenar por “nivel de uso” (menos usado = más nuevo primero)
    // Si no existe aún el campo o viene en NULL, lo tratamos como 0.
    $trajes = $trajes
        ->orderByRaw('COALESCE(nivel_uso_alquileres, 0) ASC')
        ->latest()
        ->paginate(12);

    // Campos calculados de precio efectivo según puesta
    $trajes->getCollection()->transform(function ($t) {
        $nivel = (int) ($t->nivel_uso_alquileres ?? 0);

        // Mapeo 0-based:
        // nivel 0 => 1ra (0% desc)
        // nivel 1..3 => 2da-4ta (-15%)
        // nivel 4+ => 3ra+ (desde 5ta) (-20%)
        $desc = 0;
        if ($nivel >= 1 && $nivel <= 3) {
            $desc = 0.15;
        } elseif ($nivel >= 4) {
            $desc = 0.20;
        }

        $t->precio_efectivo = (float) round(((1 - $desc) * (float) $t->pre_alquiler), 2);
        return $t;
    });


    return view('public.catalogo.index', compact(
        'trajes',
        'query',
        'filtroPuesta',
        'terminosRelacionados',
        'tipoTraduccion',
        'populares'
    ));
}

    /**
     * Endpoint para autocompletado en tiempo real de tu buscador
     */
    public function autocomplete(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $terminos = Tesauro::whereRaw('LOWER(termino_usuario) LIKE LOWER(?)', ["%{$q}%"])
            ->with('danza:cod_danza,nom_danza')
            ->limit(6)
            ->get(['cod_termino', 'termino_usuario', 'cod_danza_ref', 'tipo']);

        return response()->json($terminos);
    }

    /**
     * Muestra la vista de detalle única de un traje (si acceden por ruta directa)
     */
    public function show($id)
    {
        // Cargamos la estructura completa unificada por si entran directo desde una URL vieja o QR
        $traje = Traje::with(['danza', 'tienda.diseno', 'imagenes', 'unidades', 'varianteFemenina.imagenes'])->findOrFail($id);
        
        // Redirección de consistencia: Si es un hijo, lo movemos al padre para mantener el catálogo unificado
        if ($traje->cod_traje_padre !== null) {
            return redirect()->route('public.trajes.show', $traje->cod_traje_padre);
        }

        return redirect()->route('public.catalogo.index', ['q' => $traje->nom_traje]);
    }
    public function reservar(\App\Models\InventarioUnidad $unidad)
{
    abort_if(! $unidad->disponibilidad, 404, 'Esta unidad ya no está disponible.');
    
    $unidad->load(['traje.tienda.diseno', 'traje.imagenes', 'traje.danza']);

    // ✅ Procesamos el str_replace aquí en el controlador
    $trajeNombre = str_replace([' - Varón', ' - Dama'], '', $unidad->traje->nom_traje);
    $tienda      = $unidad->traje->tienda;
    
    return view('public.catalogo.reservar', compact('unidad', 'trajeNombre', 'tienda'));
}
}

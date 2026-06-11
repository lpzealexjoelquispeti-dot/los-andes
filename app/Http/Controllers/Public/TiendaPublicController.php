<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use App\Models\Tesauro;
use Illuminate\Http\Request;
use App\Models\BusquedaLog;

class TiendaPublicController extends Controller
{
    /**
     * Muestra el directorio global de tiendas virtuales aprobadas
     */
    public function index()
    {
        $tiendas = Tienda::has('diseno')->with('diseno')->get();
        return view('public.tiendas.index', compact('tiendas'));
    }

    /**
     * Muestra la vitrina exclusiva de una tienda filtrando y unificando por género
     */
    public function show($id)
{
    $tienda = Tienda::with(['diseno'])->findOrFail($id);

    $query                = request('q');
    
    $trajes = $tienda->trajes()
        ->whereNull('cod_traje_padre')
        ->with(['imagenes', 'danza', 'unidades', 'varianteFemenina.imagenes', 'varianteFemenina.unidades']);

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

    $trajes = $trajes->get();

    return view('public.tiendas.show', compact(
        'tienda',
        'trajes',
        'query',
        'terminosRelacionados',
        'tipoTraduccion',
        'populares'
    ));
}
    
}
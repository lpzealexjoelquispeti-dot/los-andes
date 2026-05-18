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
        $trajes = Traje::query()->with(['danza', 'tienda.diseno', 'imagenes', 'unidades']);

        $terminosRelacionados = collect();
        $tipoTraduccion       = null;

        if ($query) {

            // CAPA 1: Tesauro exacto
            $traduccion = Tesauro::whereRaw('LOWER(termino_usuario) = LOWER(?)', [$query])->first();

            if ($traduccion) {
                $traduccion->increment('veces_buscado');
                $trajes->where('cod_danza_traje', $traduccion->cod_danza_ref);
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

            } else {
                // CAPA 2: Danza directa
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
                    // CAPA 3: Texto libre — detectar si hay resultados
                    $trajes->where(function($q) use ($query) {
                        $q->where('nom_traje', 'ilike', "%{$query}%")
                          ->orWhereHas('danza', fn($d) => $d->where('nom_danza', 'ilike', "%{$query}%"));
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

        $trajes = $trajes->latest()->paginate(12);

        return view('public.catalogo.index', compact(
            'trajes',
            'query',
            'terminosRelacionados',
            'tipoTraduccion',
            'populares'
        ));
    }

    // Endpoint para autocompletado
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

    public function show($id)
    {
        $traje = Traje::with(['danza', 'tienda.diseno', 'imagenes', 'unidades'])->findOrFail($id);
        return view('public.catalogo.show', compact('traje'));
    }
}
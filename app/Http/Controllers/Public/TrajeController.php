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
        
        // 1. CARGA EN CASCADA UNIFICADA: Solo traemos trajes principales (Padres)
        // E inyectamos de golpe la variante femenina con sus respectivas imágenes para el modal dinámico
        $trajes = Traje::query()
            ->whereNull('cod_traje_padre')
            ->with(['danza', 'tienda.diseno', 'imagenes', 'unidades', 'varianteFemenina.imagenes']);

        $terminosRelacionados = collect();
        $tipoTraduccion       = null;

        if ($query) {

            // CAPA 1: Tesauro exacto (Mantiene tu inteligencia indexada)
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
                    // CAPA 3: Texto libre — Inteligencia Multi-Género
                    // Buscamos en el padre, en su danza, O en el nombre de su variante de damas para no perder conversiones
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

        // Recuperamos los términos más populares del tesauro
        $populares = Tesauro::orderByDesc('veces_buscado')
            ->where('veces_buscado', '>', 0)
            ->limit(6)
            ->pluck('termino_usuario');

        // Paginamos el lote de forma limpia
        $trajes = $trajes->latest()->paginate(12);

        return view('public.catalogo.index', compact(
            'trajes',
            'query',
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
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traje;
use App\Models\Tesauro;

class TrajeController extends Controller
{
   public function index(Request $request)
{
    $query  = $request->input('q');
    
    // CAMBIO CRUCIAL: Añadimos 'unidades' al Eager Loading inicial
    $trajes = Traje::query()->with(['danza', 'tienda.diseno', 'imagenes', 'unidades']);

    // Variables para la vista
    $terminosRelacionados = collect();
    $tipoTraduccion       = null;

    if ($query) {

        // CAPA 1: Consultamos el Tesauro (insensible a mayúsculas)
        $traduccion = Tesauro::whereRaw('LOWER(termino_usuario) = LOWER(?)', [$query])->first();

        if ($traduccion) {
            // ── NIVEL TESAURO (Puntos 4 y 5) ──
            // Registrar frecuencia de búsqueda (Punto 7)
            $traduccion->increment('veces_buscado');

            // Filtrar trajes por la danza referenciada
            $trajes->where('cod_danza_traje', $traduccion->cod_danza_ref);

            // Guardar tipo para mostrarlo en la vista
            $tipoTraduccion = $traduccion->tipo;

            // Términos relacionados de la misma danza (Punto 4)
            $terminosRelacionados = Tesauro::where('cod_danza_ref', $traduccion->cod_danza_ref)
                ->where('cod_termino', '!=', $traduccion->cod_termino)
                ->pluck('termino_usuario');

        } else {
            // ── NIVEL TEXTO ──

            // Punto 3: Verificar si coincide con una danza directamente (relación jerárquica)
            $danzaEncontrada = \App\Models\Danza::whereRaw('LOWER(nom_danza) LIKE LOWER(?)', ["%{$query}%"])->first();

            if ($danzaEncontrada) {
                // Filtrar trajes de esa danza
                $trajes->where('cod_danza_traje', $danzaEncontrada->cod_danza);

                // Mostrar términos del tesauro como subcategorías clicables (Punto 3)
                $terminosRelacionados = Tesauro::where('cod_danza_ref', $danzaEncontrada->cod_danza)
                    ->pluck('termino_usuario');
            } else {
                // Búsqueda normal por nombre de traje o danza
                $trajes->where(function($q) use ($query) {
                    $q->where('nom_traje', 'ilike', "%{$query}%")
                      ->orWhereHas('danza', fn($d) => $d->where('nom_danza', 'ilike', "%{$query}%"));
                });
            }
        }
    }

    // Términos más buscados (Punto 7)
    $populares = Tesauro::orderByDesc('veces_buscado')
        ->where('veces_buscado', '>', 0)
        ->limit(6)
        ->pluck('termino_usuario');

    // Mantenemos la paginación limpia de Laravel
    $trajes = $trajes->latest()->paginate(12);

    return view('public.catalogo.index', compact(
        'trajes',
        'query',
        'terminosRelacionados',
        'tipoTraduccion',
        'populares'
    ));
}
    // Endpoint para autocompletado (Punto 2)
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
    // Añadimos 'unidades' al Eager Loading para que la vista reciba las tallas
    $traje = Traje::with(['danza', 'tienda.diseno', 'imagenes', 'unidades'])->findOrFail($id);
    
    return view('public.catalogo.show', compact('traje'));
}
}
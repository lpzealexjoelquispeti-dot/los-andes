<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use App\Models\Tesauro;
use Illuminate\Http\Request;

class TiendaPublicController extends Controller
{
    public function index()
    {
        $tiendas = Tienda::has('diseno')->with('diseno')->get();
        return view('public.tiendas.index', compact('tiendas'));
    }

    public function show($id)
    {
        $tienda = Tienda::with(['diseno'])->findOrFail($id);

        $query                = request('q');
        $trajes               = $tienda->trajes()->with(['imagenes', 'danza']);
        $terminosRelacionados = collect();
        $tipoTraduccion       = null;

        if ($query) {

            // CAPA 1: Tesauro exacto
            $traduccion = Tesauro::whereRaw('LOWER(termino_usuario) = LOWER(?)', [$query])->first();

            if ($traduccion) {
                // Registrar frecuencia
                $traduccion->increment('veces_buscado');

                $trajes->where('cod_danza_traje', $traduccion->cod_danza_ref);
                $tipoTraduccion = $traduccion->tipo;

                // Términos relacionados de la misma danza
                $terminosRelacionados = Tesauro::where('cod_danza_ref', $traduccion->cod_danza_ref)
                    ->where('cod_termino', '!=', $traduccion->cod_termino)
                    ->pluck('termino_usuario');

            } else {
                // CAPA 2: Danza directa (jerárquica)
                $danzaEncontrada = \App\Models\Danza::whereRaw('LOWER(nom_danza) LIKE LOWER(?)', ["%{$query}%"])->first();

                if ($danzaEncontrada) {
                    $trajes->where('cod_danza_traje', $danzaEncontrada->cod_danza);

                    $terminosRelacionados = Tesauro::where('cod_danza_ref', $danzaEncontrada->cod_danza)
                        ->pluck('termino_usuario');
                } else {
                    // CAPA 3: Texto libre
                    $trajes->where(function($q) use ($query) {
                        $q->where('nom_traje', 'ilike', "%{$query}%")
                          ->orWhereHas('danza', fn($d) => $d->where('nom_danza', 'ilike', "%{$query}%"));
                    });
                }
            }
        }

        // Populares de esta tienda (términos buscados frecuentemente)
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
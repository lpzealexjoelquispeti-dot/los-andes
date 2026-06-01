<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Danza;
use App\Models\InventarioUnidad;
use App\Models\Traje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InformeEstadisticoController extends Controller
{
    public function index(Request $request)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (! $tienda || ! $tienda->est_tie) {
            return redirect()
                ->route('vendedor.dashboard')
                ->with('error', 'Necesitas una tienda aprobada para ver los reportes.');
        }

        $trajesTienda = Traje::where('cod_tienda_traje', $tienda->cod_tienda);

        $trajesPopulares = (clone $trajesTienda)
            ->withCount('unidades')
            ->orderBy('unidades_count', 'desc')
            ->take(5)
            ->get();

        $estadoInventario = DB::table('inventario_unidades')
            ->join('trajes', 'inventario_unidades.cod_traje_base', '=', 'trajes.cod_traje')
            ->where('trajes.cod_tienda_traje', $tienda->cod_tienda)
            ->whereNull('inventario_unidades.deleted_at')
            ->select('inventario_unidades.estado_fisico as estado_unidad', DB::raw('count(*) as total'))
            ->groupBy('inventario_unidades.estado_fisico')
            ->get();

        $totalTrajesMaestros = (clone $trajesTienda)
            ->whereNull('cod_traje_padre')
            ->count();

        $trajesConVariante = (clone $trajesTienda)
            ->whereNull('cod_traje_padre')
            ->whereHas('varianteFemenina')
            ->count();

        $colorTienda = $tienda->diseno->color_primario ?? '#16a34a';

        $unidadesDisponibles = DB::table('inventario_unidades')
            ->join('trajes', 'inventario_unidades.cod_traje_base', '=', 'trajes.cod_traje')
            ->where('trajes.cod_tienda_traje', $tienda->cod_tienda)
            ->whereNull('inventario_unidades.deleted_at')
            ->where('inventario_unidades.disponibilidad', true)
            ->count();

        $trajes = (clone $trajesTienda)
            ->orderBy('nom_traje')
            ->get();

        $danzasDisponibles = Danza::whereIn(
            'cod_danza',
            (clone $trajesTienda)->pluck('cod_danza_traje')->filter()->unique()
        )
            ->orderBy('nom_danza')
            ->get();

        $unidadesQuery = InventarioUnidad::with(['traje.danza'])
            ->whereHas('traje', function ($query) use ($tienda) {
                $query->where('cod_tienda_traje', $tienda->cod_tienda);
            });

        if ($request->filled('cod_traje')) {
            $unidadesQuery->where('cod_traje_base', $request->cod_traje);
        }

        $busqueda = $request->input('busqueda', $request->input('busqueda_original'));

        if ($busqueda) {
            $unidadesQuery->whereHas('traje', function ($query) use ($busqueda) {
                $query->where('nom_traje', 'like', "%{$busqueda}%");
            });
        }

        if ($request->filled('danza')) {
            $unidadesQuery->whereHas('traje', function ($query) use ($request) {
                $query->where('cod_danza_traje', $request->danza);
            });
        }

        if ($request->filled('estado')) {
            $unidadesQuery->where('estado_fisico', $request->estado);
        }

        $unidadesFiltradas = $unidadesQuery
            ->orderBy('cod_unidad')
            ->paginate(15)
            ->withQueryString();

        return view('vendedor.informes.index', compact(
            'tienda',
            'trajesPopulares',
            'estadoInventario',
            'totalTrajesMaestros',
            'trajesConVariante',
            'colorTienda',
            'unidadesDisponibles',
            'trajes',
            'danzasDisponibles',
            'unidadesFiltradas'
        ));
    }
}

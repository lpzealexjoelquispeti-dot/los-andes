<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Sirve para leer los filtros que elija el usuario
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendedor\Traje;
use App\Models\Vendedor\TrajeUnidad; 

class InformeEstadisticoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener la tienda del vendedor actual
        $tienda = Auth::user()->tienda; 
        
        if (!$tienda) {
            return redirect()->back()->with('error', 'No tienes una tienda asignada.');
        }

        // --- INFORME 1: ROTACIÓN Y POPULARIDAD ---
        $trajesPopulares = Traje::where('cod_tienda_id', $tienda->id)
            ->withCount('unidades') 
            ->orderBy('unidades_count', 'desc')
            ->take(5)
            ->get();

        // --- INFORME 2: ESTADO FÍSICO DEL INVENTARIO ---
        $estadoInventario = DB::table('inventario_unidades')
            ->join('trajes', 'inventario_unidades.cod_traje_id', '=', 'trajes.id')
            ->where('trajes.cod_tienda_id', $tienda->id)
            ->select('inventario_unidades.estado_unidad', DB::raw('count(*) as total'))
            ->groupBy('inventario_unidades.estado_unidad')
            ->get();

        // --- INFORME 3: BALANCE DE GÉNERO Y VARIANTES ---
        $totalTrajesMaestros = Traje::where('cod_tienda_id', $tienda->id)
            ->whereNull('cod_traje_padre')
            ->count();

        $trajesConVariante = Traje::where('cod_tienda_id', $tienda->id)
            ->whereNull('cod_traje_padre')
            ->whereHas('varianteFemenina')
            ->count();

        // Extraer color corporativo
        $colorTienda = $tienda->diseno->color_primario ?? '#16a34a';

        // ════ BUSCADOR PARAMETRIZADO DEL PERCHERO ════
        // Trajes para llenar el select del formulario
        $trajes = Traje::where('cod_tienda_id', $tienda->id)->get();

        // Consulta base sobre las unidades físicas de esta tienda
        $query = TrajeUnidad::whereHas('traje', function($q) use ($tienda) {
            $q->where('cod_tienda_id', $tienda->id);
        });

        // Si el usuario filtra por un traje específico
        if ($request->filled('cod_traje')) {
            $query->where('cod_traje_id', $request->cod_traje);
        }

        // Si el usuario filtra por un estado (Disponible, Alquilado, etc)
        if ($request->filled('estado')) {
            $query->where('estado_unidad', $request->estado);
        }

        $unidades = $query->with('traje')->get();

        // Enviamos TODO a la misma vista index
        return view('vendedor.informes.index', compact(
            'tienda', 
            'trajesPopulares', 
            'estadoInventario', 
            'totalTrajesMaestros', 
            'trajesConVariante',
            'colorTienda',
            'trajes',
            'unidades'
        ));
    }
}
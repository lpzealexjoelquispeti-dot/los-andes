<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendedor\Traje;
use App\Models\Vendedor\TrajeUnidad; // Tu 'inventario_unidades'

class InformeEstadisticoController extends Controller
{
    public function index()
    {
        // 1. Obtener la tienda del vendedor actual
        $tienda = Auth::user()->tienda; 
        
        if (!$tienda) {
            return redirect()->back()->with('error', 'No tienes una tienda asignada.');
        }

        // --- INFORME 1: ROTACIÓN Y POPULARIDAD ---
        // Trajes más solicitados cruzando con los registros de alquileres
        $trajesPopulares = Traje::where('cod_tienda_id', $tienda->id)
            ->withCount('unidades') // Cantidad de piezas físicas totales que tiene
            ->orderBy('unidades_count', 'desc')
            ->take(5)
            ->get();

        // --- INFORME 2: ESTADO FÍSICO DEL INVENTARIO ---
        // Contamos cuántas unidades están en cada estado dentro de su perchero
        $estadoInventario = DB::table('inventario_unidades')
            ->join('trajes', 'inventario_unidades.cod_traje_id', '=', 'trajes.id')
            ->where('trajes.cod_tienda_id', $tienda->id)
            ->select('inventario_unidades.estado_unidad', DB::raw('count(*) as total'))
            ->groupBy('inventario_unidades.estado_unidad')
            ->get();

        // --- INFORME 3: BALANCE DE GÉNERO Y VARIANTES ---
        // Ver cuántos trajes maestros tienen su variante de pareja registrada
        $totalTrajesMaestros = Traje::where('cod_tienda_id', $tienda->id)
            ->whereNull('cod_traje_padre')
            ->count();

        $trajesConVariante = Traje::where('cod_tienda_id', $tienda->id)
            ->whereNull('cod_traje_padre')
            ->whereHas('varianteFemenina')
            ->count();

        // Extraer colores corporativos guardados en su Diseño Tienda para la UI
        $colorTienda = $tienda->diseno->color_primario ?? '#16a34a';

        return view('vendedor.informes.index', compact(
            'tienda', 
            'trajesPopulares', 
            'estadoInventario', 
            'totalTrajesMaestros', 
            'trajesConVariante',
            'colorTienda'
        ));
    }
}
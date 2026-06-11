<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Traje;
use App\Models\Tienda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteTrajesMasGastadosController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();

        $tiendas = Tienda::where('cod_usuario_tie', $vendedor->id)
            ->whereNull('deleted_at')
            ->get(['cod_tienda', 'nom_tie']);

        $filtros = $request->only([
            'cod_tienda',
            'fec_desde',
            'fec_hasta',
            'top',
            'puesta_min', // 1|2|3|all
        ]);

        $top = (int) ($filtros['top'] ?? 10);
        $top = $top > 0 ? min($top, 50) : 10;

        $puestaMin = $filtros['puesta_min'] ?? 'all';

        $fechaDesde = $filtros['fec_desde'] ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $filtros['fec_hasta'] ?? now()->toDateString();

        // Trajes con top por nivel_uso_alquileres (valor guardado)
        // Nota: el filtro por fecha se aplica a partir de la idea: nivel se incrementa al Devuelto.
        // Sin embargo, tu BD no tiene aún una tabla de “log de incremento”, entonces el filtro por fecha
        // solo se utiliza como aproximación en la carga del top si tuvieras un evento. Por ahora,
        // calculamos el top por nivel acumulado y presentamos fecha como metadato del reporte.

        $trajesQ = Traje::query()
            ->where('cod_tienda_traje', function () use ($vendedor, $filtros) {
                // Si el filtro de tienda viene, usamos esa tienda.
                // Si no viene, usamos la primera tienda del vendedor (en tu modelo actual típicamente 1 tienda).
                if (!empty($filtros['cod_tienda'])) {
                    return $filtros['cod_tienda'];
                }
                $t = Tienda::where('cod_usuario_tie', $vendedor->id)->whereNull('deleted_at')->first();
                return $t?->cod_tienda ?? null;
            })
            ->whereNull('cod_traje_padre')
            ->orderByDesc('nivel_uso_alquileres')
            ->orderByDesc('cod_traje')
            ->take($top);

        // Filtro por puesta mínima usando nivel_uso_alquileres
        $trajesQ->when($puestaMin !== 'all', function ($q) use ($puestaMin) {
            // mapeo:
            // 1ra => nivel 0
            // 2da => nivel 1..3
            // 3ra+ => nivel >=4
            if ($puestaMin === '2') {
                $q->whereBetween('nivel_uso_alquileres', [1, 3]);
            } elseif ($puestaMin === '3') {
                $q->where('nivel_uso_alquileres', '>=', 4);
            } elseif ($puestaMin === '1') {
                $q->where('nivel_uso_alquileres', 0);
            }
        });

        $trajes = $trajesQ->with(['danza:cod_danza,nom_danza', 'imagenes', 'tienda.diseno'])
            ->get();

        // Agrupación para gráfico por “puesta”
        $puestaCounts = [
            '1ra' => 0,
            '2da' => 0,
            '3ra+' => 0,
        ];

        foreach ($trajes as $t) {
            $nivel = (int) ($t->nivel_uso_alquileres ?? 0);
            if ($nivel === 0) $puestaCounts['1ra']++;
            elseif ($nivel >= 1 && $nivel <= 3) $puestaCounts['2da']++;
            else $puestaCounts['3ra+']++;
        }

        return view('vendedor.reportes.trajes_mas_gastados', compact(
            'trajes',
            'tiendas',
            'filtros',
            'top',
            'puestaMin',
            'fechaDesde',
            'fechaHasta',
            'puestaCounts'
        ));
    }

    public function descargarPdf(Request $request)
    {
        $vendedor = Auth::user();

        $filtros = $request->only([
            'cod_tienda',
            'fec_desde',
            'fec_hasta',
            'top',
            'puesta_min',
        ]);

        $top = (int) ($filtros['top'] ?? 10);
        $top = $top > 0 ? min($top, 50) : 10;
        $puestaMin = $filtros['puesta_min'] ?? 'all';

        $fechaDesde = $filtros['fec_desde'] ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $filtros['fec_hasta'] ?? now()->toDateString();

        $trajesQ = Traje::query()
            ->where('cod_tienda_traje', function () use ($vendedor, $filtros) {
                if (!empty($filtros['cod_tienda'])) {
                    return $filtros['cod_tienda'];
                }
                $t = Tienda::where('cod_usuario_tie', $vendedor->id)->whereNull('deleted_at')->first();
                return $t?->cod_tienda ?? null;
            })
            ->whereNull('cod_traje_padre')
            ->orderByDesc('nivel_uso_alquileres')
            ->orderByDesc('cod_traje')
            ->take($top);

        $trajesQ->when($puestaMin !== 'all', function ($q) use ($puestaMin) {
            if ($puestaMin === '2') {
                $q->whereBetween('nivel_uso_alquileres', [1, 3]);
            } elseif ($puestaMin === '3') {
                $q->where('nivel_uso_alquileres', '>=', 4);
            } elseif ($puestaMin === '1') {
                $q->where('nivel_uso_alquileres', 0);
            }
        });

        $trajes = $trajesQ->with(['danza:cod_danza,nom_danza', 'tienda:cod_tienda,nom_tie'])->get();

        $puestaCounts = [
            '1ra' => 0,
            '2da' => 0,
            '3ra+' => 0,
        ];

        foreach ($trajes as $t) {
            $nivel = (int) ($t->nivel_uso_alquileres ?? 0);
            if ($nivel === 0) $puestaCounts['1ra']++;
            elseif ($nivel >= 1 && $nivel <= 3) $puestaCounts['2da']++;
            else $puestaCounts['3ra+']++;
        }

        $pdf = Pdf::loadView('vendedor.reportes.pdf.trajes_mas_gastados_pdf', compact(
            'trajes',
            'puestaCounts',
            'fechaDesde',
            'fechaHasta',
            'top',
            'puestaMin',
            'vendedor'
        ))->setPaper('a4', 'landscape');

        $nombreArchivo = 'reporte_trajes_mas_gastados_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($nombreArchivo);
    }
}


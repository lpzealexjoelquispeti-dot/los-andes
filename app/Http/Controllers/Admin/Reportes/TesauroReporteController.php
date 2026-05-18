<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Http\Controllers\Controller;
use App\Models\BusquedaLog;
use App\Models\Tesauro;
use App\Models\Danza;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TesauroReporteExport;

class TesauroReporteController extends Controller
{
    public function index(Request $request)
    {
        // ── FILTROS ──
        $fechaDesde  = $request->input('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta  = $request->input('fecha_hasta', now()->toDateString());
        $tipoFiltro  = $request->input('tipo');
        $danzaFiltro = $request->input('danza_id');

        // ── QUERY BASE ──
        $logsQuery = BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])
            ->when($tipoFiltro, fn($q) => $q->where('tipo_resultado', $tipoFiltro))
            ->when($danzaFiltro, fn($q) => $q->where('cod_danza_ref', $danzaFiltro));

        // ── ESTADÍSTICAS GENERALES ──
        $totalBusquedas      = (clone $logsQuery)->count();
        $busquedasTesauro    = (clone $logsQuery)->where('tipo_resultado', 'tesauro')->count();
        $busquedasDanza      = (clone $logsQuery)->where('tipo_resultado', 'danza')->count();
        $busquedasTexto      = (clone $logsQuery)->where('tipo_resultado', 'texto')->count();
        $sinResultado        = (clone $logsQuery)->where('tipo_resultado', 'sin_resultado')->count();

        // ── TOP 10 TÉRMINOS MÁS BUSCADOS ──
        $topTerminos = (clone $logsQuery)
            ->selectRaw('termino_buscado, COUNT(*) as total')
            ->groupBy('termino_buscado')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── TOP DANZAS MÁS BUSCADAS ──
        $topDanzas = (clone $logsQuery)
            ->whereNotNull('cod_danza_ref')
            ->selectRaw('cod_danza_ref, COUNT(*) as total')
            ->groupBy('cod_danza_ref')
            ->orderByDesc('total')
            ->limit(8)
            ->with('danza:cod_danza,nom_danza')
            ->get();

        // ── BÚSQUEDAS SIN RESULTADO (Para mejorar el tesauro) ──
        $terminosSinResultado = (clone $logsQuery)
            ->where('tipo_resultado', 'sin_resultado')
            ->selectRaw('termino_buscado, COUNT(*) as total')
            ->groupBy('termino_buscado')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── ACTIVIDAD POR DÍA (Para la gráfica de línea) ──
        $actividadDiaria = (clone $logsQuery)
            ->selectRaw("DATE(created_at) as fecha, COUNT(*) as total")
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // ── DISTRIBUCIÓN POR TIPO (Para la gráfica de dona) ──
        $distribucionTipo = [
            'tesauro'       => $busquedasTesauro,
            'danza'         => $busquedasDanza,
            'texto'         => $busquedasTexto,
            'sin_resultado' => $sinResultado,
        ];

        // ── TÉRMINOS DEL TESAURO MÁS USADOS (veces_buscado) ──
        $tesauroMasUsado = Tesauro::orderByDesc('veces_buscado')
            ->where('veces_buscado', '>', 0)
            ->with('danza:cod_danza,nom_danza')
            ->limit(10)
            ->get();

        // ── PARA LOS FILTROS ──
        $danzas = Danza::orderBy('nom_danza')->get(['cod_danza', 'nom_danza']);

        return view('admin.reportes.tesauro.index', compact(
            'totalBusquedas',
            'busquedasTesauro',
            'busquedasDanza',
            'busquedasTexto',
            'sinResultado',
            'topTerminos',
            'topDanzas',
            'terminosSinResultado',
            'actividadDiaria',
            'distribucionTipo',
            'tesauroMasUsado',
            'danzas',
            'fechaDesde',
            'fechaHasta',
            'tipoFiltro',
            'danzaFiltro'
        ));
    }

    // ── EXPORTAR PDF ──
    public function exportPdf(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', now()->toDateString());

        $topTerminos = BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])
            ->selectRaw('termino_buscado, tipo_resultado, COUNT(*) as total')
            ->groupBy('termino_buscado', 'tipo_resultado')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        $terminosSinResultado = BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])
            ->where('tipo_resultado', 'sin_resultado')
            ->selectRaw('termino_buscado, COUNT(*) as total')
            ->groupBy('termino_buscado')
            ->orderByDesc('total')
            ->get();

        $totales = [
            'total'         => BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])->count(),
            'tesauro'       => BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])->where('tipo_resultado', 'tesauro')->count(),
            'sin_resultado' => BusquedaLog::whereBetween('created_at', [$fechaDesde, $fechaHasta . ' 23:59:59'])->where('tipo_resultado', 'sin_resultado')->count(),
        ];

        $pdf = Pdf::loadView('admin.reportes.tesauro.pdf', compact(
            'topTerminos',
            'terminosSinResultado',
            'totales',
            'fechaDesde',
            'fechaHasta'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('reporte-tesauro-' . $fechaDesde . '-al-' . $fechaHasta . '.pdf');
    }

    // ── EXPORTAR EXCEL ──
    public function exportExcel(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', now()->toDateString());

        return Excel::download(
            new TesauroReporteExport($fechaDesde, $fechaHasta),
            'reporte-tesauro-' . $fechaDesde . '-al-' . $fechaHasta . '.xlsx'
        );
    }
}
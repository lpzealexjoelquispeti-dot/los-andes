<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// Modelos
use App\Models\SancionAlquiler;
use App\Models\Alquiler;
use App\Models\Tienda;

class ReporteSancionesController extends Controller
{
    /**
     * Muestra la vista parametrizada de sanciones y entregas.
     */
    public function index(Request $request)
    {
        $vendedor = Auth::user();

        // Tiendas del vendedor autenticado (para filtro)
        $tiendas = Tienda::where('cod_usuario_tie', $vendedor->id)
            ->whereNull('deleted_at')
            ->get();

        // ─── Parámetros del filtro ───────────────────────────────────────────
        $filtros = $request->only([
            'cod_tienda',
            'tipo_reporte',   // 'sanciones' | 'entregas' | 'ambos'
            'tipo_sancion',   // Retraso | Daño | Perdida | Limpieza | (vacío=todos)
            'est_alquiler',   // estado del alquiler
            'pagada',         // 0 | 1 | (vacío=todos)
            'fec_desde',
            'fec_hasta',
            'orden',          // monto_desc | monto_asc | fecha_desc | fecha_asc
        ]);

        $tipoReporte = $filtros['tipo_reporte'] ?? 'ambos';

        // ─── QUERY BASE: sanciones ──────────────────────────────────────────
        $sanciones = collect();
        if (in_array($tipoReporte, ['sanciones', 'ambos'])) {
            $qSanciones = SancionAlquiler::with([
                'alquiler.unidadFisica.traje.tienda',
                'alquiler.cliente',
                'alquiler.evento',
            ])
            ->whereHas('alquiler.unidadFisica.traje.tienda', function ($q) use ($vendedor) {
                $q->where('cod_usuario_tie', $vendedor->id);
            })
            ->whereNull('sanciones_alquiler.deleted_at');

            // Filtro por tienda utilizando la relación real
            if (!empty($filtros['cod_tienda'])) {
                $qSanciones->whereHas('alquiler.unidadFisica.traje.tienda', function ($q) use ($filtros) {
                    $q->where('cod_tienda', $filtros['cod_tienda']);
                });
            }

            // Filtro tipo de sanción
            if (!empty($filtros['tipo_sancion'])) {
                $qSanciones->where('tipo_sancion', $filtros['tipo_sancion']);
            }

            // Filtro pagada blindado
            if (array_key_exists('pagada', $filtros) && $filtros['pagada'] !== null && $filtros['pagada'] !== '') {
                $qSanciones->where('pagada', (bool) $filtros['pagada']);
            }

            // Filtro fecha (usa created_at de la sanción)
            if (!empty($filtros['fec_desde'])) {
                $qSanciones->whereDate('sanciones_alquiler.created_at', '>=', $filtros['fec_desde']);
            }
            if (!empty($filtros['fec_hasta'])) {
                $qSanciones->whereDate('sanciones_alquiler.created_at', '<=', $filtros['fec_hasta']);
            }

            $sanciones = $qSanciones->get();
        }

        // ─── QUERY BASE: entregas ───────────────────────────────────────────
        $entregas = collect();
        if (in_array($tipoReporte, ['entregas', 'ambos'])) {
            $qEntregas = Alquiler::with([
                'unidadFisica.traje.tienda',
                'cliente',
                'evento',
                'sanciones',
            ])
            ->whereHas('unidadFisica.traje.tienda', function ($q) use ($vendedor) {
                $q->where('cod_usuario_tie', $vendedor->id);
            })
            ->whereIn('est_alquiler', ['Entregado', 'Devuelto', 'En Mora'])
            ->whereNull('alquileres.deleted_at');

            // Filtro por tienda utilizando la relación real
            if (!empty($filtros['cod_tienda'])) {
                $qEntregas->whereHas('unidadFisica.traje.tienda', function ($q) use ($filtros) {
                    $q->where('cod_tienda', $filtros['cod_tienda']);
                });
            }

            // Filtro estado alquiler
            if (!empty($filtros['est_alquiler'])) {
                $qEntregas->where('est_alquiler', $filtros['est_alquiler']);
            }

            // Filtro fecha salida
            if (!empty($filtros['fec_desde'])) {
                $qEntregas->whereDate('fec_salida', '>=', $filtros['fec_desde']);
            }
            if (!empty($filtros['fec_hasta'])) {
                $qEntregas->whereDate('fec_salida', '<=', $filtros['fec_hasta']);
            }

            // Ordenamiento
            $orden = $filtros['orden'] ?? 'fecha_desc';
            match ($orden) {
                'monto_desc'  => $qEntregas->orderByDesc('monto_total'),
                'monto_asc'   => $qEntregas->orderBy('monto_total'),
                'fecha_asc'   => $qEntregas->orderBy('fec_salida'),
                default       => $qEntregas->orderByDesc('fec_salida'),
            };

            $entregas = $qEntregas->get();
        }

        // ─── RESUMEN ESTADÍSTICO ────────────────────────────────────────────
        $resumen = [
            'total_sanciones'       => $sanciones->count(),
            'monto_sanciones'       => $sanciones->sum('monto_sancion'),
            'sanciones_pagadas'     => $sanciones->where('pagada', true)->count(),
            'sanciones_pendientes'  => $sanciones->where('pagada', false)->count(),
            'total_entregas'        => $entregas->count(),
            'monto_entregas'        => $entregas->sum('monto_total'),
            'entregas_devueltas'    => $entregas->where('est_alquiler', 'Devuelto')->count(),
            'entregas_en_mora'      => $entregas->where('est_alquiler', 'En Mora')->count(),
        ];

        return view('vendedor.reportes.sanciones_entregas', compact(
            'sanciones', 'entregas', 'tiendas', 'filtros', 'resumen', 'tipoReporte'
        ));
    }

    /**
     * Descarga el PDF con los mismos filtros y blindaje total de arrays.
     */
    public function descargarPdf(Request $request)
    {
        $vendedor = Auth::user();
        
        $tiendas = Tienda::where('cod_usuario_tie', $vendedor->id)
            ->whereNull('deleted_at')
            ->get();

        $filtros = $request->only([
            'cod_tienda',
            'tipo_reporte',
            'tipo_sancion',
            'est_alquiler',
            'pagada',
            'fec_desde',
            'fec_hasta',
            'orden'
        ]);
        
        $tipoReporte = $filtros['tipo_reporte'] ?? 'ambos';

        // ── Sanciones ──
        $sanciones = collect();
        if (in_array($tipoReporte, ['sanciones', 'ambos'])) {
            $q = SancionAlquiler::with([
                'alquiler.unidadFisica.traje.tienda', 
                'alquiler.cliente', 
                'alquiler.evento'
            ])
            ->whereHas('alquiler.unidadFisica.traje.tienda', fn($q) => $q->where('cod_usuario_tie', $vendedor->id))
            ->whereNull('sanciones_alquiler.deleted_at');
                
            if (!empty($filtros['cod_tienda'])) {
                $q->whereHas('alquiler.unidadFisica.traje.tienda', fn($t) => $t->where('cod_tienda', $filtros['cod_tienda']));
            }
            
            if (!empty($filtros['tipo_sancion'])) {
                $q->where('tipo_sancion', $filtros['tipo_sancion']);
            }
            
            // 🌟 BLINDAJE CRÍTICO APLICADO AQUÍ: Evita el error de clave indefinida al llamar al PDF
            if (array_key_exists('pagada', $filtros) && $filtros['pagada'] !== null && $filtros['pagada'] !== '') {
                $q->where('pagada', (bool)$filtros['pagada']);
            }
            
            if (!empty($filtros['fec_desde'])) {
                $q->whereDate('sanciones_alquiler.created_at', '>=', $filtros['fec_desde']);
            }
            if (!empty($filtros['fec_hasta'])) {
                $q->whereDate('sanciones_alquiler.created_at', '<=', $filtros['fec_hasta']);
            }
            
            $sanciones = $q->get();
        }

        // ── Entregas ──
        $entregas = collect();
        if (in_array($tipoReporte, ['entregas', 'ambos'])) {
            $q = Alquiler::with([
                'unidadFisica.traje.tienda', 
                'cliente', 
                'evento', 
                'sanciones'
            ])
            ->whereHas('unidadFisica.traje.tienda', fn($q) => $q->where('cod_usuario_tie', $vendedor->id))
            ->whereIn('est_alquiler', ['Entregado', 'Devuelto', 'En Mora'])
            ->whereNull('alquileres.deleted_at');
                
            if (!empty($filtros['cod_tienda'])) {
                $q->whereHas('unidadFisica.traje.tienda', fn($t) => $t->where('cod_tienda', $filtros['cod_tienda']));
            }
            
            if (!empty($filtros['est_alquiler'])) {
                $q->where('est_alquiler', $filtros['est_alquiler']);
            }
            
            if (!empty($filtros['fec_desde'])) {
                $q->whereDate('fec_salida', '>=', $filtros['fec_desde']);
            }
            if (!empty($filtros['fec_hasta'])) {
                $q->whereDate('fec_salida', '<=', $filtros['fec_hasta']);
            }
            
            // Conservamos el ordenamiento seleccionado por el usuario en el PDF
            $orden = $filtros['orden'] ?? 'fecha_desc';
            match ($orden) {
                'monto_desc'  => $q->orderByDesc('monto_total'),
                'monto_asc'   => $q->orderBy('monto_total'),
                'fecha_asc'   => $q->orderBy('fec_salida'),
                default       => $q->orderByDesc('fec_salida'),
            };
            
            $entregas = $q->get();
        }

        // Resumen unificado para los bloques informativos superiores del PDF
        $resumen = [
            'total_sanciones'      => $sanciones->count(),
            'monto_sanciones'      => $sanciones->sum('monto_sancion'),
            'sanciones_pagadas'    => $sanciones->where('pagada', true)->count(),
            'sanciones_pendientes' => $sanciones->where('pagada', false)->count(),
            'total_entregas'       => $entregas->count(),
            'monto_entregas'       => $entregas->sum('monto_total'),
            'entregas_devueltas'   => $entregas->where('est_alquiler', 'Devuelto')->count(),
            'entregas_en_mora'     => $entregas->where('est_alquiler', 'En Mora')->count(),
        ];

        $tiendasNombre = !empty($filtros['cod_tienda'])
            ? $tiendas->firstWhere('cod_tienda', $filtros['cod_tienda'])?->nom_tie ?? 'Todas'
            : 'Todas mis tiendas';

        // Renderizado del PDF horizontal ('landscape')
        $pdf = Pdf::loadView('vendedor.reportes.pdf.sanciones_entregas_pdf', compact(
            'sanciones', 'entregas', 'resumen', 'filtros', 'tipoReporte', 'tiendasNombre', 'vendedor'
        ))->setPaper('a4', 'landscape');

        $nombreArchivo = 'reporte_sanciones_entregas_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
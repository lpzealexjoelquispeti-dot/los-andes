{{-- resources/views/vendedor/reportes/pdf/sanciones_entregas_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 8.5px;
        color: #1f2937;
        background: #ffffff;
        padding: 20px;
    }

    /* ── Estructuración Avanzada para Tablas Invisibles (Sustituto de Flexbox para DomPDF) ── */
    .table-layout {
        width: 100%;
        margin-bottom: 15px;
        border: none;
    }
    .table-layout td {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        vertical-align: top;
    }

    /* ── Encabezado Estilo Premium Corporativo ── */
    .header-block {
        background-color: #0f172a;
        color: #ffffff;
        padding: 16px 20px;
        margin-bottom: 16px;
        border-radius: 8px;
    }
    .brand-name {
        font-size: 16px;
        font-weight: bold;
        color: #10b981;
        letter-spacing: -0.5px;
    }
    .brand-sub {
        font-size: 7.5px;
        color: #9ca3af;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .report-meta {
        text-align: right;
        font-size: 8px;
        color: #d1d5db;
        line-height: 1.4;
    }
    .report-title {
        font-size: 11px;
        font-weight: bold;
        color: #f59e0b;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }

    /* ── Bloque de KPIs Robustos ── */
    .kpi-table {
        width: 100%;
        margin-bottom: 16px;
    }
    .kpi-table td {
        padding: 0 4px !important;
        width: 25%;
    }
    .kpi-box {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 10px;
        background: #f9fafb;
    }
    .kpi-box.danger  { border-color: #fca5a5; background: #fff5f5; }
    .kpi-box.warning { border-color: #fcd34d; background: #fffbeb; }
    .kpi-box.success { border-color: #86efac; background: #f0fdf4; }
    .kpi-box.orange  { border-color: #fdba74; background: #fff7ed; }
    
    .kpi-label {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #4b5563;
        margin-bottom: 3px;
        font-weight: bold;
    }
    .kpi-value {
        font-size: 15px;
        font-weight: bold;
        color: #111827;
    }
    .kpi-sub {
        font-size: 7px;
        color: #6b7280;
        margin-top: 2px;
        font-weight: 500;
    }
    .kpi-box.danger  .kpi-value { color: #dc2626; }
    .kpi-box.warning .kpi-value { color: #d97706; }
    .kpi-box.success .kpi-value { color: #16a34a; }
    .kpi-box.orange  .kpi-value { color: #ea580c; }

    /* ── Títulos de Secciones Cohesivos ── */
    .section-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-bottom: 4px;
        margin-bottom: 8px;
        margin-top: 15px;
        border-bottom: 2px solid #e5e7eb;
    }
    .section-title.red    { border-color: #ef4444; color: #b91c1c; }
    .section-title.green  { border-color: #10b981; color: #15803d; }

    /* ── Estructuración Estricta de Tablas de Datos ── */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }
    .data-table th {
        background: #1e293b;
        color: #ffffff;
        padding: 5px 6px;
        text-align: left;
        font-size: 7.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: bold;
        border: 1px solid #1e293b;
    }
    .data-table th.right  { text-align: right; }
    .data-table th.center { text-align: center; }

    .data-table td {
        padding: 5px 6px;
        font-size: 7.5px;
        color: #374151;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
    }
    .data-table tr:nth-child(even) { background: #f9fafb; }
    
    .data-table td.right  { text-align: right; }
    .data-table td.center { text-align: center; }
    .data-table td.muted  { color: #6b7280; font-size: 7px; font-mono: true; }

    .data-table tfoot td {
        padding: 6px;
        font-size: 8px;
        font-weight: bold;
        background: #f3f4f6;
        color: #1f2937;
        border-top: 2px solid #d1d5db;
        border-bottom: 1px solid #d1d5db;
    }
    .data-table tfoot td.right { text-align: right; }

    /* ── Badges Compactos de Control Físico ── */
    .badge {
        display: inline-block;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 6.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-retraso  { background: #fef9c3; color: #854d0e; }
    .badge-dano     { background: #fee2e2; color: #991b1b; }
    .badge-perdida  { background: #ffe4e6; color: #9f1239; }
    .badge-limpieza { background: #dbeafe; color: #1e40af; }
    .badge-green    { background: #dcfce7; color: #166534; }
    .badge-yellow   { background: #fef9c3; color: #854d0e; }
    .badge-blue     { background: #dbeafe; color: #1e40af; }
    .badge-orange   { background: #ffedd5; color: #9a3412; }
    .badge-gray     { background: #f3f4f6; color: #4b5563; }

    /* ── Cierre de Página Documental ── */
    .footer-block {
        margin-top: 25px;
        padding-top: 6px;
        border-top: 1px solid #e5e7eb;
        width: 100%;
    }
    .footer-text {
        font-size: 7px;
        color: #9ca3af;
        font-weight: bold;
        text-transform: uppercase;
    }

    .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO CON TABLA INVISIBLE (COMPATIBLE CON DOMPDF) ═══════════════════════════════════════════ --}}
<div class="header-block">
    <table class="table-layout">
        <tr>
            <td>
                <div class="brand-name">🎭 AndesTrajes</div>
                <div class="brand-sub">Plataforma de Alquiler Folclórico</div>
            </td>
            <td class="report-meta">
                <div class="report-title">Reporte de Sanciones y Entregas</div>
                <div style="margin-top:3px;">Vendedor: <strong style="color:#ffffff;">{{ $vendedor->name ?? 'Usuario' }} {{ $vendedor->ap_pat ?? '' }}</strong></div>
                <div>Tienda(s): <strong style="color:#ffffff;">{{ $tiendasNombre ?? 'Todas las Sucursales' }}</strong></div>
                <div>Generado: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    {{-- Resumen de Filtros Activos --}}
    <div style="font-size:7px; color:#9ca3af; line-height:1.5; padding-top:6px; border-top:1px solid rgba(255,255,255,0.1);">
        <strong style="color:#e5e7eb; text-transform:uppercase; letter-spacing:0.5px;">Filtros de Extracción:</strong>
        Vista: <strong style="color:#fbbf24;">{{ ucfirst($filtros['tipo_reporte'] ?? 'ambos') }}</strong>
        @if(!empty($filtros['tipo_sancion'])) · Falta: <strong style="color:#fbbf24;">{{ $filtros['tipo_sancion'] }}</strong> @endif
        @if(!empty($filtros['est_alquiler'])) · Estado: <strong style="color:#fbbf24;">{{ $filtros['est_alquiler'] }}</strong> @endif
        @if(!empty($filtros['fec_desde'])) · Desde: <strong style="color:#fbbf24;">{{ \Carbon\Carbon::parse($filtros['fec_desde'])->format('d/m/Y') }}</strong> @endif
        @if(!empty($filtros['fec_hasta'])) · Hasta: <strong style="color:#fbbf24;">{{ \Carbon\Carbon::parse($filtros['fec_hasta'])->format('d/m/Y') }}</strong> @endif
    </div>
</div>

{{-- ══ SECCIÓN DE KPIS RENDERIZADOS EN GRILLA FIJA DE TABLA ═══════════════════════════════════════════ --}}
<table class="kpi-table">
    <tr>
        <td>
            <div class="kpi-box danger">
                <div class="kpi-label">Total Sanciones</div>
                <div class="kpi-value">{{ $resumen['total_sanciones'] ?? 0 }}</div>
                <div class="kpi-sub">Bs {{ number_format($resumen['monto_sanciones'] ?? 0, 2) }} en multas</div>
            </div>
        </td>
        <td>
            <div class="kpi-box warning">
                <div class="kpi-label">Sanciones Pendientes</div>
                <div class="kpi-value">{{ $resumen['sanciones_pendientes'] ?? 0 }}</div>
                <div class="kpi-sub">{{ $resumen['sanciones_pagadas'] ?? 0 }} liquidadas</div>
            </div>
        </td>
        <td>
            <div class="kpi-box success">
                <div class="kpi-label">Total Entregas</div>
                <div class="kpi-value">{{ $resumen['total_entregas'] ?? 0 }}</div>
                <div class="kpi-sub">Bs {{ number_format($resumen['monto_entregas'] ?? 0, 2) }} facturado</div>
            </div>
        </td>
        <td>
            <div class="kpi-box orange">
                <div class="kpi-label">En Mora</div>
                <div class="kpi-value">{{ $resumen['entregas_en_mora'] ?? 0 }}</div>
                <div class="kpi-sub">{{ $resumen['entregas_devueltas'] ?? 0 }} devueltos conforme</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ REPOSITORIO DE LA TABLA DE SANCIONES ═══════════════════════════════════════════════ --}}
@if(in_array($tipoReporte, ['sanciones', 'ambos']) && isset($sanciones) && $sanciones->count() > 0)
<div class="section-title red">⚠ Sanciones Registradas ({{ $sanciones->count() }} registros)</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">ID</th>
            <th style="width: 10%;">Tipo Falta</th>
            <th style="width: 16%;">Cliente</th>
            <th style="width: 18%;">Prenda Coreográfica</th>
            <th style="width: 10%;">N/Serie</th>
            <th style="width: 12%;">Evento Folclórico</th>
            <th class="right" style="width: 10%;">Monto</th>
            <th class="center" style="width: 8%;">Estado</th>
            <th style="width: 12%;">Fecha Falta</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sanciones as $s)
        <tr>
            <td class="muted">#{{ $s->cod_sancion }}</td>
            <td>
                @php 
                    $bt = match($s->tipo_sancion) { 
                        'Retraso'=>'badge-retraso',
                        'Daño'=>'badge-dano',
                        'Dano'=>'badge-dano',
                        'Perdida'=>'badge-perdida',
                        'Limpieza'=>'badge-limpieza', 
                        default=>'badge-gray' 
                    }; 
                @endphp
                <span class="badge {{ $bt }}">{{ $s->tipo_sancion === 'Dano' ? 'Daño' : $s->tipo_sancion }}</span>
            </td>
            <td style="font-weight: bold;">{{ $s->alquiler->cliente->name ?? '—' }} {{ $s->alquiler->cliente->ap_pat ?? '' }}</td>
            {{-- 🌟 CORRECCIÓN CRÍTICA: Uso de la relación real unidadFisica --}}
            <td>{{ $s->alquiler->unidadFisica->traje->nom_traje ?? ($s->alquiler->unidad->traje->nom_traje ?? '—') }}</td>
            <td class="muted">{{ $s->alquiler->unidadFisica->nro_serie_interno ?? ($s->alquiler->unidad->nro_serie_interno ?? '—') }}</td>
            <td class="muted">{{ $s->alquiler->evento->nom_evento ?? '—' }}</td>
            <td class="right" style="color:#dc2626; font-weight:bold;">Bs {{ number_format($s->monto_sancion, 2) }}</td>
            <td class="center">
                @if($s->pagada)
                    <span class="badge badge-green">Sí</span>
                @else
                    <span class="badge badge-yellow">Pendiente</span>
                @endif
            </td>
            <td class="muted">{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">TOTAL ACUMULADO PENALIZACIONES Y MULTAS</td>
            <td class="right" style="color:#b91c1c; font-size:9px;">Bs {{ number_format($sanciones->sum('monto_sancion'), 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@elseif(in_array($tipoReporte, ['sanciones', 'ambos']))
<div class="section-title red">Sanciones Registradas</div>
<p style="font-size:7.5px; color:#9ca3af; padding:5px 0; font-weight: bold; text-transform: uppercase;">No se encontraron cargos de sanciones con los filtros aplicados.</p>
@endif

{{-- ══ REPOSITORIO DE LA TABLA DE ENTREGAS (ALQUILERES) ════════════════════════════════════════════════ --}}
@if(in_array($tipoReporte, ['entregas', 'ambos']) && isset($entregas) && $entregas->count() > 0)

{{-- Salto de página preventivo si la tabla de sanciones es densa para evitar cortes feos --}}
@if($tipoReporte === 'ambos' && $sanciones->count() > 6)
<div class="page-break"></div>
@endif

<div class="section-title green">✓ Control de Entregas y Despachos ({{ $entregas->count() }} registros)</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">ID</th>
            <th style="width: 16%;">Cliente Destinatario</th>
            <th style="width: 10%;">Nro Celular</th>
            <th style="width: 18%;">Prenda Coreográfica</th>
            <th class="center" style="width: 5%;">Talla</th>
            <th style="width: 12%;">Evento</th>
            <th style="width: 8%;">Despacho</th>
            <th style="width: 8%;">Ret. Prev.</th>
            <th style="width: 8%;">Ret. Real</th>
            <th class="right" style="width: 10%;">Monto Alq.</th>
            <th class="center" style="width: 8%;">Estado</th>
            <th class="center" style="width: 5%;">Sanc.</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entregas as $e)
        @php
            $retrasado = $e->fec_retorno_real && $e->fec_retorno_real > $e->fec_retorno_prev;
            $be = match($e->est_alquiler) { 
                'Entregado'=>'badge-blue', 
                'Devuelto'=>'badge-green', 
                'En Mora'=>'badge-orange', 
                default=>'badge-gray' 
            };
        @endphp
        <tr>
            <td class="muted">#{{ $e->cod_alquiler }}</td>
            <td style="font-weight: bold;">{{ $e->cliente->name ?? '—' }} {{ $e->cliente->ap_pat ?? '' }}</td>
            <td class="muted">{{ $e->nro_celular_cliente ?? '—' }}</td>
            {{-- 🌟 CORRECCIÓN CRÍTICA: Uso de la relación real unidadFisica --}}
            <td>{{ $e->unidadFisica->traje->nom_traje ?? ($e->unidad->traje->nom_traje ?? '—') }}</td>
            <td class="center muted">{{ $e->unidadFisica->talla ?? ($e->unidad->talla ?? 'U') }}</td>
            <td class="muted">{{ $e->evento->nom_evento ?? '—' }}</td>
            <td class="muted">{{ \Carbon\Carbon::parse($e->fec_salida)->format('d/m/Y') }}</td>
            <td class="muted">{{ \Carbon\Carbon::parse($e->fec_retorno_prev)->format('d/m/Y') }}</td>
            <td style="{{ $retrasado ? 'color:#ea580c; font-weight:bold;' : 'color:#6b7280;' }}">
                {{ $e->fec_retorno_real ? \Carbon\Carbon::parse($e->fec_retorno_real)->format('d/m/Y') : '—' }}
            </td>
            <td class="right" style="color:#16a34a; font-weight:bold;">Bs {{ number_format($e->monto_total, 2) }}</td>
            <td class="center"><span class="badge {{ $be }}">{{ $e->est_alquiler }}</span></td>
            <td class="center">
                @if($e->sanciones && $e->sanciones->count() > 0)
                    <span class="badge" style="background:#fee2e2; color:#991b1b;">{{ $e->sanciones->count() }}</span>
                @else
                    <span class="muted">—</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9">TOTAL BRUTO FACTURADO EN ENTEGAS Y COBROS</td>
            <td class="right" style="color:#15803d; font-size:9px;">Bs {{ number_format($entregas->sum('monto_total'), 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@elseif(in_array($tipoReporte, ['entregas', 'ambos']))
<div class="section-title green">Control de Entregas y Despachos</div>
<p style="font-size:7.5px; color:#9ca3af; padding:5px 0; font-weight: bold; text-transform: uppercase;">No se registraron movimientos de flujos físicos en el almacén.</p>
@endif

{{-- ══ PIE DE PÁGINA DOCUMENTAL EN ANCHURA COMPLETA ═══════════════════════════════════════════════ --}}
<div class="footer-block">
    <table class="table-layout">
        <tr>
            <td class="footer-text"><strong>AndesTrajes</strong> · Sistema de Gestión Folclórica Comercial</td>
            <td class="footer-text" style="text-align: right;">Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} · Documento Oficial de Auditoría</td>
        </tr>
    </table>
</div>

</body>
</html>
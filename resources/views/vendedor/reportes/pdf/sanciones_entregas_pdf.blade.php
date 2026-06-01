{{-- resources/views/vendedor/reportes/pdf/sanciones_entregas_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
        color: #1a1a2e;
        background: #ffffff;
    }

    /* ── Encabezado ─────────────────────────────────────── */
    .header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
        color: white;
        padding: 18px 24px 14px;
        margin-bottom: 16px;
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .brand-name {
        font-size: 18px;
        font-weight: bold;
        color: #4ade80; /* andes-verde */
        letter-spacing: -0.5px;
    }
    .brand-sub {
        font-size: 8px;
        color: rgba(255,255,255,0.5);
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .report-meta {
        text-align: right;
        font-size: 8px;
        color: rgba(255,255,255,0.6);
        line-height: 1.6;
    }
    .report-title {
        font-size: 11px;
        font-weight: bold;
        color: #fbbf24;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    /* ── Resumen KPIs ────────────────────────────────────── */
    .kpi-row {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
        padding: 0 2px;
    }
    .kpi-box {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 10px;
        background: #f8fafc;
    }
    .kpi-box.danger  { border-color: #fca5a5; background: #fff5f5; }
    .kpi-box.warning { border-color: #fcd34d; background: #fffbeb; }
    .kpi-box.success { border-color: #86efac; background: #f0fdf4; }
    .kpi-box.orange  { border-color: #fdba74; background: #fff7ed; }
    .kpi-label {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 16px;
        font-weight: bold;
        color: #111827;
    }
    .kpi-sub {
        font-size: 7.5px;
        color: #6b7280;
        margin-top: 2px;
    }
    .kpi-box.danger  .kpi-value { color: #ef4444; }
    .kpi-box.warning .kpi-value { color: #d97706; }
    .kpi-box.success .kpi-value { color: #16a34a; }
    .kpi-box.orange  .kpi-value { color: #ea580c; }

    /* ── Filtros activos ─────────────────────────────────── */
    .filtros-bar {
        font-size: 7.5px;
        color: #6b7280;
        margin-bottom: 12px;
        padding: 5px 8px;
        background: #f1f5f9;
        border-left: 3px solid #4ade80;
        border-radius: 0 4px 4px 0;
    }
    .filtros-bar strong { color: #374151; }

    /* ── Sección título ──────────────────────────────────── */
    .section-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #374151;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 5px;
        margin-bottom: 8px;
        margin-top: 14px;
    }
    .section-title.red    { border-color: #ef4444; color: #b91c1c; }
    .section-title.green  { border-color: #4ade80; color: #15803d; }

    /* ── Tablas ──────────────────────────────────────────── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }
    thead tr {
        background: #1e293b;
        color: white;
    }
    thead th {
        padding: 5px 6px;
        text-align: left;
        font-size: 7.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    thead th.right { text-align: right; }
    thead th.center { text-align: center; }

    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr:hover { background: #eff6ff; }

    tbody td {
        padding: 4.5px 6px;
        font-size: 8px;
        color: #374151;
        vertical-align: middle;
    }
    tbody td.right  { text-align: right; }
    tbody td.center { text-align: center; }
    tbody td.muted  { color: #9ca3af; font-size: 7.5px; }

    tfoot td {
        padding: 5px 6px;
        font-size: 8.5px;
        font-weight: bold;
        background: #f1f5f9;
        border-top: 2px solid #e2e8f0;
    }
    tfoot td.right { text-align: right; }

    /* ── Badges ──────────────────────────────────────────── */
    .badge {
        display: inline-block;
        padding: 1.5px 5px;
        border-radius: 3px;
        font-size: 7px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    /* ── Pie de página ───────────────────────────────────── */
    .footer {
        margin-top: 18px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        font-size: 7px;
        color: #9ca3af;
    }
    .footer strong { color: #6b7280; }

    .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ═══════════════════════════════════════════════════════ --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="brand-name">🎭 AndesTrajes</div>
            <div class="brand-sub">Plataforma de alquiler folclórico</div>
        </div>
        <div class="report-meta">
            <div class="report-title">Reporte de Sanciones y Entregas</div>
            <div style="margin-top:4px;">Vendedor: <strong style="color:white;">{{ $vendedor->name }} {{ $vendedor->ap_pat }}</strong></div>
            <div>Tienda(s): <strong style="color:white;">{{ $tiendasNombre }}</strong></div>
            <div>Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- Filtros activos --}}
    <div style="font-size:7.5px; color:rgba(255,255,255,0.5); line-height:1.8;">
        <strong style="color:rgba(255,255,255,0.7);">Filtros aplicados:</strong>
        Vista: <strong style="color:#fbbf24;">{{ ucfirst($filtros['tipo_reporte'] ?? 'ambos') }}</strong>
        @if(!empty($filtros['tipo_sancion'])) · Tipo sanción: <strong style="color:#fbbf24;">{{ $filtros['tipo_sancion'] }}</strong> @endif
        @if(!empty($filtros['est_alquiler'])) · Estado: <strong style="color:#fbbf24;">{{ $filtros['est_alquiler'] }}</strong> @endif
        @if(!empty($filtros['fec_desde'])) · Desde: <strong style="color:#fbbf24;">{{ \Carbon\Carbon::parse($filtros['fec_desde'])->format('d/m/Y') }}</strong> @endif
        @if(!empty($filtros['fec_hasta'])) · Hasta: <strong style="color:#fbbf24;">{{ \Carbon\Carbon::parse($filtros['fec_hasta'])->format('d/m/Y') }}</strong> @endif
    </div>
</div>

{{-- ══ KPIs ═══════════════════════════════════════════════════════════ --}}
<div class="kpi-row">
    <div class="kpi-box danger">
        <div class="kpi-label">Total Sanciones</div>
        <div class="kpi-value">{{ $resumen['total_sanciones'] }}</div>
        <div class="kpi-sub">Bs {{ number_format($resumen['monto_sanciones'], 2) }} en multas</div>
    </div>
    <div class="kpi-box warning">
        <div class="kpi-label">Sanciones Pendientes</div>
        <div class="kpi-value">{{ $resumen['sanciones_pendientes'] }}</div>
        <div class="kpi-sub">{{ $resumen['sanciones_pagadas'] }} ya pagadas</div>
    </div>
    <div class="kpi-box success">
        <div class="kpi-label">Total Entregas</div>
        <div class="kpi-value">{{ $resumen['total_entregas'] }}</div>
        <div class="kpi-sub">Bs {{ number_format($resumen['monto_entregas'], 2) }} facturado</div>
    </div>
    <div class="kpi-box orange">
        <div class="kpi-label">En Mora</div>
        <div class="kpi-value">{{ $resumen['entregas_en_mora'] }}</div>
        <div class="kpi-sub">{{ $resumen['entregas_devueltas'] }} devueltos OK</div>
    </div>
</div>

{{-- ══ TABLA SANCIONES ═══════════════════════════════════════════════ --}}
@if(in_array($tipoReporte, ['sanciones', 'ambos']) && $sanciones->count() > 0)
<div class="section-title red">⚠ Sanciones ({{ $sanciones->count() }} registros)</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tipo</th>
            <th>Cliente</th>
            <th>Traje</th>
            <th>N/Serie</th>
            <th>Evento</th>
            <th class="right">Monto</th>
            <th class="center">Pagada</th>
            <th>Descripción</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sanciones as $s)
        <tr>
            <td class="muted">{{ $s->cod_sancion }}</td>
            <td>
                @php $bt = match($s->tipo_sancion) { 'Retraso'=>'badge-retraso','Daño'=>'badge-dano','Perdida'=>'badge-perdida','Limpieza'=>'badge-limpieza', default=>'badge-gray' }; @endphp
                <span class="badge {{ $bt }}">{{ $s->tipo_sancion }}</span>
            </td>
            <td>{{ $s->alquiler->cliente->name ?? '—' }} {{ $s->alquiler->cliente->ap_pat ?? '' }}</td>
            <td>{{ $s->alquiler->unidad->traje->nom_traje ?? '—' }}</td>
            <td class="muted">{{ $s->alquiler->unidad->nro_serie_interno ?? '—' }}</td>
            <td class="muted">{{ $s->alquiler->evento->nom_evento ?? '—' }}</td>
            <td class="right" style="color:#ef4444;font-weight:bold;">Bs {{ number_format($s->monto_sancion, 2) }}</td>
            <td class="center">
                @if($s->pagada)
                    <span class="badge badge-green">Sí</span>
                @else
                    <span class="badge badge-yellow">Pendiente</span>
                @endif
            </td>
            <td style="max-width:120px;">{{ \Illuminate\Support\Str::limit($s->descripcion ?? '—', 45) }}</td>
            <td class="muted">{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Total sanciones</td>
            <td class="right" style="color:#ef4444;">Bs {{ number_format($sanciones->sum('monto_sancion'), 2) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@elseif(in_array($tipoReporte, ['sanciones', 'ambos']))
<div class="section-title red">Sanciones</div>
<p style="font-size:8px;color:#9ca3af;padding:8px 0;">No hay sanciones con los filtros aplicados.</p>
@endif

{{-- ══ TABLA ENTREGAS ════════════════════════════════════════════════ --}}
@if(in_array($tipoReporte, ['entregas', 'ambos']) && $entregas->count() > 0)

{{-- Salto de página si hay ambas tablas y hay datos en sanciones --}}
@if($tipoReporte === 'ambos' && $sanciones->count() > 10)
<div class="page-break"></div>
@endif

<div class="section-title green">✓ Entregas ({{ $entregas->count() }} registros)</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Celular</th>
            <th>Traje</th>
            <th>Talla</th>
            <th>Evento</th>
            <th>Salida</th>
            <th>Ret. Prev.</th>
            <th>Ret. Real</th>
            <th class="right">Monto</th>
            <th class="center">Estado</th>
            <th class="center">Sanc.</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entregas as $e)
        @php
            $retrasado = $e->fec_retorno_real && $e->fec_retorno_real > $e->fec_retorno_prev;
            $be = match($e->est_alquiler) { 'Entregado'=>'badge-blue', 'Devuelto'=>'badge-green', 'En Mora'=>'badge-orange', default=>'badge-gray' };
        @endphp
        <tr>
            <td class="muted">{{ $e->cod_alquiler }}</td>
            <td>{{ $e->cliente->name ?? '—' }} {{ $e->cliente->ap_pat ?? '' }}</td>
            <td class="muted">{{ $e->nro_celular_cliente ?? '—' }}</td>
            <td>{{ $e->unidad->traje->nom_traje ?? '—' }}</td>
            <td class="center muted">{{ $e->unidad->talla ?? '—' }}</td>
            <td class="muted">{{ $e->evento->nom_evento ?? '—' }}</td>
            <td class="muted">{{ \Carbon\Carbon::parse($e->fec_salida)->format('d/m/Y') }}</td>
            <td class="muted">{{ \Carbon\Carbon::parse($e->fec_retorno_prev)->format('d/m/Y') }}</td>
            <td style="{{ $retrasado ? 'color:#ea580c;font-weight:bold;' : 'color:#9ca3af;' }}">
                {{ $e->fec_retorno_real ? \Carbon\Carbon::parse($e->fec_retorno_real)->format('d/m/Y') : '—' }}
            </td>
            <td class="right" style="color:#16a34a;font-weight:bold;">Bs {{ number_format($e->monto_total, 2) }}</td>
            <td class="center"><span class="badge {{ $be }}">{{ $e->est_alquiler }}</span></td>
            <td class="center">
                @if($e->sanciones && $e->sanciones->count() > 0)
                    <span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $e->sanciones->count() }}</span>
                @else
                    <span class="muted">—</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9">Total facturado en entregas</td>
            <td class="right" style="color:#16a34a;">Bs {{ number_format($entregas->sum('monto_total'), 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@elseif(in_array($tipoReporte, ['entregas', 'ambos']))
<div class="section-title green">Entregas</div>
<p style="font-size:8px;color:#9ca3af;padding:8px 0;">No hay entregas con los filtros aplicados.</p>
@endif

{{-- ══ PIE DE PÁGINA ═══════════════════════════════════════════════ --}}
<div class="footer">
    <div><strong>AndesTrajes</strong> · Sistema de gestión folclórica</div>
    <div>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} · Documento confidencial</div>
</div>

</body>
</html>
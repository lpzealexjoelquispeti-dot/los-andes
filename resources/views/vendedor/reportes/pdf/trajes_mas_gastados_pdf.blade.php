<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .header { background: #111827; color: white; padding: 26px 36px; margin-bottom: 22px; }
        .header h1 { font-size: 22px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px; }
        .header p { font-size: 10px; opacity: 0.85; margin-top: 6px; text-transform: uppercase; letter-spacing: 2px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; text-align: left; padding: 8px 12px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; }
        td { padding: 8px 12px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:last-child td { border-bottom: none; }

        .section { margin-bottom: 18px; }
        .section-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; margin-bottom: 10px; border-bottom: 2px solid #f3f4f6; padding-bottom: 6px; }

        .pill { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .p1 { background: #ecfdf5; color: #047857; }
        .p2 { background: #e0f2fe; color: #075985; }
        .p3 { background: #fffbeb; color: #92400e; }

        .footer { position: fixed; bottom: 18px; left: 30px; right: 30px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 8px; }

        .kpis { display: flex; gap: 12px; margin-bottom: 18px; }
        .kpi-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; text-align: center; }
        .kpi-num { font-size: 24px; font-weight: 900; color: #111827; }
        .kpi-lbl { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-top: 2px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Trajes Más Gastados (Top)</h1>
    <p>Reporte parametrizado · Puesta 1ra/2da/3ra+</p>
    <div style="margin-top:8px; font-size:10px; opacity:0.9; letter-spacing:0.5px;">
        Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }} · Top {{ $top }} · Puesta min: {{ $puestaMin }}
    </div>
</div>

<div class="section">
    <div class="kpis">
        <div class="kpi-box">
            <div class="kpi-num">{{ (int)$puestaCounts['1ra'] }}</div>
            <div class="kpi-lbl">1ra (nivel 0)</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-num">{{ (int)$puestaCounts['2da'] }}</div>
            <div class="kpi-lbl">2da (nivel 1..3)</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-num">{{ (int)$puestaCounts['3ra+'] }}</div>
            <div class="kpi-lbl">3ra+ (nivel ≥ 4)</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Ranking por nivel acumulado</div>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Traje</th>
            <th>Danza</th>
            <th>Puesta</th>
            <th style="text-align:right">Nivel</th>
        </tr>
        </thead>
        <tbody>
        @foreach($trajes as $i => $t)
            @php
                $nivel = (int)($t->nivel_uso_alquileres ?? 0);
                $puesta = $nivel === 0 ? '1ra' : ($nivel >= 1 && $nivel <= 3 ? '2da' : '3ra+');
                $pillClass = $puesta === '1ra' ? 'p1' : ($puesta === '2da' ? 'p2' : 'p3');
            @endphp
            <tr>
                <td style="color:#9ca3af; font-weight:900">{{ $i+1 }}</td>
                <td style="font-weight:700; text-transform:uppercase">{{ $t->nom_traje }}</td>
                <td>{{ $t->danza->nom_danza ?? '—' }}</td>
                <td><span class="pill {{ $pillClass }}">{{ $puesta }}</span></td>
                <td style="text-align:right; font-weight:900">{{ $nivel }}</td>
            </tr>
        @endforeach
        @if($trajes->isEmpty())
            <tr>
                <td colspan="5" style="padding:18px; text-align:center; color:#9ca3af; font-weight:900;">Sin datos</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="footer">
    Generado el {{ now()->format('d/m/Y H:i') }} — Sistema Los Andes · Reporte
</div>

</body>
</html>


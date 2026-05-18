<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .header { background: #007a3d; color: white; padding: 30px 40px; margin-bottom: 30px; }
        .header h1 { font-size: 24px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px; }
        .header p  { font-size: 10px; opacity: 0.8; margin-top: 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header .fecha { font-size: 10px; margin-top: 8px; opacity: 0.7; }

        .stats { display: flex; gap: 15px; padding: 0 40px; margin-bottom: 30px; }
        .stat-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 15px; text-align: center; }
        .stat-box .num { font-size: 28px; font-weight: 900; color: #007a3d; }
        .stat-box .lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-top: 4px; }

        .section { padding: 0 40px; margin-bottom: 25px; }
        .section-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; margin-bottom: 12px; border-bottom: 2px solid #f3f4f6; padding-bottom: 6px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; text-align: left; padding: 8px 12px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; }
        td { padding: 8px 12px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:last-child td { border-bottom: none; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 8px; font-weight: 900; text-transform: uppercase; }
        .badge-tesauro { background: #dcfce7; color: #16a34a; }
        .badge-danza   { background: #dbeafe; color: #2563eb; }
        .badge-texto   { background: #fef9c3; color: #ca8a04; }
        .badge-sin     { background: #fee2e2; color: #dc2626; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #d1d5db; border-top: 1px solid #f3f4f6; padding-top: 8px; }
        .alerta { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 8px; }
        .alerta .term { font-weight: 900; text-transform: uppercase; color: #dc2626; }
        .alerta .count { color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Inteligencia de Búsqueda</h1>
        <p>Análisis Estadístico del Tesauro Folklórico</p>
        <div class="fecha">Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</div>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="stats">
        <div class="stat-box">
            <div class="num">{{ number_format($totales['total']) }}</div>
            <div class="lbl">Total Búsquedas</div>
        </div>
        <div class="stat-box">
            <div class="num" style="color:#007a3d">{{ number_format($totales['tesauro']) }}</div>
            <div class="lbl">Vía Tesauro</div>
        </div>
        <div class="stat-box">
            <div class="num" style="color:#dc2626">{{ number_format($totales['sin_resultado']) }}</div>
            <div class="lbl">Sin Resultado</div>
        </div>
        <div class="stat-box">
            <div class="num" style="color:#2563eb">
                {{ $totales['total'] > 0 ? round($totales['tesauro'] / $totales['total'] * 100) : 0 }}%
            </div>
            <div class="lbl">Eficiencia Tesauro</div>
        </div>
    </div>

    {{-- TOP TÉRMINOS --}}
    <div class="section">
        <div class="section-title">Top Términos Más Buscados</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Término Buscado</th>
                    <th>Tipo de Resultado</th>
                    <th style="text-align:right">Búsquedas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topTerminos as $i => $termino)
                <tr>
                    <td style="color:#d1d5db; font-weight:900">{{ $i + 1 }}</td>
                    <td style="font-weight:700; text-transform:uppercase">{{ $termino->termino_buscado }}</td>
                    <td>
                        <span class="badge badge-{{ $termino->tipo_resultado === 'sin_resultado' ? 'sin' : $termino->tipo_resultado }}">
                            {{ $termino->tipo_resultado }}
                        </span>
                    </td>
                    <td style="text-align:right; font-weight:900; color:#007a3d">{{ $termino->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TÉRMINOS SIN RESULTADO --}}
    @if($terminosSinResultado->count())
    <div class="section">
        <div class="section-title">⚠ Términos Sin Resultado — Agregar al Tesauro</div>
        @foreach($terminosSinResultado as $termino)
        <div class="alerta">
            <span class="term">{{ $termino->termino_buscado }}</span>
            <span class="count"> — buscado {{ $termino->total }} {{ $termino->total == 1 ? 'vez' : 'veces' }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} — Sistema Los Andes · Reporte Confidencial
    </div>

</body>
</html>
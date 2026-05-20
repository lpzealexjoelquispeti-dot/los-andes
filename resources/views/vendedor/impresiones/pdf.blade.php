<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plancha de Impresión Térmica</title>
    <style>
        @page { margin: 12px; }
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 5px; background-color: #ffffff; }
        
        .grid-etiquetas { width: 100%; }
        
        /* Contenedor del Sticker / Tarjeta */
        .card-sticker {
            width: 30%;
            border: 1px solid #cbd5e1;
            margin: 4px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            border-radius: 8px;
            overflow: hidden; /* Obliga a la cabecera a respetar las esquinas redondeadas */
            page-break-inside: avoid;
            background-color: #ffffff;
        }
        
        /* Cabecera Corporativa Unificada */
        .header-corporativo {
            padding: 6px 8px;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        
        /* Ajuste quirúrgico para que el Logo y el Texto se alineen en DomPDF */
        .logo-img {
            height: 12px;
            width: auto;
            vertical-align: middle;
            margin-right: 5px;
        }

        .texto-tienda {
            vertical-align: middle;
            display: inline-block;
            font-size: 8px;
        }
        
        /* Cuerpo del Sticker */
        .cuerpo-sticker { 
            padding: 8px; 
        }
        
        .danza-titulo { 
            font-size: 10px; 
            font-weight: 900; 
            text-transform: uppercase; 
            color: #1e293b; 
            margin: 0; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .pieza-nombre { 
            font-size: 11px; 
            font-weight: bold; 
            color: #475569; 
            margin: 4px 0; 
            text-transform: uppercase; 
        }
        
        /* Badge de Talla Negro */
        .talla-badge {
            font-size: 8px;
            background-color: #0f172a;
            color: #ffffff;
            padding: 2px 6px;
            font-weight: bold;
            display: inline-block;
            border-radius: 4px;
            margin: 2px 0;
            text-transform: uppercase;
        }
        
        /* Pie de página con el Código Identificador de la Pieza */
        .serial-txt {
            font-family: 'Courier', monospace;
            font-size: 10px;
            font-weight: bold;
            background-color: #f8fafc;
            padding: 5px;
            display: block;
            margin-top: 6px;
            border-top: 1px dashed #e2e8f0;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <div class="grid-etiquetas">
        @foreach($etiquetasFinales as $etiqueta)
            <div class="card-sticker">
                
                {{-- ══ CABECERA CORPORATIVA DINÁMICA ══ --}}
                <div class="header-corporativo" style="background-color: {{ $etiqueta['tienda_color'] }};">
                    @if(isset($logoPath) && $logoPath !== null)
                        {{-- Si el controlador validó que el archivo existe en el disco, DomPDF lo estampa --}}
                        <img src="{{ $logoPath }}" class="logo-img" alt="Logo">
                    @else
                        {{-- Icono de respaldo si la tienda no tiene un logotipo físico guardado --}}
                        <span style="vertical-align: middle; margin-right: 4px;">⛺</span>
                    @endif
                    <span class="texto-tienda">{{ $etiqueta['tienda_nom'] }}</span>
                </div>
                
                {{-- ══ CUERPO TÉCNICO DE CONTROL ══ --}}
                <div class="cuerpo-sticker">
                    <h5 class="danza-titulo">{{ str_replace([' - Varón', ' - Damas'], '', $etiqueta['traje_nom']) }}</h5>
                    <p class="pieza-nombre">{{ $etiqueta['pieza_nom'] }}</p>
                    <span class="talla-badge">TALLA: {{ $etiqueta['talla'] }}</span>
                </div>
                
                {{-- ══ PIE DE SEGURIDAD (SERIAL COMPLETO POR PIEZA) ══ --}}
                <span class="serial-txt">{{ $etiqueta['serial_pieza'] }}</span>
                
            </div>
        @endforeach
    </div>

</body>
</html>
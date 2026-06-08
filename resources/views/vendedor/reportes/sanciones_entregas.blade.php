<x-app-layout>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ══ ENCABEZADO CON DISEÑO PREMIUM CLARO ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-6 gap-4">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                    Sanciones y Entregas
                </h1>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-1">
                    Módulo Estadístico Parametrizado · Panel analítico interactivo
                </p>
            </div>
            {{-- Botón PDF adaptado a Estilo Corporativo --}}
            <a href="{{ route('vendedor.reportes.sanciones_entregas.pdf', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition duration-150">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </a>
        </div>

        {{-- ══ TARJETAS RESUMEN (KPIs) EN CAJAS BLANCAS REDONDEADAS ═════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            {{-- KPI 1: Sanciones --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-400">Total Sanciones</span>
                    <h3 class="text-4xl font-black text-red-600 mt-2">
                        {{ $resumen['total_sanciones'] ?? 0 }}
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">
                    Bs {{ number_format($resumen['monto_sanciones'] ?? 0, 2) }}
                </p>
            </div>

            {{-- KPI 2: Sanciones Pendientes --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-yellow-500">Sanciones Pendientes</span>
                    <h3 class="text-4xl font-black text-yellow-600 mt-2">
                        {{ $resumen['sanciones_pendientes'] ?? 0 }}
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">
                    {{ $resumen['sanciones_pagadas'] ?? 0 }} pagadas en total
                </p>
            </div>

            {{-- KPI 3: Entregas --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-green-500">Total Entregas</span>
                    <h3 class="text-4xl font-black text-green-600 mt-2">
                        {{ $resumen['total_entregas'] ?? 0 }}
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">
                    Bs {{ number_format($resumen['monto_entregas'] ?? 0, 2) }}
                </p>
            </div>

            {{-- KPI 4: En Mora --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-orange-500">En Mora</span>
                    <h3 class="text-4xl font-black text-orange-600 mt-2">
                        {{ $resumen['entregas_en_mora'] ?? 0 }}
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">
                    {{ $resumen['entregas_devueltas'] ?? 0 }} devueltos a almacén
                </p>
            </div>
        </div>

        {{-- ══ SECCIÓN DE GRÁFICOS ESTADÍSTICOS INTERACTIVOS (FONDO CLARO) ══════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Gráfico 1: Análisis Económico (Dona) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Balance Financiero (Monto en Bs)
                    </h3>
                    <div class="relative min-h-[220px] max-h-[220px] flex items-center justify-center">
                        <canvas id="chartFinanzas"></canvas>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 font-bold uppercase text-center tracking-wider mt-6">
                    Comparativa porcentual entre el valor recaudado de Alquileres vs. Penalizaciones.
                </p>
            </div>

            {{-- Gráfico 2: Frecuencia Operativa (Barras) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Rendimiento Volumétrico (Registros)
                    </h3>
                    <div class="relative min-h-[220px] max-h-[220px] flex items-center justify-center">
                        <canvas id="chartVolumen"></canvas>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 font-bold uppercase text-center tracking-wider mt-6">
                    Auditoría de flujo físico de prendas en sucursal según los filtros seleccionados.
                </p>
            </div>
        </div>

        {{-- ══ FILTROS PARAMETRIZADOS CLAROS Y MINIMALISTAS ═════════════════════════════════════════ --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="mb-6">
                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800">
                    🔍 Buscador Parametrizado Estadístico
                </h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">
                    Filtra y extrae reportes consolidados en tiempo real
                </p>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 p-5 rounded-3xl">
                {{-- Tipo de reporte --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Ver</label>
                    <select name="tipo_reporte"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="ambos" {{ ($filtros['tipo_reporte'] ?? 'ambos') === 'ambos' ? 'selected' : '' }}>Sanciones + Entregas</option>
                        <option value="sanciones" {{ ($filtros['tipo_reporte'] ?? '') === 'sanciones' ? 'selected' : '' }}>Solo Sanciones</option>
                        <option value="entregas" {{ ($filtros['tipo_reporte'] ?? '') === 'entregas' ? 'selected' : '' }}>Solo Entregas</option>
                    </select>
                </div>

                {{-- Tienda --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Tienda</label>
                    <select name="cod_tienda"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todas mis tiendas --</option>
                        @foreach($tiendas as $tiendaItem)
                            <option value="{{ $tiendaItem->cod_tienda }}" {{ ($filtros['cod_tienda'] ?? '') == $tiendaItem->cod_tienda ? 'selected' : '' }}>
                                {{ $tiendaItem->nom_tie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo sanción --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Tipo Sanción</label>
                    <select name="tipo_sancion"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todos los Tipos --</option>
                        @foreach(['Retraso', 'Daño', 'Perdida', 'Limpieza'] as $tipo)
                            <option value="{{ $tipo }}" {{ ($filtros['tipo_sancion'] ?? '') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado entrega --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Estado Entrega</label>
                    <select name="est_alquiler"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todos los Estados --</option>
                        @foreach(['Entregado', 'Devuelto', 'En Mora'] as $est)
                            <option value="{{ $est }}" {{ ($filtros['est_alquiler'] ?? '') === $est ? 'selected' : '' }}>{{ $est }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sanción pagada --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Sanción Pagada</label>
                    <select name="pagada"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todos --</option>
                        <option value="1" {{ ($filtros['pagada'] ?? '') === '1' ? 'selected' : '' }}>Pagada</option>
                        <option value="0" {{ ($filtros['pagada'] ?? '') === '0' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>

                {{-- Fecha desde --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Desde</label>
                    <input type="date" name="fec_desde" value="{{ $filtros['fec_desde'] ?? '' }}"
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 text-gray-700 font-medium">
                </div>

                {{-- Fecha hasta --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Hasta</label>
                    <input type="date" name="fec_hasta" value="{{ $filtros['fec_hasta'] ?? '' }}"
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 text-gray-700 font-medium">
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 text-white text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl shadow bg-gray-900 hover:bg-gray-800 transition duration-150 text-center">
                        Filtrar
                    </button>
                    <a href="{{ url()->current() }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-600 text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl transition duration-150 text-center">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- ══ REPOSITORIO DE LA TABLA DE SANCIONES ═══════════════════════════════════════════════ --}}
        @if(in_array(($filtros['tipo_reporte'] ?? 'ambos'), ['sanciones', 'ambos']))
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="mb-6">
                <h2 class="text-md font-black uppercase tracking-wider text-gray-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 inline-block"></span>
                    Módulo de Sanciones Registradas
                    <span class="text-gray-400 font-bold text-xs uppercase tracking-normal">— {{ $sanciones->count() }} registros activos</span>
                </h2>
            </div>

            @if($sanciones->isEmpty())
                <div class="py-12 text-center text-gray-400 font-bold uppercase text-xs tracking-wider border-2 border-dashed border-gray-100 rounded-3xl">
                    ❌ No se encontraron sanciones aplicadas con los criterios seleccionados.
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="pb-3 pl-2">ID</th>
                            <th class="pb-3">Tipo Sanción</th>
                            <th class="pb-3">Cliente</th>
                            <th class="pb-3">Traje / Unidad</th>
                            <th class="pb-3">Evento Asociado</th>
                            <th class="pb-3 text-right">Monto</th>
                            <th class="pb-3 text-center">Pagada</th>
                            <th class="pb-3 pl-4">Descripción Contextual</th>
                            <th class="pb-3 text-right pr-2">Fecha Emisión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                        @foreach($sanciones as $s)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 pl-2 font-mono text-xs text-gray-400">#{{ $s->cod_sancion }}</td>
                            <td class="py-4">
                                @php
                                    $colorTipo = match($s->tipo_sancion) {
                                        'Retraso'  => 'bg-yellow-50 text-yellow-700 border-yellow-200/50',
                                        'Daño'     => 'bg-red-50 text-red-700 border-red-200/50',
                                        'Perdida'  => 'bg-rose-50 text-rose-700 border-rose-200/50',
                                        'Limpieza' => 'bg-blue-50 text-blue-700 border-blue-200/50',
                                        default    => 'bg-gray-50 text-gray-700 border-gray-200/50',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-tight border {{ $colorTipo }}">
                                    {{ $s->tipo_sancion }}
                                </span>
                            </td>
                            <td class="py-4 text-gray-900 font-bold">
                                {{ $s->alquiler->cliente->name ?? '—' }} {{ $s->alquiler->cliente->ap_pat ?? '' }}
                            </td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-xs">{{ $s->alquiler->unidadFisica->traje->nom_traje ?? $s->alquiler->unidad->traje->nom_traje ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">N/S: {{ $s->alquiler->unidadFisica->nro_serie_interno ?? $s->alquiler->unidad->nro_serie_interno ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="py-4 text-gray-400 text-xs font-bold uppercase tracking-wider">{{ $s->alquiler->evento->nom_evento ?? '—' }}</td>
                            <td class="py-4 text-right font-black text-red-600">
                                Bs {{ number_format($s->monto_sancion, 2) }}
                            </td>
                            <td class="py-4 text-center">
                                @if($s->pagada)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black uppercase bg-green-50 text-green-700 border border-green-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Sí
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black uppercase bg-yellow-50 text-yellow-700 border border-yellow-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-600"></span> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 pl-4 text-gray-500 text-xs max-w-xs truncate font-medium">{{ $s->descripcion ?? '—' }}</td>
                            <td class="py-4 text-right pr-2 text-gray-400 text-xs font-mono whitespace-nowrap">
                                {{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100 text-sm font-black text-gray-800 bg-gray-50/50">
                            <td colspan="5" class="py-4 pl-2 text-xs font-black uppercase text-gray-400 tracking-wider">Total Acumulado Sanciones</td>
                            <td class="py-4 text-right font-black text-red-600 text-base">
                                Bs {{ number_format($sanciones->sum('monto_sancion'), 2) }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
        @endif

        {{-- ══ REPOSITORIO DE LA TABLA DE ENTREGAS (ALQUILERES) ════════════════════════════════════════════════ --}}
        @if(in_array(($filtros['tipo_reporte'] ?? 'ambos'), ['entregas', 'ambos']))
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="mb-6">
                <h2 class="text-md font-black uppercase tracking-wider text-gray-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-600 inline-block"></span>
                    Módulo de Entregas y Despachos
                    <span class="text-gray-400 font-bold text-xs uppercase tracking-normal">— {{ $entregas->count() }} registros de flujo</span>
                </h2>
            </div>

            @if($entregas->isEmpty())
                <div class="py-12 text-center text-gray-400 font-bold uppercase text-xs tracking-wider border-2 border-dashed border-gray-100 rounded-3xl">
                    ❌ No se registraron entregas de prendas con los filtros aplicados.
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="pb-3 pl-2">ID</th>
                            <th class="pb-3">Cliente Destinatario</th>
                            <th class="pb-3">Traje / Especificaciones</th>
                            <th class="pb-3">Evento</th>
                            <th class="pb-3">Fec. Salida</th>
                            <th class="pb-3">Retorno Prev.</th>
                            <th class="pb-3">Retorno Real</th>
                            <th class="pb-3 text-right">Monto Cobrado</th>
                            <th class="pb-3 text-center">Estado Flujo</th>
                            <th class="pb-3 text-center pr-2">Sanciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                        @foreach($entregas as $e)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 pl-2 font-mono text-xs text-gray-400">#{{ $e->cod_alquiler }}</td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900">{{ $e->cliente->name ?? '—' }} {{ $e->cliente->ap_pat ?? '' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold tracking-tight font-mono">{{ $e->nro_celular_cliente ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-xs">{{ $e->unidadFisica->traje->nom_traje ?? $e->unidad->traje->nom_traje ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">
                                        Talla: {{ $e->unidadFisica->talla ?? $e->unidad->talla ?? '—' }} · N/S: {{ $e->unidadFisica->nro_serie_interno ?? $e->unidad->nro_serie_interno ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 text-gray-400 text-xs font-bold uppercase tracking-wider">{{ $e->evento->nom_evento ?? '—' }}</td>
                            <td class="py-4 text-gray-600 text-xs font-mono whitespace-nowrap">
                                {{ $e->fec_salida ? \Carbon\Carbon::parse($e->fec_salida)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-4 text-gray-600 text-xs font-mono whitespace-nowrap">
                                {{ $e->fec_retorno_prev ? \Carbon\Carbon::parse($e->fec_retorno_prev)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-4 text-xs font-mono whitespace-nowrap
                                {{ $e->fec_retorno_real && $e->fec_retorno_real > $e->fec_retorno_prev ? 'text-orange-600 font-bold' : 'text-gray-600' }}">
                                {{ $e->fec_retorno_real ? \Carbon\Carbon::parse($e->fec_retorno_real)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-4 text-right font-black text-green-600">
                                Bs {{ number_format($e->monto_total, 2) }}
                            </td>
                            <td class="py-4 text-center">
                                @php
                                    $colorEst = match($e->est_alquiler) {
                                        'Entregado' => 'bg-blue-50 text-blue-700 border-blue-200/50',
                                        'Devuelto'  => 'bg-green-50 text-green-700 border-green-200/50',
                                        'En Mora'   => 'bg-orange-50 text-orange-700 border-orange-200/50',
                                        default     => 'bg-gray-50 text-gray-700 border-gray-200/50',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-tight border {{ $colorEst }}">
                                    {{ $e->est_alquiler }}
                                </span>
                            </td>
                            <td class="py-4 text-center pr-2">
                                @if($e->sanciones && $e->sanciones->count() > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs font-black">
                                        {{ $e->sanciones->count() }}
                                    </span>
                                @else
                                    <span class="text-gray-300 font-bold text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100 text-sm font-black text-gray-800 bg-gray-50/50">
                            <td colspan="7" class="py-4 pl-2 text-xs font-black uppercase text-gray-400 tracking-wider">Total Bruto Facturado</td>
                            <td class="py-4 text-right font-black text-green-600 text-base">
                                Bs {{ number_format($entregas->sum('monto_total'), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- ═══ MOTOR DE GRÁFICOS (CHART.JS CORREGIDO PARA PALETA CLARA) ═════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Datos Inyectados dinámicamente con fallbacks matemáticos limpios a nivel numérico
            const montoEntregas = Number("{{ $resumen['monto_entregas'] ?? 0 }}") || 0;
            const montoSanciones = Number("{{ $resumen['monto_sanciones'] ?? 0 }}") || 0;
            
            const totalEntregasCount = Number("{{ $resumen['total_entregas'] ?? 0 }}") || 0;
            const totalSancionesCount = Number("{{ $resumen['total_sanciones'] ?? 0 }}") || 0;
            const enMoraCount = Number("{{ $resumen['entregas_en_mora'] ?? 0 }}") || 0;

            // Gráfico 1: Dona de Finanzas (Estilo Claro)
            const ctxFinanzas = document.getElementById('chartFinanzas').getContext('2d');
            new Chart(ctxFinanzas, {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos Alquileres', 'Cargos por Sanción'],
                    datasets: [{
                        data: [montoEntregas, montoSanciones],
                        backgroundColor: ['#16a34a', '#dc2626'], 
                        borderWidth: 2,
                        borderColor: '#ffffff' 
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { 
                                color: '#4b5563', 
                                font: { size: 11, weight: 'bold' } 
                            }
                        }
                    },
                    cutout: '72%'
                }
            });

            // Gráfico 2: Barras de Volumen Operativo (Estilo Claro)
            const ctxVolumen = document.getElementById('chartVolumen').getContext('2d');
            new Chart(ctxVolumen, {
                type: 'bar',
                data: {
                    labels: ['Total Entregas', 'Total Sanciones', 'Órdenes en Mora'],
                    datasets: [{
                        data: [totalEntregasCount, totalSancionesCount, enMoraCount],
                        backgroundColor: ['#2563eb', '#e11d48', '#ea580c'], 
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#4b5563', font: { size: 10, weight: 'bold' } },
                            grid: { display: false }
                        },
                        y: {
                            ticks: { color: '#4b5563', font: { size: 10 } },
                            grid: { color: '#f3f4f6' } 
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
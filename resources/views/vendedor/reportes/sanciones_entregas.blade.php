{{-- resources/views/vendedor/reportes/sanciones_entregas.blade.php --}}

<x-app-layout>
@section('content')
<div class="min-h-screen bg-gray-950 text-gray-100 p-6 space-y-6">

    {{-- ══ ENCABEZADO ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Sanciones y Entregas</h1>
            <p class="text-sm text-gray-400 mt-0.5">Reporte parametrizado · Tus trajes entregados y sus sanciones</p>
        </div>
        {{-- Botón PDF --}}
        <a href="{{ route('vendedor.reportes.sanciones_entregas.pdf', request()->query()) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-andes-verde hover:bg-andes-verde/80 text-black font-semibold rounded-lg text-sm transition shadow-lg shadow-andes-verde/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar PDF
        </a>
    </div>

    {{-- ══ TARJETAS RESUMEN ══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Sanciones --}}
        <div class="bg-gray-900 border border-red-500/20 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Total Sanciones</p>
            <p class="text-2xl font-bold text-red-400">{{ $resumen['total_sanciones'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Bs {{ number_format($resumen['monto_sanciones'], 2) }}</p>
        </div>
        <div class="bg-gray-900 border border-yellow-500/20 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Sanciones Pendientes</p>
            <p class="text-2xl font-bold text-yellow-400">{{ $resumen['sanciones_pendientes'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $resumen['sanciones_pagadas'] }} pagadas</p>
        </div>
        {{-- Entregas --}}
        <div class="bg-gray-900 border border-andes-verde/20 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Total Entregas</p>
            <p class="text-2xl font-bold text-andes-verde">{{ $resumen['total_entregas'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Bs {{ number_format($resumen['monto_entregas'], 2) }}</p>
        </div>
        <div class="bg-gray-900 border border-orange-500/20 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">En Mora</p>
            <p class="text-2xl font-bold text-orange-400">{{ $resumen['entregas_en_mora'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $resumen['entregas_devueltas'] }} devueltos</p>
        </div>
    </div>

    {{-- ══ FILTROS ══════════════════════════════════════════════════════════ --}}
   <form method="GET" action="{{ route('vendedor.reportes.sanciones_entregas') }}">
          class="bg-gray-900/60 border border-white/5 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Filtros</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Tipo de reporte --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Ver</label>
                <select name="tipo_reporte"
                        class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
                    <option value="ambos"     {{ ($filtros['tipo_reporte'] ?? 'ambos') === 'ambos'    ? 'selected' : '' }}>Sanciones + Entregas</option>
                    <option value="sanciones" {{ ($filtros['tipo_reporte'] ?? '') === 'sanciones' ? 'selected' : '' }}>Solo Sanciones</option>
                    <option value="entregas"  {{ ($filtros['tipo_reporte'] ?? '') === 'entregas'  ? 'selected' : '' }}>Solo Entregas</option>
                </select>
            </div>

            {{-- Tienda --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Tienda</label>
                <select name="cod_tienda"
                        class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
                    <option value="">Todas mis tiendas</option>
                    @foreach($tiendas as $tienda)
                        <option value="{{ $tienda->cod_tienda }}"
                            {{ ($filtros['cod_tienda'] ?? '') == $tienda->cod_tienda ? 'selected' : '' }}>
                            {{ $tienda->nom_tie }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo sanción --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Tipo Sanción</label>
                <select name="tipo_sancion"
                        class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
                    <option value="">Todos</option>
                    @foreach(['Retraso', 'Daño', 'Perdida', 'Limpieza'] as $tipo)
                        <option value="{{ $tipo }}" {{ ($filtros['tipo_sancion'] ?? '') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Estado alquiler --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Estado Entrega</label>
                <select name="est_alquiler"
                        class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
                    <option value="">Todos</option>
                    @foreach(['Entregado', 'Devuelto', 'En Mora'] as $est)
                        <option value="{{ $est }}" {{ ($filtros['est_alquiler'] ?? '') === $est ? 'selected' : '' }}>{{ $est }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sanción pagada --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Sanción Pagada</label>
                <select name="pagada"
                        class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
                    <option value="">Todos</option>
                    <option value="1" {{ ($filtros['pagada'] ?? '') === '1' ? 'selected' : '' }}>Pagada</option>
                    <option value="0" {{ ($filtros['pagada'] ?? '') === '0' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>

            {{-- Fecha desde --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Desde</label>
                <input type="date" name="fec_desde" value="{{ $filtros['fec_desde'] ?? '' }}"
                       class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
            </div>

            {{-- Fecha hasta --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Hasta</label>
                <input type="date" name="fec_hasta" value="{{ $filtros['fec_hasta'] ?? '' }}"
                       class="w-full bg-gray-800 border border-white/10 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-andes-verde/50 focus:border-andes-verde outline-none">
            </div>

            {{-- Botones --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-andes-verde hover:bg-andes-verde/80 text-black font-semibold text-sm py-2 px-4 rounded-lg transition">
                    Filtrar
                </button>
                <a href="{{ route('vendedor.reportes.sanciones_entregas') }}"
   class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-lg transition">
   Limpiar
</a>
            </div>
        </div>
    </form>

    {{-- ══ TABLA SANCIONES ═══════════════════════════════════════════════ --}}
    @if(in_array($tipoReporte, ['sanciones', 'ambos']))
    <div class="space-y-3">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
            Sanciones
            <span class="text-gray-600 font-normal normal-case tracking-normal">— {{ $sanciones->count() }} registros</span>
        </h2>

        @if($sanciones->isEmpty())
            <div class="bg-gray-900/50 border border-white/5 rounded-xl py-10 text-center text-gray-500 text-sm">
                No hay sanciones con los filtros aplicados.
            </div>
        @else
        <div class="overflow-x-auto rounded-xl border border-white/5">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-gray-500 text-xs uppercase tracking-widest">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Traje / Unidad</th>
                        <th class="px-4 py-3 text-left">Evento</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Pagada</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($sanciones as $s)
                    <tr class="bg-gray-900/40 hover:bg-gray-800/40 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $s->cod_sancion }}</td>
                        <td class="px-4 py-3">
                            @php
                                $colorTipo = match($s->tipo_sancion) {
                                    'Retraso'  => 'bg-yellow-500/15 text-yellow-400',
                                    'Daño'     => 'bg-red-500/15 text-red-400',
                                    'Perdida'  => 'bg-rose-500/15 text-rose-400',
                                    'Limpieza' => 'bg-blue-500/15 text-blue-400',
                                    default    => 'bg-gray-500/15 text-gray-400',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $colorTipo }}">
                                {{ $s->tipo_sancion }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-200">
                            {{ $s->alquiler->cliente->name ?? '—' }}
                            {{ $s->alquiler->cliente->ap_pat ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-gray-300 text-xs">
                            {{ $s->alquiler->unidad->traje->nom_traje ?? '—' }}<br>
                            <span class="text-gray-500">N/S: {{ $s->alquiler->unidad->nro_serie_interno ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $s->alquiler->evento->nom_evento ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-400">
                            Bs {{ number_format($s->monto_sancion, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($s->pagada)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-500/15 text-green-400 text-xs rounded">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Sí
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-yellow-500/15 text-yellow-400 text-xs rounded">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ $s->descripcion ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-900 border-t border-white/10">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-xs text-gray-500 font-semibold uppercase">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-red-300">
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

    {{-- ══ TABLA ENTREGAS ════════════════════════════════════════════════ --}}
    @if(in_array($tipoReporte, ['entregas', 'ambos']))
    <div class="space-y-3">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-andes-verde inline-block"></span>
            Entregas
            <span class="text-gray-600 font-normal normal-case tracking-normal">— {{ $entregas->count() }} registros</span>
        </h2>

        @if($entregas->isEmpty())
            <div class="bg-gray-900/50 border border-white/5 rounded-xl py-10 text-center text-gray-500 text-sm">
                No hay entregas con los filtros aplicados.
            </div>
        @else
        <div class="overflow-x-auto rounded-xl border border-white/5">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-gray-500 text-xs uppercase tracking-widest">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Traje / Talla</th>
                        <th class="px-4 py-3 text-left">Evento</th>
                        <th class="px-4 py-3 text-left">Salida</th>
                        <th class="px-4 py-3 text-left">Retorno Prev.</th>
                        <th class="px-4 py-3 text-left">Retorno Real</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Sanciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($entregas as $e)
                    <tr class="bg-gray-900/40 hover:bg-gray-800/40 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $e->cod_alquiler }}</td>
                        <td class="px-4 py-3 text-gray-200">
                            {{ $e->cliente->name ?? '—' }} {{ $e->cliente->ap_pat ?? '' }}<br>
                            <span class="text-xs text-gray-500">{{ $e->nro_celular_cliente ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-300 text-xs">
                            {{ $e->unidad->traje->nom_traje ?? '—' }}<br>
                            <span class="text-gray-500">Talla: {{ $e->unidad->talla ?? '—' }} · N/S: {{ $e->unidad->nro_serie_interno ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $e->evento->nom_evento ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-300 text-xs whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($e->fec_salida)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-300 text-xs whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($e->fec_retorno_prev)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-xs whitespace-nowrap
                            {{ $e->fec_retorno_real && $e->fec_retorno_real > $e->fec_retorno_prev ? 'text-orange-400' : 'text-gray-300' }}">
                            {{ $e->fec_retorno_real ? \Carbon\Carbon::parse($e->fec_retorno_real)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-andes-verde">
                            Bs {{ number_format($e->monto_total, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $colorEst = match($e->est_alquiler) {
                                    'Entregado' => 'bg-blue-500/15 text-blue-400',
                                    'Devuelto'  => 'bg-green-500/15 text-green-400',
                                    'En Mora'   => 'bg-orange-500/15 text-orange-400',
                                    default     => 'bg-gray-500/15 text-gray-400',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $colorEst }}">
                                {{ $e->est_alquiler }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($e->sanciones && $e->sanciones->count() > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-500/15 text-red-400 text-xs rounded font-medium">
                                    {{ $e->sanciones->count() }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                </span>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-900 border-t border-white/10">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-xs text-gray-500 font-semibold uppercase">Total Facturado</td>
                        <td class="px-4 py-3 text-right font-bold text-andes-verde">
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
</x-app-layout>
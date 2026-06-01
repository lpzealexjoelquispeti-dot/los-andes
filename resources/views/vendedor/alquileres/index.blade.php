<x-app-layout>
@section('header', 'Gestión de Alquileres')

<style>
    .estado-tab {
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        padding: .5rem 1.1rem;
        border-radius: 999px;
        border: 2px solid transparent;
        transition: all .2s;
        cursor: pointer;
        white-space: nowrap;
    }
    .estado-tab.active { color: #fff; border-color: transparent; }
    .estado-tab:not(.active) { background: #f3f4f6; color: #6b7280; border-color: #e5e7eb; }
    .estado-tab:not(.active):hover { background: #e5e7eb; color: #374151; }

    .card-alquiler {
        background: #fff;
        border: 2px solid #f0f0f0;
        border-radius: 24px;
        transition: box-shadow .25s, border-color .25s, transform .2s;
    }
    .card-alquiler:hover { box-shadow: 0 8px 32px rgba(0,0,0,.08); border-color: #e0e0e0; transform: translateY(-1px); }

    .badge {
        font-size: .6rem; font-weight: 800;
        letter-spacing: .1em; text-transform: uppercase;
        padding: .25rem .7rem; border-radius: 999px;
    }
    .badge-pendiente  { background: #fef9c3; color: #854d0e; }
    .badge-reservado  { background: #dbeafe; color: #1e40af; }
    .badge-entregado  { background: #dcfce7; color: #166534; }
    .badge-devuelto   { background: #f3f4f6; color: #374151; }
    .badge-mora       { background: #fee2e2; color: #991b1b; }
    .badge-cancelado  { background: #f3f4f6; color: #9ca3af; }

    .btn-aprobar {
        background: #007A33; color: #fff;
        font-weight: 800; font-size: .65rem; letter-spacing: .12em; text-transform: uppercase;
        padding: .55rem 1.2rem; border-radius: 12px; border: none; cursor: pointer;
        transition: opacity .2s, transform .15s;
    }
    .btn-aprobar:hover { opacity: .88; transform: translateY(-1px); }

    .btn-rechazar {
        background: #fff; color: #DA291C;
        font-weight: 800; font-size: .65rem; letter-spacing: .12em; text-transform: uppercase;
        padding: .55rem 1.2rem; border-radius: 12px;
        border: 2px solid #fca5a5; cursor: pointer;
        transition: all .2s;
    }
    .btn-rechazar:hover { background: #fee2e2; border-color: #DA291C; }

    .fade-in { animation: fadeIn .35s ease both; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
</style>

<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- ── CABECERA ── --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[.2em] text-gray-400 mb-1">Panel vendedor</p>
            <h1 class="text-2xl font-black text-andes-oscuro uppercase tracking-tight">Alquileres</h1>
        </div>

        {{-- Contador rápido --}}
        <div class="flex gap-3">
            @php
                $countPendiente = $conteos['Pendiente_Aprobacion'] ?? 0;
                $countMora      = $conteos['En Mora'] ?? 0;
            @endphp
            @if($countPendiente > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-4 py-2 text-center">
                    <p class="text-xl font-black text-yellow-700">{{ $countPendiente }}</p>
                    <p class="text-[9px] font-black uppercase tracking-wider text-yellow-600">Por aprobar</p>
                </div>
            @endif
            @if($countMora > 0)
                <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-2 text-center">
                    <p class="text-xl font-black text-red-700">{{ $countMora }}</p>
                    <p class="text-[9px] font-black uppercase tracking-wider text-red-600">En mora</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── TABS DE ESTADO ── --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-8 hide-scrollbar">
        @php
            $estados = [
                ''                    => ['label' => 'Todos',        'color' => '#1A1A1A'],
                'Pendiente_Aprobacion'=> ['label' => 'Por aprobar',  'color' => '#DA291C'],
                'Reservado'           => ['label' => 'Reservados',   'color' => '#1e40af'],
                'Entregado'           => ['label' => 'Entregados',   'color' => '#007A33'],
                'Devuelto'            => ['label' => 'Devueltos',    'color' => '#374151'],
                'En Mora'             => ['label' => 'En mora',      'color' => '#DA291C'],
                'Cancelado'           => ['label' => 'Cancelados',   'color' => '#6b7280'],
            ];
            $estadoActual = request('estado', '');
        @endphp

        @foreach($estados as $val => $meta)
            <a href="{{ route('vendedor.alquileres.index', $val ? ['estado' => $val] : []) }}"
               class="estado-tab {{ $estadoActual === $val ? 'active' : '' }}"
               style="{{ $estadoActual === $val ? 'background-color:' . $meta['color'] . '; border-color:' . $meta['color'] : '' }}">
                {{ $meta['label'] }}
                @if(isset($conteos[$val]) && $conteos[$val] > 0)
                    <span class="ml-1 opacity-70">({{ $conteos[$val] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- ── FLASH ── --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl px-5 py-3 flex items-center gap-3 fade-in">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ── LISTA DE ALQUILERES ── --}}
    @forelse($alquileres as $alquiler)
        @php
            $traje   = $alquiler->unidadFisica->traje;
            $evento  = $alquiler->evento;
            $cliente = $alquiler->cliente;
            $imagen  = $traje->imagenes->first()?->ruta_img;

            $badgeClass = match($alquiler->est_alquiler) {
                'Pendiente_Aprobacion' => 'badge-pendiente',
                'Reservado'            => 'badge-reservado',
                'Entregado'            => 'badge-entregado',
                'Devuelto'             => 'badge-devuelto',
                'En Mora'              => 'badge-mora',
                default                => 'badge-cancelado',
            };
            $badgeLabel = match($alquiler->est_alquiler) {
                'Pendiente_Aprobacion' => '⏳ Por aprobar',
                'Reservado'            => '📅 Reservado',
                'Entregado'            => '✅ Entregado',
                'Devuelto'             => '↩️ Devuelto',
                'En Mora'              => '🚨 En mora',
                'Cancelado'            => '✗ Cancelado',
                default                => $alquiler->est_alquiler,
            };
        @endphp

        <div class="card-alquiler p-6 mb-4 fade-in {{ $alquiler->est_alquiler === 'Pendiente_Aprobacion' ? 'border-yellow-200 bg-yellow-50/30' : '' }}">
            <div class="flex flex-col lg:flex-row gap-5">

                {{-- Foto del traje --}}
                <div class="w-full lg:w-20 h-24 lg:h-20 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0">
                    @if($imagen)
                        <img src="{{ asset('storage/'.$imagen) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start gap-2 mb-2">
                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">#{{ $alquiler->cod_alquiler }}</span>
                    </div>

                    <h3 class="text-base font-black text-andes-oscuro uppercase leading-tight mb-1">
                        {{ $traje->nom_traje }}
                    </h3>

                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-[11px] text-gray-500 font-medium">
                        <span>
                            <span class="font-black text-gray-700">{{ $cliente->name }} {{ $cliente->ap_pat }}</span>
                        </span>
                        <span>📱 {{ $alquiler->nro_celular_cliente ?? '—' }}</span>
                        <span>🎭 {{ $evento->nom_evento }}</span>
                        <span>📅 {{ \Carbon\Carbon::parse($alquiler->fec_salida)->format('d M') }} → {{ \Carbon\Carbon::parse($alquiler->fec_retorno_prev)->format('d M Y') }}</span>
                        <span>👕 Talla {{ $alquiler->unidadFisica->talla }}</span>
                    </div>
                </div>

                {{-- Monto + acciones --}}
                <div class="flex flex-col items-end justify-between gap-3 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-[9px] font-black uppercase tracking-wider text-gray-400">Total</p>
                        <p class="text-lg font-black text-andes-oscuro">Bs. {{ number_format($alquiler->monto_total, 0) }}</p>
                        <p class="text-[10px] font-semibold text-andes-verde">Seña: Bs. {{ number_format($alquiler->monto_sena, 0) }}</p>
                    </div>

                    <div class="flex gap-2">
                        {{-- Ver detalle siempre disponible --}}
                        <a href="{{ route('vendedor.alquileres.show', $alquiler->cod_alquiler) }}"
                           class="text-[10px] font-black uppercase tracking-wider text-gray-500 hover:text-gray-800 
                                  border-2 border-gray-200 hover:border-gray-400 px-3 py-2 rounded-xl transition">
                            Ver detalle
                        </a>

                        {{-- Aprobar/rechazar solo si está pendiente --}}
                        @if($alquiler->est_alquiler === 'Pendiente_Aprobacion')
                            <form method="POST" action="{{ route('vendedor.alquileres.aprobar', $alquiler->cod_alquiler) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-aprobar">✓ Aprobar</button>
                            </form>
                        @endif

                        {{-- Entregar si está reservado --}}
                        @if($alquiler->est_alquiler === 'Reservado')
                            <form method="POST" action="{{ route('vendedor.alquileres.entregar', $alquiler->cod_alquiler) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-aprobar" style="background:#1A1A1A">📦 Entregar</button>
                            </form>
                        @endif

                        {{-- Devolver si está entregado --}}
                        @if($alquiler->est_alquiler === 'Entregado')
                            <a href="{{ route('vendedor.alquileres.show', $alquiler->cod_alquiler) }}"
                               class="btn-aprobar" style="background:#DA291C; display:inline-block">↩ Devolver</a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Alerta si tiene garante --}}
            @if($alquiler->nombre_garante)
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-[10px] font-semibold text-gray-500">
                        Garante: <span class="font-black text-gray-700">{{ $alquiler->nombre_garante }}</span>
                        — CI: <span class="font-black text-gray-700">{{ $alquiler->ci_garante }}</span>
                    </p>
                </div>
            @endif
        </div>

    @empty
        <div class="py-20 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="1" d="M9 14l2 2 4-4M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z"/>
            </svg>
            <p class="text-lg font-black text-gray-400 uppercase tracking-tight">Sin alquileres en este estado</p>
            <p class="text-[11px] text-gray-400 font-medium mt-1 uppercase tracking-wider">Cambia el filtro para ver otros registros</p>
        </div>
    @endforelse

    {{-- Paginación --}}
    <div class="mt-8">{{ $alquileres->appends(request()->query())->links() }}</div>

</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

</x-app-layout>

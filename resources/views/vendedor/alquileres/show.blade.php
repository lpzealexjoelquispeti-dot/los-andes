<x-app-layout>
@section('header', 'Detalle de Alquiler #' . $alquiler->cod_alquiler)

<style>
    .info-block { background: #fafafa; border: 2px solid #f0f0f0; border-radius: 20px; padding: 1.25rem 1.5rem; }
    .info-label { font-size: .65rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: .2rem; }
    .info-value { font-size: .9rem; font-weight: 700; color: #111827; }

    .badge { font-size: .6rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; padding: .3rem .8rem; border-radius: 999px; }
    .badge-pendiente { background: #fef9c3; color: #854d0e; }
    .badge-reservado { background: #dbeafe; color: #1e40af; }
    .badge-entregado { background: #dcfce7; color: #166534; }
    .badge-devuelto  { background: #f3f4f6; color: #374151; }
    .badge-mora      { background: #fee2e2; color: #991b1b; }
    .badge-cancelado { background: #f3f4f6; color: #9ca3af; }

    .btn-primary {
        font-weight: 800; font-size: .7rem;
        letter-spacing: .12em; text-transform: uppercase;
        padding: .8rem 1.8rem; border-radius: 14px; border: none; cursor: pointer;
        transition: opacity .2s, transform .15s; color: #fff;
    }
    .btn-primary:hover { opacity: .88; transform: translateY(-1px); }

    .btn-outline {
        font-weight: 700; font-size: .7rem;
        letter-spacing: .1em; text-transform: uppercase;
        padding: .8rem 1.5rem; border-radius: 14px; cursor: pointer;
        background: #fff; transition: all .2s;
    }

    .timeline-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
    .timeline-line { width: 2px; background: #e5e7eb; flex-shrink: 0; margin-left: 4px; }

    .comprobante-img {
        border-radius: 18px; border: 2px solid #e5e7eb; overflow: hidden;
        max-height: 380px; cursor: zoom-in; transition: transform .2s;
    }
    .comprobante-img:hover { transform: scale(1.01); }

    .fade-in { animation: fadeIn .35s ease both; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
</style>

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
        'Pendiente_Aprobacion' => '⏳ Pendiente de aprobación',
        'Reservado'            => '📅 Reservado — listo para entrega',
        'Entregado'            => '✅ Traje entregado',
        'Devuelto'             => '↩️ Traje devuelto',
        'En Mora'              => '🚨 En mora',
        'Cancelado'            => '✗ Cancelado',
        default                => $alquiler->est_alquiler,
    };
@endphp

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-8 text-[11px] font-bold uppercase tracking-widest text-gray-400">
        <a href="{{ route('vendedor.alquileres.index') }}" class="hover:text-gray-700 transition">Alquileres</a>
        <span class="text-gray-300">›</span>
        <span class="text-gray-600">#{{ $alquiler->cod_alquiler }}</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl px-5 py-3 flex items-center gap-3 fade-in">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Encabezado con estado --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <span class="badge {{ $badgeClass }} text-sm px-4 py-1.5 mb-3 inline-block">{{ $badgeLabel }}</span>
            <h1 class="text-2xl lg:text-3xl font-black text-andes-oscuro uppercase tracking-tight">{{ $traje->nom_traje }}</h1>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                Solicitud #{{ $alquiler->cod_alquiler }} — {{ \Carbon\Carbon::parse($alquiler->created_at)->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══ COLUMNA IZQUIERDA (2/3) ══ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Traje + Unidad --}}
            <div class="info-block flex gap-4 fade-in">
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                    @if($imagen)
                        <img src="{{ asset('storage/'.$imagen) }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1">
                    <span class="info-label">Traje alquilado</span>
                    <p class="text-lg font-black text-andes-oscuro uppercase tracking-tight">{{ $traje->nom_traje }}</p>
                    <div class="flex gap-3 mt-1 text-[11px] font-medium text-gray-500">
                        <span>Talla <strong>{{ $alquiler->unidadFisica->talla }}</strong></span>
                        <span>·</span>
                        <span>Serie <strong>{{ $alquiler->unidadFisica->nro_serie_interno }}</strong></span>
                        <span>·</span>
                        <span>{{ $alquiler->unidadFisica->estado_fisico }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="info-label">Total</span>
                    <p class="text-2xl font-black text-andes-oscuro">Bs. {{ number_format($alquiler->monto_total, 0) }}</p>
                    <p class="text-[10px] font-semibold text-andes-verde mt-0.5">Seña: Bs. {{ number_format($alquiler->monto_sena, 0) }}</p>
                </div>
            </div>

            {{-- Datos del cliente --}}
            <div class="info-block fade-in">
                <p class="info-label mb-4">Datos del cliente</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="info-label">Nombre</span>
                        <p class="info-value">{{ $cliente->name }} {{ $cliente->ap_pat }}</p>
                    </div>
                    <div>
                        <span class="info-label">Email</span>
                        <p class="info-value text-sm">{{ $cliente->email }}</p>
                    </div>
                    <div>
                        <span class="info-label">WhatsApp</span>
                        <div class="flex items-center gap-2">
                            <p class="info-value">+591 {{ $alquiler->nro_celular_cliente ?? '—' }}</p>
                            @if($alquiler->nro_celular_cliente)
                                <a href="https://wa.me/591{{ $alquiler->nro_celular_cliente }}"
                                   target="_blank"
                                   class="text-andes-verde hover:text-green-700 transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Garante si existe --}}
                @if($alquiler->nombre_garante)
                    <div class="border-t border-gray-100 mt-4 pt-4 grid grid-cols-2 gap-4">
                        <div>
                            <span class="info-label">Garante</span>
                            <p class="info-value">{{ $alquiler->nombre_garante }}</p>
                        </div>
                        <div>
                            <span class="info-label">CI del garante</span>
                            <p class="info-value">{{ $alquiler->ci_garante }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Evento y fechas --}}
            <div class="info-block fade-in">
                <p class="info-label mb-4">Evento y fechas</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="info-label">Evento</span>
                        <p class="info-value">{{ $evento->nom_evento }}</p>
                    </div>
                    <div>
                        <span class="info-label">Fecha salida</span>
                        <p class="info-value">{{ \Carbon\Carbon::parse($alquiler->fec_salida)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="info-label">Retorno previsto</span>
                        <p class="info-value">{{ \Carbon\Carbon::parse($alquiler->fec_retorno_prev)->format('d/m/Y') }}</p>
                    </div>
                    @if($alquiler->fec_retorno_real)
                        <div>
                            <span class="info-label">Retorno real</span>
                            <p class="info-value text-andes-verde">{{ \Carbon\Carbon::parse($alquiler->fec_retorno_real)->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sanciones si las hay --}}
            @if($alquiler->sanciones?->count())
                <div class="info-block border-red-100 bg-red-50/40 fade-in">
                    <p class="info-label text-red-500 mb-3">Sanciones applied</p>
                    <div class="space-y-2">
                        @foreach($alquiler->sanciones as $sancion)
                            <div class="flex items-center justify-between py-2 border-b border-red-100 last:border-0">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $sancion->tipo_sancion === 'Dano' ? 'Daño / Rotura' : $sancion->tipo_sancion }}
                                    </p>
                                    @if($sancion->descripcion)
                                        <p class="text-[11px] text-gray-500">{{ $sancion->descripcion }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-red-700">Bs. {{ number_format($sancion->monto_sancion, 0) }}</p>
                                    <p class="text-[9px] font-bold {{ $sancion->pagada ? 'text-andes-verde' : 'text-red-500' }}">
                                        {{ $sancion->pagada ? 'Pagada' : 'Pendiente' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Motivo de rechazo si fue cancelado --}}
            @if($alquiler->motivo_rechazo)
                <div class="info-block border-red-200 bg-red-50 fade-in">
                    <span class="info-label text-red-500">Motivo de rechazo</span>
                    <p class="info-value text-red-800 mt-1">{{ $alquiler->motivo_rechazo }}</p>
                </div>
            @endif

        </div>

        {{-- ══ COLUMNA DERECHA (1/3) ══ --}}
        <div class="space-y-5">

            {{-- COMPROBANTE DE PAGO --}}
            <div class="info-block fade-in">
                <p class="info-label mb-3">Comprobante de seña</p>
                @if($alquiler->comprobante_pago_path)
                    <a href="{{ asset('storage/'.$alquiler->comprobante_pago_path) }}" target="_blank">
                        <div class="comprobante-img">
                            <img src="{{ asset('storage/'.$alquiler->comprobante_pago_path) }}"
                                 class="w-full object-contain"
                                 alt="Comprobante de pago">
                        </div>
                    </a>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-2 text-center">
                        Toca para ampliar
                    </p>
                @else
                    <div class="py-8 text-center text-gray-300">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs font-bold">Sin comprobante adjunto</p>
                    </div>
                @endif
            </div>

            {{-- ACCIONES SEGÚN ESTADO COMERCIAL --}}
            <div class="space-y-3 fade-in">

                {{-- ── PENDIENTE: aprobar o rechazar ── --}}
                @if($alquiler->est_alquiler === 'Pendiente_Aprobacion')
                    <form method="POST" action="{{ route('vendedor.alquileres.aprobar', $alquiler->cod_alquiler) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary w-full" style="background:#007A33">
                            ✓ Aprobar solicitud
                        </button>
                    </form>

                    <div x-data="{ showRechazo: false }">
                        <button type="button" @click="showRechazo = !showRechazo"
                                class="btn-outline w-full border-2 border-red-200 text-andes-rojo hover:bg-red-50 hover:border-red-400">
                            ✗ Rechazar solicitud
                        </button>
                        <div x-show="showRechazo" x-cloak x-transition class="mt-3">
                            <form method="POST" action="{{ route('vendedor.alquileres.rechazar', $alquiler->cod_alquiler) }}"
                                  class="space-y-3">
                                @csrf @method('PATCH')
                                <textarea name="motivo_rechazo" required rows="3"
                                          placeholder="Escribe el motivo del rechazo para notificar al cliente..."
                                          class="w-full border-2 border-gray-200 rounded-xl p-3 text-sm font-medium resize-none focus:border-andes-rojo focus:ring-0 outline-none"></textarea>
                                <button type="submit" class="btn-primary w-full" style="background:#DA291C">
                                    Confirmar rechazo
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- ── RESERVADO: marcar como entregado ── --}}
                @if($alquiler->est_alquiler === 'Reservado')
                    <form method="POST" action="{{ route('vendedor.alquileres.entregar', $alquiler->cod_alquiler) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary w-full" style="background:#1A1A1A">
                            📦 Marcar como entregado
                        </button>
                    </form>
                    <form method="POST" action="{{ route('vendedor.alquileres.cancelar', $alquiler->cod_alquiler) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-outline w-full border-2 border-gray-200 text-gray-500 hover:border-gray-400"
                                onclick="return confirm('¿Cancelar este alquiler reservado?')">
                            Cancelar reserva
                        </button>
                    </form>
                @endif

                {{-- ── ENTREGADO: Levantar modales de devolución o sanción con Alpine ── --}}
                @if($alquiler->est_alquiler === 'Entregado')
                    <button type="button" class="btn-primary w-full text-center block text-white font-black" style="background:#007A33"
                            x-data 
                            @click="$dispatch('open-devolucion')">
                        ↩ Registrar Devolución
                    </button>
                    
                    <button type="button" class="btn-outline w-full border-2 border-orange-200 text-orange-600 hover:bg-orange-50 text-center block"
                            x-data
                            @click="$dispatch('open-sanciones')">
                        ⚠ Aplicar Sanción / Multa
                    </button>
                @endif

                {{-- Botón de retorno general --}}
                <a href="{{ route('vendedor.alquileres.index') }}"
                   class="btn-outline w-full border-2 border-gray-200 text-gray-500 hover:border-gray-400 text-center block">
                    ← Volver a alquileres
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ── MODAL DE SANCIONES ANIDADAS ── --}}
<div x-data="{ open: false }" 
     @open-sanciones.window="open = true" 
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition>
    
    <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl border border-gray-100 space-y-4"
         @click.away="open = false">
        
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">⚠ Registrar Sanción / Daño</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 font-bold text-xs">✕</button>
        </div>

        <form method="POST" action="{{ route('vendedor.alquileres.sanciones.store', $alquiler->cod_alquiler) }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Tipo de Falta</label>
                <select name="tipo_sancion" class="w-full rounded-xl border-gray-200 text-xs font-bold p-3 bg-gray-50" required>
                    <option value="">-- Selecciona el tipo --</option>
                    <option value="Retraso">Entrega Tardía (Mora)</option>
                    <option value="Dano">Prenda Dañada / Rasgada</option>
                    <option value="Perdida">Pérdida Total del Traje</option>
                    <option value="Limpieza">Requiere Limpieza Especial</option>
                </select>
            </div>

            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Monto de la multa (Bs.)</label>
                <input type="number" name="monto_sancion" min="1" step="1" required placeholder="Ej. 50"
                       class="w-full rounded-xl border-gray-200 text-xs font-bold p-3">
            </div>

            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Detalle o Justificación</label>
                <textarea name="descripcion" rows="3" required placeholder="Ej. Presenta una rotura en el ala izquierda..."
                          class="w-full rounded-xl border-gray-200 text-xs font-bold p-3"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="open = false" class="flex-1 py-3 bg-gray-100 text-gray-600 text-[11px] font-black uppercase tracking-wider rounded-xl">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3 bg-orange-600 text-white text-[11px] font-black uppercase tracking-wider rounded-xl shadow-lg shadow-orange-600/20">
                    Aplicar Sanción
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL PARA REGISTRAR LA DEVOLUCIÓN FÍSICA ── --}}
<div x-data="{ openDevolucion: false }" 
     @open-devolucion.window="openDevolucion = true" 
     x-show="openDevolucion" 
     x-cloak
     class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition>
    
    <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl border border-gray-100 space-y-4"
         @click.away="openDevolucion = false">
        
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">↩ Registrar Retorno de Prenda</h3>
            <button @click="openDevolucion = false" class="text-gray-400 hover:text-gray-600 font-bold text-xs">✕</button>
        </div>

        <form method="POST" action="{{ route('vendedor.alquileres.devolver', $alquiler->cod_alquiler) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            
            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Fecha de Entrega Real</label>
                <input type="date" name="fec_retorno_real" required 
                       value="{{ old('fec_retorno_real', now()->format('Y-m-d')) }}"
                       class="w-full rounded-xl border-gray-200 text-xs font-bold p-3 bg-gray-50">
            </div>

            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Estado Físico de Retorno</label>
                <select name="estado_fisico" class="w-full rounded-xl border-gray-200 text-xs font-bold p-3 bg-gray-50" required>
                    <option value="Buen Estado" {{ old('estado_fisico') == 'Buen Estado' ? 'selected' : '' }}>Buen Estado (Listo para lavar/alquilar)</option>
                    <option value="Nuevo" {{ old('estado_fisico') == 'Nuevo' ? 'selected' : '' }}>Como Nuevo</option>
                    <option value="Desgastado" {{ old('estado_fisico') == 'Desgastado' ? 'selected' : '' }}>Desgastado por uso regular</option>
                    <option value="En Reparación" {{ old('estado_fisico') == 'En Reparación' ? 'selected' : '' }}>🛠️ Requiere ingresar a Taller (Desactiva disponibilidad)</option>
                </select>
            </div>

            <div>
                <label class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Observaciones de Recepción</label>
                <textarea name="observaciones" rows="3" placeholder="Ej. Ninguna pieza faltante, faja en buen estado..."
                          class="w-full rounded-xl border-gray-200 text-xs font-bold p-3">{{ old('observaciones', 'Devuelto conforme.') }}</textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="openDevolucion = false" class="flex-1 py-3 bg-gray-100 text-gray-600 text-[11px] font-black uppercase tracking-wider rounded-xl">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-600/20">
                    Confirmar Retorno
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
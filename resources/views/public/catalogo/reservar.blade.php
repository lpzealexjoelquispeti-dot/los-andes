<x-public-layout>

{{-- ═══════════════════════════════════════════════
     PÁGINA DE RESERVA — WIZARD 3 PASOS
     Ruta esperada: GET /reservar/{cod_unidad}
     Variables blade: $unidad (con traje, tienda, diseno)
     ═══════════════════════════════════════════════ --}}

@php
    $traje      = $unidad->traje;
    $tienda     = $traje->tienda;
    $color      = $tienda->diseno->color_primario ?? '#16a34a';
    $imagen     = $traje->imagenes->first()?->ruta_img;
    $dias       = 1;
    $monto      = $traje->pre_alquiler;
    $sena       = round($monto * 0.40, 0);
@endphp

<style>
    :root {
        --brand: {{ $color }};
        --brand-light: {{ $color }}22;
        --brand-mid:   {{ $color }}55;
    }

    /* ── Fuentes editoriales ── */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap');

    .wiz-body       { font-family: 'DM Sans', sans-serif; }
    .wiz-display    { font-family: 'Playfair Display', serif; }

    /* ── Paso activo / completado ── */
    .step-dot { transition: background .35s, border-color .35s, transform .35s; }
    .step-dot.active  { background: var(--brand); border-color: var(--brand); transform: scale(1.15); }
    .step-dot.done    { background: var(--brand); border-color: var(--brand); }
    .step-line        { transition: background .5s; }
    .step-line.done   { background: var(--brand); }

    /* ── Paneles del wizard ── */
    .wiz-panel { display: none; animation: fadeUp .4s ease both; }
    .wiz-panel.active { display: block; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Input base ── */
    .wiz-input {
        width: 100%;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: .75rem 1rem;
        font-size: .8rem;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        background: #fff;
    }
    .wiz-input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-light); }

    /* ── Upload zone ── */
    .upload-zone {
        border: 2.5px dashed #d1d5db;
        border-radius: 18px;
        transition: border-color .25s, background .25s;
        cursor: pointer;
    }
    .upload-zone:hover, .upload-zone.has-file {
        border-color: var(--brand);
        background: var(--brand-light);
    }

    /* ── Botón brand ── */
    .btn-brand {
        background: var(--brand);
        color: #fff;
        font-weight: 800;
        font-size: .72rem;
        letter-spacing: .14em;
        text-transform: uppercase;
        border-radius: 14px;
        padding: 1rem 2rem;
        transition: opacity .2s, transform .15s;
        border: none;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-brand:hover   { opacity: .88; transform: translateY(-1px); }
    .btn-brand:active  { transform: translateY(0); }
    .btn-brand:disabled { opacity: .4; cursor: not-allowed; transform: none; }

    .btn-ghost {
        background: transparent;
        color: #6b7280;
        font-weight: 700;
        font-size: .72rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        border-radius: 14px;
        padding: 1rem 1.5rem;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: border-color .2s, color .2s;
    }
    .btn-ghost:hover { border-color: var(--brand); color: var(--brand); }

    /* ── Resumen lateral ── */
    .side-card {
        background: #fafafa;
        border: 2px solid #f0f0f0;
        border-radius: 28px;
        overflow: hidden;
        position: sticky;
        top: 6rem;
    }

    /* ── Precio dinámico ── */
    #precio-total, #precio-sena { transition: all .3s; }

    /* ── Tag de estado ── */
    .tag-estado {
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        padding: .3rem .8rem;
        border-radius: 999px;
    }
</style>

<div class="wiz-body max-w-6xl mx-auto px-4 py-12 min-h-screen">

    {{-- ═══ BREADCRUMB ═══ --}}
    <div class="flex items-center gap-2 mb-10 text-[11px] font-bold uppercase tracking-widest text-gray-400">
        <a href="{{ route('public.catalogo.index') }}" class="hover:text-gray-700 transition">Catálogo</a>
        <span class="text-gray-200">›</span>
        <span class="text-gray-600">Reservar traje</span>
    </div>

    {{-- ═══ TÍTULO ═══ --}}
    <div class="mb-10">
        <p class="text-[10px] font-black uppercase tracking-[.2em] text-gray-400 mb-1">Alquiler en línea</p>
        <h1 class="wiz-display text-3xl lg:text-4xl text-gray-900 leading-tight">
            {{ str_replace([' - Varón', ' - Dama'], '', $traje->nom_traje) }}
        </h1>
    </div>

    {{-- ═══ STEPPER ═══ --}}
    <div class="flex items-center gap-0 mb-12 max-w-sm">
        @foreach ([['1','Traje'], ['2','Tus datos'], ['3','Comprobante']] as $i => $step)
            <div class="flex flex-col items-center" style="min-width:60px">
                <div class="step-dot w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center
                            text-[11px] font-black text-gray-400 {{ $i === 0 ? 'active' : '' }}"
                     id="dot-{{ $step[0] }}">
                    <span id="dot-icon-{{ $step[0] }}">{{ $step[0] }}</span>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-widest mt-1.5 text-gray-400"
                      id="dot-label-{{ $step[0] }}">{{ $step[1] }}</span>
            </div>
            @if($i < 2)
                <div class="flex-1 h-[2px] bg-gray-100 mb-4 step-line" id="line-{{ $i+1 }}"></div>
            @endif
        @endforeach
    </div>

    {{-- ═══ LAYOUT PRINCIPAL ═══ --}}
    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- ════════════════════════════
             COLUMNA IZQUIERDA — WIZARD
             ════════════════════════════ --}}
        <div class="flex-1 min-w-0">
            <form method="POST"
                  action="{{ route('cliente.alquileres.store') }}"
                  enctype="multipart/form-data"
                  id="wiz-form">
                @csrf
                <input type="hidden" name="cod_unidad_alq" id="hidden-unidad" value="{{ $unidad->cod_unidad }}">

                {{-- ╔══════════════════════════╗
                     ║  PASO 1 — TRAJE Y FECHAS ║
                     ╚══════════════════════════╝ --}}
                <div class="wiz-panel active bg-white rounded-[2rem] border-2 border-gray-100 p-8 shadow-sm" id="panel-1">

                    <h2 class="wiz-display text-xl text-gray-800 mb-1">Confirma el traje y fechas</h2>
                    <p class="text-[11px] text-gray-400 font-medium mb-8 uppercase tracking-wider">Paso 1 de 3</p>

                    {{-- Unidad seleccionada --}}
                    <div class="mb-6">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                            Unidad seleccionada
                        </label>
                        <div class="bg-gray-50 rounded-2xl p-4 border-2 border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                                @if($imagen)
                                    <img src="{{ asset('storage/'.$imagen) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="font-black text-sm text-gray-800 uppercase tracking-tight">
                                    Talla {{ $unidad->talla }}
                                    <span class="font-medium text-gray-400 normal-case">— Serie {{ $unidad->nro_serie_interno }}</span>
                                </p>
                                <span class="tag-estado"
                                      style="background: var(--brand-light); color: var(--brand)">
                                    {{ $unidad->estado_fisico }}
                                </span>
                            </div>
                            <a href="{{ route('public.catalogo.index') }}"
                               class="ml-auto text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-700 transition underline">
                                Cambiar
                            </a>
                        </div>
                    </div>

                    {{-- Nombre del evento --}}
                    <div class="mb-5">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                            Nombre del evento *
                        </label>
                        <input type="text"
                               name="nom_evento"
                               id="nom_evento"
                               required
                               placeholder="Ej: Entrada Universitaria 2026, Preste Villa Copacabana..."
                               class="wiz-input"
                               value="{{ old('nom_evento') }}">
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                                Fecha de salida *
                            </label>
                            <input type="date"
                                   name="fec_salida"
                                   id="fec_salida"
                                   required
                                   min="{{ now()->toDateString() }}"
                                   max="{{ now()->addDays(7)->toDateString() }}"
                                   class="wiz-input"
                                   value="{{ old('fec_salida') }}">
                            <p class="text-[9px] text-gray-400 mt-1 font-medium">Máx. 7 días de anticipación</p>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                                Fecha de retorno *
                            </label>
                            <input type="date"
                                   name="fec_retorno_prev"
                                   id="fec_retorno"
                                   required
                                   class="wiz-input"
                                   value="{{ old('fec_retorno_prev') }}">
                        </div>
                    </div>

                    {{-- Errores --}}
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-5">
                            @foreach($errors->all() as $error)
                                <p class="text-red-600 text-xs font-semibold">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-end mt-8">
                        <button type="button" onclick="goTo(2)" class="btn-brand px-10">
                            Continuar →
                        </button>
                    </div>
                </div>

                {{-- ╔═══════════════════════╗
                     ║  PASO 2 — TUS DATOS   ║
                     ╚═══════════════════════╝ --}}
                <div class="wiz-panel bg-white rounded-[2rem] border-2 border-gray-100 p-8 shadow-sm" id="panel-2">

                    <h2 class="wiz-display text-xl text-gray-800 mb-1">Tus datos de contacto</h2>
                    <p class="text-[11px] text-gray-400 font-medium mb-8 uppercase tracking-wider">Paso 2 de 3</p>

                    {{-- Número WhatsApp --}}
                    <div class="mb-6">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                            Tu número WhatsApp *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 pointer-events-none">+591</span>
                            <input type="text"
                                   name="nro_celular_cliente"
                                   required
                                   maxlength="8"
                                   pattern="[0-9]{7,8}"
                                   placeholder="70012345"
                                   class="wiz-input pl-14"
                                   value="{{ old('nro_celular_cliente') }}">
                        </div>
                        <p class="text-[9px] text-gray-400 mt-1 font-medium">El vendedor te contactará por este número para coordinar la entrega</p>
                    </div>

                    {{-- Separador --}}
                    <div class="border-t-2 border-dashed border-gray-100 my-6 relative">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-white px-3 text-[9px] font-black uppercase tracking-widest text-gray-300">
                            Referencia / Garante
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-500 font-medium mb-5 leading-relaxed">
                        Para garantizar la seriedad del alquiler, necesitamos una persona de referencia que responda por el traje.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                                Nombre completo del garante *
                            </label>
                            <input type="text"
                                   name="nombre_garante"
                                   required
                                   placeholder="Nombre y apellidos"
                                   class="wiz-input"
                                   value="{{ old('nombre_garante') }}">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                                Carnet de Identidad del garante *
                            </label>
                            <input type="text"
                                   name="ci_garante"
                                   required
                                   maxlength="20"
                                   placeholder="Ej: 12345678 LP"
                                   class="wiz-input"
                                   value="{{ old('ci_garante') }}">
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="rounded-2xl p-4" style="background: var(--brand-light); border: 1.5px solid var(--brand-mid)">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--brand)">
                            🔒 Tus datos están protegidos y solo serán usados para este alquiler
                        </p>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="goTo(1)" class="btn-ghost">← Volver</button>
                        <button type="button" onclick="goTo(3)" class="btn-brand px-10">Continuar →</button>
                    </div>
                </div>

                {{-- ╔══════════════════════════╗
                     ║  PASO 3 — COMPROBANTE    ║
                     ╚══════════════════════════╝ --}}
                <div class="wiz-panel bg-white rounded-[2rem] border-2 border-gray-100 p-8 shadow-sm" id="panel-3">

                    <h2 class="wiz-display text-xl text-gray-800 mb-1">Sube tu comprobante de seña</h2>
                    <p class="text-[11px] text-gray-400 font-medium mb-8 uppercase tracking-wider">Paso 3 de 3 — último paso</p>

                    {{-- Monto a pagar --}}
                    <div class="rounded-2xl p-5 mb-6 border-2" style="background: var(--brand-light); border-color: var(--brand-mid)">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: var(--brand)">
                            Seña a depositar (40% del total)
                        </p>
                        <p class="wiz-display text-4xl font-black" style="color: var(--brand)" id="sena-display">
                            Bs. {{ $sena }}
                        </p>
                        <p class="text-[10px] font-medium text-gray-500 mt-1">
                            Total del alquiler: <span class="font-black text-gray-700" id="total-display">Bs. {{ $monto }}</span>
                            — resto se paga al recoger en tienda
                        </p>
                    </div>

                    {{-- Info tienda --}}
                    <div class="bg-gray-50 rounded-2xl p-4 mb-6 flex items-center gap-4 border border-gray-100">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background: var(--brand-light)">
                            <svg class="w-5 h-5" style="color: var(--brand)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-sm text-gray-800">{{ $tienda->nom_tie }}</p>
                            <p class="text-[10px] text-gray-500 font-medium">{{ $tienda->dir_tie }}</p>
                            @if($tienda->diseno?->link_whatsapp)
                                <a href="https://wa.me/591{{ $tienda->diseno->link_whatsapp }}"
                                   target="_blank"
                                   class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 hover:underline">
                                    Pedir QR de pago por WhatsApp →
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Upload zone --}}
                    <div class="mb-6">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                            Foto del comprobante *
                        </label>
                        <label for="comprobante_input" class="upload-zone block p-8 text-center" id="upload-label">
                            <div id="upload-placeholder">
                                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="font-black text-sm text-gray-600 mb-1">Toca para subir la foto</p>
                                <p class="text-[10px] text-gray-400 font-medium">JPG, PNG o WEBP — máx. 4MB</p>
                            </div>
                            <div id="upload-preview" class="hidden">
                                <img id="preview-img" src="" class="max-h-40 mx-auto rounded-xl object-contain mb-2">
                                <p class="text-[10px] font-bold text-gray-500" id="preview-name"></p>
                                <p class="text-[9px] font-black uppercase tracking-wider mt-1" style="color: var(--brand)">✓ Comprobante cargado — toca para cambiar</p>
                            </div>
                        </label>
                        <input type="file"
                               name="comprobante_pago"
                               id="comprobante_input"
                               required
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               class="sr-only"
                               onchange="previewFile(this)">
                    </div>

                    {{-- Resumen final antes de enviar --}}
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 mb-6 space-y-2" id="resumen-final">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-3">Resumen de tu solicitud</p>
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-500">Traje</span>
                            <span class="font-black text-gray-800">{{ str_replace([' - Varón',' - Dama'], '', $traje->nom_traje) }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-500">Talla / Serie</span>
                            <span class="font-black text-gray-800">{{ $unidad->talla }} — {{ $unidad->nro_serie_interno }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-500">Evento</span>
                            <span class="font-black text-gray-800" id="resumen-evento">—</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-500">Fechas</span>
                            <span class="font-black text-gray-800" id="resumen-fechas">—</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between text-xs">
                            <span class="font-black text-gray-700 uppercase tracking-wider">Seña a pagar ahora</span>
                            <span class="font-black text-base" style="color: var(--brand)" id="resumen-sena">Bs. {{ $sena }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="goTo(2)" class="btn-ghost">← Volver</button>
                        <button type="submit" class="btn-brand px-10 flex items-center gap-2" id="btn-submit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Enviar solicitud
                        </button>
                    </div>
                </div>

            </form>
        </div>

        {{-- ════════════════════════════
             COLUMNA DERECHA — RESUMEN
             ════════════════════════════ --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="side-card">

                {{-- Imagen del traje --}}
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    @if($imagen)
                        <img src="{{ asset('storage/'.$imagen) }}"
                             class="w-full h-full object-cover"
                             alt="{{ $traje->nom_traje }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-6 space-y-4">
                    {{-- Danza badge --}}
                    <span class="tag-estado" style="background: var(--brand-light); color: var(--brand)">
                        {{ $traje->danza->nom_danza ?? 'Danza folklórica' }}
                    </span>

                    <div>
                        <h3 class="wiz-display text-lg font-black text-gray-900 leading-tight">
                            {{ str_replace([' - Varón',' - Dama'], '', $traje->nom_traje) }}
                        </h3>
                        <p class="text-[11px] text-gray-400 font-medium mt-0.5">{{ $tienda->nom_tie }}</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Precio / día</span>
                            <span class="font-black text-gray-800">Bs. {{ number_format($traje->pre_alquiler, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Días</span>
                            <span class="font-black text-gray-800" id="side-dias">1</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                            <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider">Total</span>
                            <span class="font-black text-gray-900 text-lg" id="side-total">Bs. {{ number_format($traje->pre_alquiler, 0) }}</span>
                        </div>
                        <div class="rounded-xl p-3" style="background: var(--brand-light)">
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black uppercase tracking-wider" style="color: var(--brand)">Seña ahora (40%)</span>
                                <span class="font-black" style="color: var(--brand)" id="side-sena">Bs. {{ $sena }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Recojo en tienda --}}
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">📍 Recojo en tienda</p>
                        <p class="text-[11px] font-semibold text-gray-600">{{ $tienda->dir_tie }}</p>
                        @if($tienda->horario_tie)
                            <p class="text-[10px] text-gray-400 font-medium mt-1">{{ $tienda->horario_tie }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- fin layout --}}
</div>

<script>
    const precioBase = {{ $traje->pre_alquiler }};

    // ─── Navegación wizard ───
    function goTo(step) {
        if (step === 2 && !validarPaso1()) return;
        if (step === 3 && !validarPaso2()) return;

        document.querySelectorAll('.wiz-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + step).classList.add('active');

        // Stepper visual
        [1, 2, 3].forEach(i => {
            const dot   = document.getElementById('dot-' + i);
            const icon  = document.getElementById('dot-icon-' + i);
            dot.classList.remove('active', 'done');
            if (i < step)       { dot.classList.add('done');   icon.innerHTML = '✓'; }
            else if (i === step) { dot.classList.add('active'); icon.innerHTML = i; }
            else                 {                               icon.innerHTML = i; }
        });
        [1, 2].forEach(i => {
            const line = document.getElementById('line-' + i);
            line.classList.toggle('done', i < step);
        });

        if (step === 3) actualizarResumenFinal();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ─── Validaciones por paso ───
    function validarPaso1() {
        const evento  = document.getElementById('nom_evento').value.trim();
        const salida  = document.getElementById('fec_salida').value;
        const retorno = document.getElementById('fec_retorno').value;
        if (!evento)  { alert('Ingresa el nombre del evento.'); return false; }
        if (!salida)  { alert('Selecciona la fecha de salida.'); return false; }
        if (!retorno) { alert('Selecciona la fecha de retorno.'); return false; }
        if (retorno < salida) { alert('La fecha de retorno debe ser igual o posterior a la salida.'); return false; }
        return true;
    }

    function validarPaso2() {
        const cel     = document.querySelector('[name=nro_celular_cliente]').value.trim();
        const garante = document.querySelector('[name=nombre_garante]').value.trim();
        const ci      = document.querySelector('[name=ci_garante]').value.trim();
        if (!cel || cel.length < 7)   { alert('Ingresa tu número de celular (mín. 7 dígitos).'); return false; }
        if (!garante) { alert('Ingresa el nombre del garante.'); return false; }
        if (!ci)      { alert('Ingresa el CI del garante.'); return false; }
        return true;
    }

    // ─── Cálculo dinámico de días y precios ───
    function calcularPrecio() {
        const salida  = document.getElementById('fec_salida').value;
        const retorno = document.getElementById('fec_retorno').value;
        if (!salida || !retorno) return;

        const diff = Math.max(1, Math.round((new Date(retorno) - new Date(salida)) / 86400000) + 1);
        const total = diff * precioBase;
        const sena  = Math.round(total * 0.4);

        document.getElementById('side-dias').textContent  = diff;
        document.getElementById('side-total').textContent = 'Bs. ' + total.toLocaleString();
        document.getElementById('side-sena').textContent  = 'Bs. ' + sena;
        document.getElementById('sena-display').textContent = 'Bs. ' + sena;
        document.getElementById('total-display').textContent = 'Bs. ' + total.toLocaleString();
        document.getElementById('resumen-sena').textContent  = 'Bs. ' + sena;

        // Fecha retorno mínima = fecha salida
        document.getElementById('fec_retorno').min = salida;
    }

    document.getElementById('fec_salida').addEventListener('change', calcularPrecio);
    document.getElementById('fec_retorno').addEventListener('change', calcularPrecio);

    // ─── Preview del comprobante ───
    function previewFile(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-name').textContent = file.name;
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('upload-preview').classList.remove('hidden');
            document.getElementById('upload-label').classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    // ─── Resumen final paso 3 ───
    function actualizarResumenFinal() {
        const evento  = document.getElementById('nom_evento').value.trim();
        const salida  = document.getElementById('fec_salida').value;
        const retorno = document.getElementById('fec_retorno').value;

        document.getElementById('resumen-evento').textContent = evento || '—';
        if (salida && retorno) {
            const fmt = d => new Date(d + 'T12:00:00').toLocaleDateString('es-BO', { day:'2-digit', month:'short' });
            document.getElementById('resumen-fechas').textContent = fmt(salida) + ' → ' + fmt(retorno);
        }
    }
</script>

</x-public-layout>
<x-public-layout>
    @php 
        $diseno        = $tienda->diseno;
        $colorPrimario = $diseno->color_primario ?? '#22c55e'; 
        $colorFondo    = $diseno->color_fondo    ?? '#1f2937';
        $slogan        = $diseno->slogan         ?? 'Tradición y Folklore';
        $whatsapp      = $diseno->link_whatsapp;
        $facebook      = $diseno->link_facebook;
        $horario       = $diseno->horario_tie    ?? null;
    @endphp

    {{-- ══════════════════════════════════════
         HEADER / BANNER
         ====================================== --}}
    <section class="relative h-[300px] md:h-[400px] w-full overflow-hidden">
        <div class="absolute inset-0">
            @if($diseno->banner_path)
                <img src="{{ asset('storage/'.$diseno->banner_path) }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-andes-oscuro"></div>
            @endif
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        </div>
        <div class="relative h-full max-w-7xl mx-auto px-6 flex items-center gap-8">
            <div class="w-28 h-28 md:w-44 md:h-44 rounded-3xl border-4 bg-black overflow-hidden shadow-2xl flex-shrink-0" 
                 style="border-color: {{ $colorPrimario }}">
                @if($diseno->logo_path)
                    <img src="{{ asset('storage/'.$diseno->logo_path) }}" class="w-full h-full object-cover">
                @endif
            </div>
            <div class="text-white">
                <h1 class="text-4xl md:text-7xl font-black uppercase tracking-tighter drop-shadow-2xl leading-none mb-3">
                    {{ $tienda->nom_tie }}
                </h1>
                <p class="text-sm md:text-xl font-medium italic opacity-80 tracking-wide">"{{ $slogan }}"</p>

                {{-- HORARIO EN HEADER --}}
                @if($horario)
                    <div class="mt-4 flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-bold opacity-80">{{ $horario }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════
         ESTRUCTURA DOS COLUMNAS
         ====================================== --}}
    <div class="flex flex-col lg:flex-row w-full min-h-screen" style="background-color: {{ $colorFondo }};">
        
        {{-- ── COLUMNA PRINCIPAL ── --}}
        <main class="flex-grow p-6 md:p-12 lg:p-16">

            {{-- CONTEDOR MAESTRO DE ALPINE TOTALMENTE INTEGRADO CON SWITCH MULTI-GÉNERO --}}
            <div x-data="{ 
                    openModal: false, 
                    currentTraje: {}, 
                    activePhoto: 0,
                    generoSeleccionado: 'M', {{-- M = Masculino/Padre, F = Femenino/Variante --}}

                    showTraje(traje) {
                        this.currentTraje = traje;
                        this.generoSeleccionado = 'M';
                        this.activePhoto  = 0;
                        this.openModal    = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    closeModal() {
                        this.openModal = false;
                        document.body.classList.remove('overflow-hidden');
                    },

                    {{-- Helpers dinámicos para el switch reactivo --}}
                    getNombre() {
                        if (this.generoSeleccionado === 'F' && this.currentTraje.variante_femenina) {
                            return this.currentTraje.variante_femenina.nom_traje;
                        }
                        return this.currentTraje.nom_traje;
                    },
                    getDescripcion() {
                        if (this.generoSeleccionado === 'F' && this.currentTraje.variante_femenina) {
                            return this.currentTraje.variante_femenina.des_traje;
                        }
                        return this.currentTraje.des_traje;
                    },
                    getImagenes() {
                        if (this.generoSeleccionado === 'F' && this.currentTraje.variante_femenina) {
                            return this.currentTraje.variante_femenina.imagenes || [];
                        }
                        return this.currentTraje.imagenes || [];
                    },
                    getUnidades() {
                        if (this.generoSeleccionado === 'F' && this.currentTraje.variante_femenina) {
                            return this.currentTraje.variante_femenina.unidades || [];
                        }
                        return this.currentTraje.unidades || [];
                    },
                    getUnidadesDisponibles() {
                        return this.getUnidades().filter((unidad) => unidad.disponibilidad);
                    }
                 }"
                 style="background-color: {{ $colorFondo }};">

                {{-- ══════════════════════════════════════
                     CABECERA + BUSCADOR TESAURO
                     ====================================== --}}
                <div class="w-full lg:max-w-md relative mb-12"
                     x-data="{
                         q: '{{ request('q') }}',
                         sugerencias: [],
                         mostrar: false,
                         buscar() {
                             if (this.q.length < 2) { this.sugerencias = []; this.mostrar = false; return; }
                             fetch('/api/tesauro/autocomplete?q=' + encodeURIComponent(this.q))
                                 .then(r => r.json())
                                 .then(data => {
                                     this.sugerencias = data;
                                     this.mostrar = data.length > 0;
                                 });
                         },
                         elegir(termino) {
                             this.q = termino;
                             this.mostrar = false;
                             this.$refs.form.submit();
                         }
                     }">

                    <form x-ref="form" action="" method="GET" class="relative group">
                        <input
                            type="text"
                            name="q"
                            x-model="q"
                            @input.debounce.300ms="buscar()"
                            @focus="if(sugerencias.length) mostrar = true"
                            @click.outside="mostrar = false"
                            placeholder="Busca por danza, accesorio o sinónimo..."
                            class="w-full bg-white/10 border-2 border-white/10 rounded-2xl py-4 px-7 text-white
                                   placeholder:text-white/30 font-bold text-sm uppercase tracking-wider
                                   focus:outline-none focus:ring-2 focus:border-transparent transition-all backdrop-blur-sm"
                        />
                        <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-3 rounded-xl shadow-lg transition-all hover:scale-110 text-white"
                                style="background-color: {{ $colorPrimario }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    {{-- DROPDOWN SUGERENCIAS --}}
                    <div x-show="mostrar" x-cloak
                         class="absolute top-full left-0 right-0 mt-2 bg-white/10 backdrop-blur-xl
                                border border-white/10 rounded-2xl overflow-hidden shadow-2xl z-50">
                        <template x-for="s in sugerencias" :key="s.cod_termino">
                            <button @click="elegir(s.termino_usuario)"
                                    class="w-full px-6 py-3 text-left flex items-center justify-between
                                           hover:bg-white/10 transition border-b border-white/5 last:border-0">
                                <span class="font-bold text-sm uppercase text-white" x-text="s.termino_usuario"></span>
                                <span class="text-[9px] font-black uppercase px-2 py-1 rounded-full"
                                      :class="{
                                          'bg-blue-500/30 text-blue-300':     s.tipo === 'sinonimo',
                                          'bg-yellow-500/30 text-yellow-300': s.tipo === 'ortografia',
                                          'bg-purple-500/30 text-purple-300': s.tipo === 'referencia'
                                      }"
                                      x-text="s.tipo">
                                </span>
                            </button>
                        </template>
                    </div>

                    {{-- Subcategorías --}}
                    @if(isset($terminosRelacionados) && $terminosRelacionados->count())
                        <div class="mt-3 px-2">
                            <p class="text-white/30 text-[9px] font-black uppercase tracking-widest mb-2">
                                Filtrar por subcategoría:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($terminosRelacionados as $termino)
                                    <a href="?q={{ $termino }}"
                                       class="px-3 py-1 rounded-full text-[10px] font-black uppercase transition
                                              border border-white/20 text-white/60 hover:text-white hover:border-white/50">
                                        {{ $termino }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Limpiar búsqueda --}}
                    @if(request('q'))
                        <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest mt-2 px-2">
                            Resultados para: <span class="text-white/80">{{ request('q') }}</span>
                            — <a href="{{ url()->current() }}" class="underline hover:text-white transition">Limpiar</a>
                        </p>
                    @endif

                    {{-- Búsquedas populares --}}
                    @if(!request('q') && isset($populares) && $populares->count())
                        <div class="mt-4">
                            <p class="text-white/30 text-[9px] font-black uppercase tracking-widest mb-2 px-1">
                                Búsquedas frecuentes
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($populares as $popular)
                                    <a href="?q={{ $popular }}"
                                       class="px-3 py-1 bg-white/5 border border-white/10 rounded-full
                                              text-[10px] font-black uppercase text-white/50 hover:bg-white/10
                                              hover:text-white transition">
                                        {{ $popular }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ══════════════════════════════════════
                     GRID DE TRAJES (Filtro unificado)
                     ====================================== --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($trajes as $traje)
                        {{-- Muro anti-duplicados por si viajan hijos sueltos en el array --}}
                        @if($traje->cod_traje_padre !== null) @continue @endif

                        @php
                            $trajeJson              = $traje->toArray();
                            $trajeJson['imagenes']  = $traje->imagenes;
                            $trajeJson['danza']     = $traje->danza;
                            $trajeJson['unidades']  = $traje->unidades;
                            
                            // Inyectamos el bloque de damas acoplado
                            if($traje->varianteFemenina) {
                                $trajeJson['variante_femenina'] = $traje->varianteFemenina->toArray();
                                $trajeJson['variante_femenina']['imagenes'] = $traje->varianteFemenina->imagenes;
                                $trajeJson['variante_femenina']['unidades'] = $traje->varianteFemenina->unidades;
                            } else {
                                $trajeJson['variante_femenina'] = null;
                            }
                        @endphp

                        <div @click="showTraje({{ json_encode($trajeJson) }})" 
                             class="group bg-white/10 backdrop-blur-xl rounded-[2.5rem] p-5 border border-white/10
                                    cursor-pointer transition hover:-translate-y-2 hover:shadow-2xl relative">
                            
                            {{-- Badge Pareja Flotante --}}
                            @if($traje->varianteFemenina)
                                <div class="absolute top-8 right-8 z-20">
                                    <span class="text-[8px] font-black uppercase text-white px-2.5 py-1.5 rounded-full shadow-lg border border-white/10 backdrop-blur-md bg-black/40 tracking-wider">
                                        👫 Ambos Bloques
                                    </span>
                                </div>
                            @endif

                            <div class="h-72 bg-gray-200/20 rounded-[2rem] overflow-hidden mb-5">
                                @if($traje->imagenes->first())
                                    <img src="{{ asset('storage/'.$traje->imagenes->first()->ruta_img) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white/20 font-bold uppercase text-xs">
                                        Sin imagen
                                    </div>
                                @endif
                            </div>

                            <div class="px-2 mb-6">
                                <h3 class="text-white font-black uppercase text-sm mb-1">
                                    {{ str_replace(' - Varón', '', $traje->nom_traje) }}
                                </h3>
                                <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">
                                    Colección
                                    @if($traje->danza)
                                        · {{ $traje->danza->nom_danza }}
                                    @endif
                                </p>
                            </div>

                            <div class="h-12 w-full rounded-2xl flex items-center justify-center text-white font-black uppercase text-xs shadow-lg transition group-hover:brightness-110" 
                                 style="background-color: {{ $colorPrimario }}">
                                Bs. {{ number_format($traje->pre_alquiler, 0) }}
                            </div>
                        </div>

                    @empty
                        <div class="col-span-3 text-center py-24 text-white/30">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <p class="font-black uppercase tracking-widest text-sm">
                                @if(request('q'))
                                    Sin resultados para "{{ request('q') }}"
                                @else
                                    Esta tienda aún no tiene trajes publicados
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- ══════════════════════════════════════
                     MODAL DETALLE TRAJE (MULTI-GÉNERO)
                     ====================================== --}}
                <div x-show="openModal"
                    x-transition.opacity
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
                    style="display: none;" x-cloak>
                    
                    <div class="bg-white w-full max-w-5xl max-h-[95vh] rounded-[3rem] overflow-hidden shadow-2xl relative flex flex-col lg:flex-row"
                        @click.away="closeModal()">
                        
                        <button @click="closeModal()"
                                class="absolute top-6 right-6 z-50 text-white bg-black/20 hover:bg-black/40 p-2 rounded-full backdrop-blur-md transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- CARRUSEL REACTIVO SELEGÚN EL GÉNERO --}}
                        <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col h-[400px] lg:h-auto">
                            <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-gray-200">
                                <template x-if="getImagenes().length > 0">
                                    <img :src="'/storage/' + getImagenes()[activePhoto].ruta_img"
                                        class="w-full h-full object-cover">
                                </template>
                                
                                <template x-if="getImagenes().length > 1">
                                    <div class="absolute inset-0 flex items-center justify-between px-4">
                                        <button @click="activePhoto = (activePhoto > 0) ? activePhoto - 1 : getImagenes().length - 1"
                                                class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="3" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <button @click="activePhoto = (activePhoto < getImagenes().length - 1) ? activePhoto + 1 : 0"
                                                class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="3" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            
                            {{-- Miniaturas Reactivas --}}
                            <div class="p-4 bg-white border-t flex gap-2 overflow-x-auto justify-center hide-scrollbar">
                                <template x-for="(img, index) in getImagenes()" :key="index">
                                    <button @click="activePhoto = index"
                                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                            :class="activePhoto === index ? 'shadow-lg scale-110' : 'opacity-40'"
                                            :style="activePhoto === index ? 'border-color: {{ $colorPrimario }}' : 'border-color: transparent'">
                                        <img :src="'/storage/' + img.ruta_img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- DETALLES INYECTADOS SEGÚN FILTRO --}}
                        <div class="w-full lg:w-1/2 p-10 flex flex-col overflow-y-auto">
                            
                            {{-- Switch de Género Interno (Solo visible si tiene variante de dama) --}}
                            <template x-if="currentTraje.variante_femenina">
                                <div class="mb-6 p-1 bg-gray-100 rounded-2xl flex border max-w-xs shadow-inner">
                                    <button type="button" @click="generoSeleccionado = 'M'; activePhoto = 0"
                                            :class="generoSeleccionado === 'M' ? 'bg-white text-gray-900 shadow-md font-black' : 'text-gray-500 font-bold'"
                                            class="flex-1 py-2 text-xs uppercase rounded-xl transition-all">
                                        🤵 Varón
                                    </button>
                                    <button type="button" @click="generoSeleccionado = 'F'; activePhoto = 0"
                                            :class="generoSeleccionado === 'F' ? 'bg-white text-gray-900 shadow-md font-black' : 'text-gray-500 font-bold'"
                                            class="flex-1 py-2 text-xs uppercase rounded-xl transition-all">
                                        💃 Damas
                                    </button>
                                </div>
                            </template>

                            <h2 class="text-3xl font-black uppercase leading-tight mb-2"
                                style="color: {{ $colorPrimario }}"
                                x-text="getNombre()"></h2>
                            
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">
                                Categoría: <span class="text-gray-800" x-text="currentTraje.danza?.nom_danza || 'General'"></span>
                            </p>
                            
                            <div class="py-6 border-y border-gray-100 mb-8 space-y-5">
                                {{-- Precio --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Precio Alquiler / día</p>
                                    <p class="text-3xl font-black" style="color: {{ $colorPrimario }}">
                                        Bs. <span x-text="generoSeleccionado === 'F' && currentTraje.variante_femenina ? currentTraje.variante_femenina.pre_alquiler : currentTraje.pre_alquiler"></span>
                                    </p>
                                </div>

                                {{-- Tallas clicables → inician el alquiler en línea --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                        Elige tu talla para alquilar
                                    </p>

                                    {{-- Con unidades disponibles --}}
                                    <template x-if="getUnidadesDisponibles().length > 0">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="unidad in getUnidadesDisponibles()" :key="unidad.cod_unidad">
                                                <a :href="'/reservar/' + unidad.cod_unidad"
                                                   class="group px-4 py-2.5 rounded-xl border-2 font-black text-xs uppercase
                                                          transition-all duration-200 hover:shadow-md hover:scale-105
                                                          flex flex-col items-center gap-0.5 cursor-pointer"
                                                   style="border-color: {{ $colorPrimario }}40; color: {{ $colorPrimario }}; background-color: {{ $colorPrimario }}08"
                                                   x-on:mouseover="$el.style.backgroundColor = '{{ $colorPrimario }}22'"
                                                   x-on:mouseleave="$el.style.backgroundColor = '{{ $colorPrimario }}08'">
                                                    <span x-text="unidad.talla" class="text-sm leading-none"></span>
                                                    <span class="text-[8px] font-semibold opacity-50 normal-case tracking-normal"
                                                          x-text="unidad.estado_fisico"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Sin unidades disponibles --}}
                                    <template x-if="getUnidadesDisponibles().length === 0">
                                        <div class="flex items-center gap-2 py-3 px-4 bg-red-50 rounded-xl border border-red-100">
                                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-bold text-red-500">Sin tallas disponibles por el momento</span>
                                        </div>
                                    </template>

                                    {{-- Contador de disponibilidad --}}
                                    <template x-if="getUnidades().length > 0">
                                        <p class="text-[9px] text-gray-400 font-medium mt-2">
                                            <span x-text="getUnidadesDisponibles().length"></span> de
                                            <span x-text="getUnidades().length"></span> unidades disponibles
                                        </p>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-grow">
                                <h4 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3">Descripción del Traje</h4>
                                <p class="text-gray-500 text-sm leading-relaxed italic"
                                   x-text="getDescripcion() || 'Sin descripción disponible.'"></p>
                            </div>

                            <div class="mt-10">
                                {{-- Con stock → WhatsApp como consulta secundaria (la acción principal es elegir talla) --}}
                                <template x-if="getUnidadesDisponibles().length > 0">
                                    <a :href="'https://wa.me/591{{ $diseno->link_whatsapp }}?text=Hola! Vengo de tu tienda virtual. Me interesa el bloque de ' + (generoSeleccionado === 'F' ? 'Damas' : 'Varón') + ' para el traje: ' + getNombre()"
                                       target="_blank"
                                       class="w-full py-3.5 rounded-2xl bg-white text-gray-500 font-bold uppercase
                                              text-[10px] tracking-[0.15em] border-2 border-gray-200
                                              flex items-center justify-center gap-2
                                              transition hover:border-gray-400 hover:text-gray-700">
                                        <x-si-whatsapp class="w-4 h-4 fill-current text-emerald-500" />
                                        ¿Dudas? Consulta por WhatsApp
                                    </a>
                                </template>

                                {{-- Sin stock → WhatsApp prominente para consultar disponibilidad --}}
                                <template x-if="getUnidadesDisponibles().length === 0">
                                    <a :href="'https://wa.me/591{{ $diseno->link_whatsapp }}?text=Hola! Vengo de tu tienda virtual. Me interesa el bloque de ' + (generoSeleccionado === 'F' ? 'Damas' : 'Varón') + ' para el traje: ' + getNombre() + '. ¿Cuándo habrá disponibilidad?'"
                                       target="_blank"
                                       class="w-full py-4 rounded-2xl text-white font-black uppercase text-xs tracking-[0.2em] shadow-xl flex items-center justify-center gap-3 transition transform hover:scale-105"
                                       style="background-color: {{ $colorPrimario }}">
                                        <x-si-whatsapp class="w-5 h-5 fill-current" />
                                        Consultar Disponibilidad
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ── ASIDE CONTACTO (Mantiene tu lógica estructural original intacta) ── --}}
        <aside class="w-full lg:w-40 lg:sticky lg:top-24 h-auto lg:h-[calc(100vh-6rem)] flex flex-col items-center py-12 gap-10 border-t lg:border-t-0 lg:border-l border-white/10"
               style="background-color: rgba(0,0,0,0.2);">
            
            <div class="hidden lg:block [writing-mode:vertical-lr] text-[10px] font-black text-white/30 uppercase tracking-[0.6em] mb-4">
                Contactar Tienda
            </div>

            @if($whatsapp)
               <a href="https://wa.me/591{{ $whatsapp }}" target="_blank"
                  class="w-16 h-16 bg-[#25D366] rounded-full flex items-center justify-center shadow-[0_15px_35px_rgba(37,211,102,0.4)] hover:scale-110 transition-all border-4 border-white group shrink-0 aspect-square">
                    <x-si-whatsapp class="w-8 h-8 text-white fill-current" />
                </a>
            @endif

            @if($facebook)
               <a href="{{ $facebook }}" target="_blank"
                  class="w-16 h-16 bg-[#1877F2] rounded-full flex items-center justify-center shadow-[0_15px_35px_rgba(24,119,242,0.4)] hover:scale-110 transition-all border-4 border-white group shrink-0 aspect-square">
                    <x-si-facebook class="w-10 h-10 text-white fill-current group-hover:-rotate-12 transition-transform" />
                </a>
            @endif

            {{-- HORARIO EN ASIDE --}}
            @if($horario)
                <div class="flex flex-col items-center gap-2 text-center px-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $colorPrimario }}20; color: {{ $colorPrimario }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[9px] font-black text-white/50 uppercase tracking-widest leading-relaxed [writing-mode:vertical-lr] lg:[writing-mode:horizontal-tb]">
                        {{ $horario }}
                    </p>
                </div>
            @endif

            <div class="mt-auto w-1.5 h-24 rounded-full" style="background-color: {{ $colorPrimario }}"></div>
        </aside>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</x-public-layout>
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
    ══════════════════════════════════════ --}}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-bold opacity-80">{{ $horario }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════
         ESTRUCTURA DOS COLUMNAS
    ══════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row w-full min-h-screen" style="background-color: {{ $colorFondo }};">
        
        {{-- ── COLUMNA PRINCIPAL ── --}}
        <main class="flex-grow p-6 md:p-12 lg:p-16">

            <div x-data="{ 
                    openModal: false, 
                    currentTraje: {}, 
                    activePhoto: 0,
                    showTraje(traje) {
                        this.currentTraje = traje;
                        this.activePhoto  = 0;
                        this.openModal    = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    closeModal() {
                        this.openModal = false;
                        document.body.classList.remove('overflow-hidden');
                    }
                 }"
                 style="background-color: {{ $colorFondo }};">

                {{-- ══════════════════════════════════════
                     CABECERA + BUSCADOR TESAURO
                ══════════════════════════════════════ --}}
               {{-- BUSCADOR CON AUTOCOMPLETADO --}}
<div class="w-full lg:max-w-md relative"
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
                <span class="font-bold text-sm uppercase text-white"
                      x-text="s.termino_usuario"></span>
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

    {{-- Subcategorías / Términos relacionados --}}
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
                     GRID DE TRAJES
                ══════════════════════════════════════ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($trajes as $traje)
                        @php
                            $trajeJson              = $traje->toArray();
                            $trajeJson['imagenes']  = $traje->imagenes;
                            $trajeJson['danza']     = $traje->danza;
                        @endphp

                        <div @click="showTraje({{ json_encode($trajeJson) }})" 
                             class="group bg-white/10 backdrop-blur-xl rounded-[2.5rem] p-5 border border-white/10
                                    cursor-pointer transition hover:-translate-y-2 hover:shadow-2xl">
                            
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
                                <h3 class="text-white font-black uppercase text-sm mb-1">{{ $traje->nom_traje }}</h3>
                                <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">
                                    Talla {{ $traje->talla_traje }}
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                     MODAL DETALLE TRAJE
                ══════════════════════════════════════ --}}
                <div x-show="openModal"
                    x-transition.opacity
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
                    style="display: none;">
                    
                    <div class="bg-white w-full max-w-5xl max-h-[95vh] rounded-[3rem] overflow-hidden shadow-2xl relative flex flex-col lg:flex-row"
                        @click.away="closeModal()">
                        
                        <button @click="closeModal()"
                                class="absolute top-6 right-6 z-50 text-white bg-black/20 hover:bg-black/40 p-2 rounded-full backdrop-blur-md transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- CARRUSEL --}}
                        <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col h-[400px] lg:h-auto">
                            <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-gray-200">
                                <template x-if="currentTraje.imagenes && currentTraje.imagenes.length > 0">
                                    <img :src="'/storage/' + currentTraje.imagenes[activePhoto].ruta_img"
                                        class="w-full h-full object-cover">
                                </template>
                                <template x-if="currentTraje.imagenes?.length > 1">
                                    <div class="absolute inset-0 flex items-center justify-between px-4">
                                        <button @click="activePhoto = (activePhoto > 0) ? activePhoto - 1 : currentTraje.imagenes.length - 1"
                                                class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="3" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <button @click="activePhoto = (activePhoto < currentTraje.imagenes.length - 1) ? activePhoto + 1 : 0"
                                                class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="3" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <div class="p-4 bg-white border-t flex gap-2 overflow-x-auto justify-center">
                                <template x-for="(img, index) in currentTraje.imagenes" :key="index">
                                    <button @click="activePhoto = index"
                                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                            :class="activePhoto === index ? 'shadow-lg scale-110' : 'opacity-40'"
                                            :style="activePhoto === index ? 'border-color: {{ $colorPrimario }}' : 'border-color: transparent'">
                                        <img :src="'/storage/' + img.ruta_img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- DETALLES --}}
                        <div class="w-full lg:w-1/2 p-10 flex flex-col overflow-y-auto">
                            <h2 class="text-3xl font-black uppercase leading-tight mb-2"
                                style="color: {{ $colorPrimario }}"
                                x-text="currentTraje.nom_traje"></h2>
                            
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">
                                Categoría: <span class="text-gray-800" x-text="currentTraje.danza?.nom_danza || 'General'"></span>
                            </p>
                            
                            <div class="grid grid-cols-2 gap-6 py-6 border-y border-gray-100 mb-8">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Precio Alquiler</p>
                                    <p class="text-3xl font-black" style="color: {{ $colorPrimario }}">
                                        Bs. <span x-text="currentTraje.pre_alquiler"></span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tallas Disponibles</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-if="currentTraje.unidades && currentTraje.unidades.length > 0">
                                            <template x-for="unidad in currentTraje.unidades" :key="unidad.cod_unidad">
                                                <span class="text-xs font-black px-2.5 py-1 rounded-lg uppercase border shadow-sm"
                                                    style="color: {{ $colorPrimario }}; border-color: {{ $colorPrimario }}20; background-color: {{ $colorPrimario }}08"
                                                    x-text="unidad.talla"></span>
                                            </template>
                                        </template>
                                        <template x-if="!currentTraje.unidades || currentTraje.unidades.length === 0">
                                            <span class="text-xs font-bold text-gray-400 italic">Agotado</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-grow">
                                <h4 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3">Descripción del Traje</h4>
                                <p class="text-gray-500 text-sm leading-relaxed italic"
                                x-text="currentTraje.des_traje || 'Sin descripción disponible.'"></p>
                            </div>

                            <div class="mt-10">
                                <a :href="'https://wa.me/591{{ $diseno->link_whatsapp }}?text=Hola! Me interesa alquilar el traje: ' + currentTraje.nom_traje"
                                target="_blank"
                                class="w-full py-4 rounded-2xl text-white font-black uppercase text-xs tracking-[0.2em] shadow-xl flex items-center justify-center gap-3 transition transform hover:scale-105"
                                style="background-color: {{ $colorPrimario }}">
                                    
                                    {{-- Componente de codeat3/blade-simple-icons --}}
                                    <x-si-whatsapp class="w-5 h-5 fill-current" />
                                    
                                    Consultar Disponibilidad
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ── ASIDE CONTACTO ── --}}
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

            {{-- HORARIO EN ASIDE (versión compacta) --}}
            @if($horario)
                <div class="flex flex-col items-center gap-2 text-center px-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $colorPrimario }}20; color: {{ $colorPrimario }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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

</x-public-layout>
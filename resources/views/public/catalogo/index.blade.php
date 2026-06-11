<x-public-layout>
    {{-- CONTENEDOR ALPINE PARA EL CATÁLOGO Y EL MODAL INTEGRADO --}}
    <div x-data="{ 
            openModal: false, 
            currentTraje: {}, 
            activePhoto: 0,
            selectedUnit: '',
            generoSeleccionado: 'M', {{-- M = Masculino (Padre), F = Femenino (Variante) --}}

            showTraje(trajeMaster) {
                this.currentTraje = trajeMaster;
                this.generoSeleccionado = 'M'; {{-- Por defecto abre siempre el Varón/Maestro --}}
                this.activePhoto = 0;
                this.selectedUnit = '';
                this.openModal = true;
                document.body.classList.add('overflow-hidden');
            },
            closeModal() {
                this.openModal = false;
                document.body.classList.remove('overflow-hidden');
            },
            
            {{-- Helpers dinámicos para alternar datos, imágenes y stock según el género activo --}}
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
         class="max-w-7xl mx-auto py-16 px-4 min-h-screen">
        
        {{-- ==========================================
             CABECERA E INTELIGENCIA DE BÚSQUEDA
             ========================================== --}}
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

            <form x-ref="form" action="{{ route('public.catalogo.index') }}" method="GET" class="relative group">

                {{-- Filtro por puesta (1ra/2da/3ra) --}}
                <div class="mt-3 flex flex-wrap gap-2">
                    @php
                        $puestaActual = request('puesta', 'all');
                    @endphp
                    <button type="button" onclick="document.getElementById('input-puesta').value='all'; this.closest('form').submit();"
                            class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border-2 transition-all {{ $puestaActual==='all' ? 'bg-andes-verde text-white border-andes-verde' : 'bg-white border-gray-200 text-gray-700 hover:border-andes-verde' }}">
                        Todas
                    </button>
                    <button type="button" onclick="document.getElementById('input-puesta').value='1'; this.closest('form').submit();"
                            class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border-2 transition-all {{ $puestaActual==='1' ? 'bg-andes-verde text-white border-andes-verde' : 'bg-white border-gray-200 text-gray-700 hover:border-andes-verde' }}">
                        1ra
                    </button>
                    <button type="button" onclick="document.getElementById('input-puesta').value='2'; this.closest('form').submit();"
                            class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border-2 transition-all {{ $puestaActual==='2' ? 'bg-andes-verde text-white border-andes-verde' : 'bg-white border-gray-200 text-gray-700 hover:border-andes-verde' }}">
                        2da
                    </button>
                    <button type="button" onclick="document.getElementById('input-puesta').value='3'; this.closest('form').submit();"
                            class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border-2 transition-all {{ $puestaActual==='3' ? 'bg-andes-verde text-white border-andes-verde' : 'bg-white border-gray-200 text-gray-700 hover:border-andes-verde' }}">
                        3ra
                    </button>
                    <input type="hidden" name="puesta" id="input-puesta" value="{{ $puestaActual }}">
                </div>

            

                <input
                    type="text"
                    name="q"
                    x-model="q"
                    @input.debounce.300ms="buscar()"
                    @focus="if(sugerencias.length) mostrar = true"
                    @click.outside="mostrar = false"
                    placeholder="Busca por danza, accesorio o sinónimo..."
                    class="w-full bg-white border-2 border-gray-100 rounded-2xl py-5 px-8 shadow-sm
                           group-hover:shadow-xl focus:ring-2 focus:ring-andes-verde transition-all
                           font-bold text-sm uppercase tracking-wider">
                <button type="submit"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-andes-oscuro text-white
                               p-3 rounded-xl hover:bg-andes-verde transition-colors shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>

            {{-- DROPDOWN SUGERENCIAS TESAURO --}}
            <div x-show="mostrar"
                 x-cloak
                 class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100
                        rounded-2xl overflow-hidden shadow-2xl z-50">
                <template x-for="s in sugerencias" :key="s.cod_termino">
                    <button @click="elegir(s.termino_usuario)"
                            class="w-full px-6 py-3 text-left flex items-center justify-between
                                   hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                        <span class="font-bold text-sm uppercase text-gray-700" x-text="s.termino_usuario"></span>
                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-full"
                              :class="{
                                  'bg-blue-100 text-blue-600':     s.tipo === 'sinonimo',
                                  'bg-yellow-100 text-yellow-600': s.tipo === 'ortografia',
                                  'bg-purple-100 text-purple-600': s.tipo === 'referencia'
                              }"
                              x-text="s.tipo">
                        </span>
                    </button>
                </template>
            </div>

            @if(isset($terminosRelacionados) && $terminosRelacionados->count())
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-3 px-2">
                    También relacionado: <span class="text-gray-600">{{ $terminosRelacionados->implode(', ') }}</span>
                </p>
            @endif

            @if(request('q'))
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2 px-2">
                    Resultados para: <span class="text-gray-700">{{ request('q') }}</span>
                    — <a href="{{ route('public.catalogo.index') }}" class="underline hover:text-andes-verde transition">Limpiar</a>
                </p>
            @endif
        </div>

        {{-- ==========================================
             GRID DE TRAJES (Catálogo Limpio)
             ========================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($trajes as $traje)
                @php 
                    if($traje->cod_traje_padre !== null) continue;

                    $colorTienda = $traje->tienda->diseno->color_primario ?? '#16a34a';
                    
                    $trajeJson = $traje->toArray();
                    $trajeJson['imagenes'] = $traje->imagenes;
                    $trajeJson['tienda_nombre'] = $traje->tienda->nom_tie;
                    $trajeJson['color_tienda'] = $colorTienda;
                    $trajeJson['danza_nombre'] = $traje->danza->nom_danza ?? 'General';
                    $trajeJson['whatsapp'] = $traje->tienda->diseno->link_whatsapp ?? '';
                    $trajeJson['unidades'] = $traje->unidades;
                    
                    if($traje->varianteFemenina) {
                        $trajeJson['variante_femenina'] = $traje->varianteFemenina->toArray();
                        $trajeJson['variante_femenina']['imagenes'] = $traje->varianteFemenina->imagenes;
                        $trajeJson['variante_femenina']['unidades'] = $traje->varianteFemenina->unidades;
                    } else {
                        $trajeJson['variante_femenina'] = null;
                    }
                @endphp
                
                {{-- CARTA MAESTRA DEL TRAJE --}}
                <div @click="showTraje({{ json_encode($trajeJson) }})" 
                     class="group relative bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col h-full cursor-pointer">
                    
                    <div class="aspect-[3/4] bg-gray-100 overflow-hidden relative">
                        @if($traje->imagenes->first())
                            <img src="{{ asset('storage/' . $traje->imagenes->first()->ruta_img) }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                 alt="{{ $traje->nom_traje }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        
                        <div class="absolute top-6 left-6">
                            <span class="bg-white/90 backdrop-blur-md text-gray-900 text-[9px] font-black uppercase px-4 py-2 rounded-full shadow-lg tracking-widest border border-gray-100">
                                {{ $traje->danza->nom_danza ?? 'General' }}
                            </span>
                        </div>

                        @if($traje->varianteFemenina)
                            <div class="absolute top-6 right-6">
                                <span class="bg-gradient-to-r from-blue-500 to-pink-500 text-white text-[8px] font-black uppercase px-3 py-1.5 rounded-full shadow-md tracking-wider">
                                    👫 Colección Pareja
                                </span>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="bg-white text-gray-900 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl scale-90 group-hover:scale-100 transition-transform">
                                Ver Catálogo
                            </span>
                        </div>
                    </div>

                    <div class="p-8 flex-grow">
                        <h3 class="text-xl font-black text-gray-800 uppercase leading-tight mb-2 tracking-tighter">
                            {{ str_replace(' - Varón', '', $traje->nom_traje) }}
                        </h3>
                        
                        <div class="flex items-center gap-2 mt-4">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $colorTienda }}"></div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tienda: <span class="text-gray-800">{{ $traje->tienda->nom_tie }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="px-8 pb-8 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Precio Base</span>
                            <span class="text-sm font-black uppercase" style="color: {{ $colorTienda }}">Bs. {{ number_format($traje->precio_efectivo ?? $traje->pre_alquiler, 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl group-hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L4 9z"></path></svg>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-4 border-dashed border-gray-100">
                    <div class="mb-6 opacity-20 flex justify-center">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-400 uppercase tracking-tighter">No encontramos resultados</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">Intenta buscar por otra danza o categoría folclórica.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-16 flex justify-center">
            {{ $trajes->links() }}
        </div>

        {{-- ==========================================
             MODAL DE DETALLES MULTI-GÉNERO INTERACTIVO
             ========================================== --}}
        <div x-show="openModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
             x-transition.opacity
             x-cloak
             style="display: none;">
            
            <div class="bg-white w-full max-w-5xl h-full max-h-[85vh] lg:max-h-[75vh] rounded-[3rem] overflow-hidden shadow-2xl relative flex flex-col lg:flex-row" 
                 @click.away="closeModal()">
                
                {{-- Botón Cerrar --}}
                <button @click="closeModal()" 
                        class="absolute top-6 right-6 z-50 text-white bg-black/20 hover:bg-black/50 p-2 rounded-full backdrop-blur-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- ══════════════════════════════════
                     IZQUIERDA — CARRUSEL DE IMÁGENES
                     ══════════════════════════════════ --}}
                <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col h-[40vh] lg:h-full overflow-hidden">
                    
                    <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-gray-50 p-4 h-full">
                        
                        {{-- Foto activa --}}
                        <template x-if="getImagenes().length > 0">
                            <img :src="'/storage/' + getImagenes()[activePhoto].ruta_img" 
                                 class="w-full h-full max-h-[32vh] lg:max-h-[50vh] object-contain rounded-2xl drop-shadow-md">
                        </template>

                        {{-- Placeholder sin imágenes --}}
                        <template x-if="getImagenes().length === 0">
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </template>
                        
                        {{-- Controles anterior / siguiente --}}
                        <template x-if="getImagenes().length > 1">
                            <div class="absolute inset-0 flex items-center justify-between px-6 pointer-events-none">
                                <button @click="activePhoto = (activePhoto > 0) ? activePhoto - 1 : getImagenes().length - 1" 
                                        class="bg-white/70 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition pointer-events-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="3" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button @click="activePhoto = (activePhoto < getImagenes().length - 1) ? activePhoto + 1 : 0" 
                                        class="bg-white/70 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition pointer-events-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="3" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Miniaturas --}}
                    <div class="p-4 bg-white border-t flex gap-2 overflow-x-auto justify-center hide-scrollbar">
                        <template x-for="(img, index) in getImagenes()" :key="index">
                            <button @click="activePhoto = index" 
                                    class="w-14 h-14 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="activePhoto === index ? 'shadow-lg scale-105' : 'opacity-40 border-transparent'"
                                    :style="activePhoto === index ? 'border-color: ' + currentTraje.color_tienda : ''">
                                <img :src="'/storage/' + img.ruta_img" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════
                     DERECHA — INFO + TALLAS + ACCIONES
                     ══════════════════════════════════ --}}
                <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col overflow-y-auto max-h-[45vh] lg:max-h-full">
                    
                    {{-- Switch de género (solo si hay variante femenina) --}}
                    <template x-if="currentTraje.variante_femenina">
                        <div class="mb-6 p-1 bg-gray-100 rounded-2xl flex border max-w-xs shadow-inner">
                            <button type="button" 
                                    @click="generoSeleccionado = 'M'; activePhoto = 0; selectedUnit = ''"
                                    :class="generoSeleccionado === 'M' 
                                        ? 'bg-white text-gray-900 shadow-md font-black' 
                                        : 'text-gray-500 font-bold'"
                                    class="flex-1 py-2 text-xs uppercase rounded-xl transition-all">
                                🤵 Varón
                            </button>
                            <button type="button" 
                                    @click="generoSeleccionado = 'F'; activePhoto = 0; selectedUnit = ''"
                                    :class="generoSeleccionado === 'F' 
                                        ? 'bg-white text-gray-900 shadow-md font-black' 
                                        : 'text-gray-500 font-bold'"
                                    class="flex-1 py-2 text-xs uppercase rounded-xl transition-all">
                                💃 Damas
                            </button>
                        </div>
                    </template>

                    {{-- Etiqueta tienda --}}
                    <div class="inline-flex items-center gap-2 mb-4 bg-gray-50 px-4 py-2 rounded-full w-fit border border-gray-100">
                        <div class="w-2 h-2 rounded-full" :style="'background-color: ' + currentTraje.color_tienda"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">
                            Tienda: <span class="text-gray-900" x-text="currentTraje.tienda_nombre"></span>
                        </span>
                    </div>

                    {{-- Nombre dinámico --}}
                    <h2 class="text-2xl lg:text-3xl font-black uppercase leading-tight mb-2 tracking-tighter" 
                        :style="'color: ' + currentTraje.color_tienda" 
                        x-text="getNombre()">
                    </h2>
                    
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6">
                        Danza: <span class="text-gray-800" x-text="currentTraje.danza_nombre"></span>
                    </p>

                    {{-- ── PRECIO + TALLAS COMO BOTONES ── --}}
                    <div class="py-5 border-y border-gray-100 mb-6 bg-gray-50/50 rounded-3xl px-6 space-y-4">

                        {{-- Precio --}}
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Precio alquiler / día</p>
                            <p class="text-2xl font-black" :style="'color: ' + currentTraje.color_tienda">
                                Bs. <span x-text="generoSeleccionado === 'F' && currentTraje.variante_femenina 
                                    ? (currentTraje.variante_femenina.precio_efectivo ?? currentTraje.variante_femenina.pre_alquiler)
                                    : (currentTraje.precio_efectivo ?? currentTraje.pre_alquiler)">
                                </span>
                            </p>
                        </div>

                        {{-- Tallas disponibles clicables --}}
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                Elige tu talla para reservar
                            </p>

                            {{-- Con unidades disponibles --}}
                            <template x-if="getUnidadesDisponibles().length > 0">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="unidad in getUnidadesDisponibles()" :key="unidad.cod_unidad">
                                        <a :href="'/reservar/' + unidad.cod_unidad"
                                           class="group px-4 py-2.5 rounded-xl border-2 font-black text-xs uppercase 
                                                  transition-all duration-200 hover:shadow-md hover:scale-105 
                                                  flex flex-col items-center gap-0.5 cursor-pointer"
                                           :style="'border-color: ' + currentTraje.color_tienda + '40; color: ' + currentTraje.color_tienda + '; background-color: ' + currentTraje.color_tienda + '08'"
                                           x-on:mouseover="$el.style.backgroundColor = currentTraje.color_tienda + '22'"
                                           x-on:mouseleave="$el.style.backgroundColor = currentTraje.color_tienda + '08'">
                                            <span x-text="unidad.talla" class="text-sm leading-none"></span>
                                            <span class="text-[8px] font-semibold opacity-50 normal-case tracking-normal" 
                                                  x-text="unidad.estado_fisico">
                                            </span>
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

                    {{-- Descripción --}}
                    <div class="flex-grow mb-6">
                        <h4 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Detalles del traje
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed italic" 
                           x-text="getDescripcion() || 'Sin descripción adicional.'">
                        </p>
                    </div>

                    {{-- ── BOTÓN INFERIOR ── --}}
                    <div class="mt-auto">

                        {{-- Si hay stock → WhatsApp como secundario discreto --}}
                        <template x-if="getUnidadesDisponibles().length > 0">
                            <a :href="'https://wa.me/591' + currentTraje.whatsapp + '?text=Hola! Vengo del catálogo. Me interesa el traje: ' + getNombre()" 
                               target="_blank"
                               class="w-full py-3.5 rounded-2xl bg-white text-gray-500 font-bold uppercase 
                                      text-[10px] tracking-[0.15em] border-2 border-gray-200 
                                      flex items-center justify-center gap-2 
                                      transition hover:border-gray-400 hover:text-gray-700">
                                <x-si-whatsapp class="w-4 h-4 fill-current text-emerald-500" />
                                Consultar por WhatsApp
                            </a>
                        </template>

                        {{-- Si no hay stock → WhatsApp como única acción, estilo prominente --}}
                        <template x-if="getUnidadesDisponibles().length === 0">
                            <a :href="'https://wa.me/591' + currentTraje.whatsapp + '?text=Hola! Me interesa el traje: ' + getNombre() + '. ¿Cuándo habrá disponibilidad?'" 
                               target="_blank"
                               class="w-full py-4 rounded-2xl text-white font-black uppercase 
                                      text-[11px] tracking-[0.2em] shadow-xl 
                                      flex items-center justify-center gap-3 
                                      transition transform hover:scale-[1.02]"
                               :style="'background-color: ' + currentTraje.color_tienda">
                                <x-si-whatsapp class="w-5 h-5 fill-current" />
                                Consultar disponibilidad
                            </a>
                        </template>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</x-public-layout>

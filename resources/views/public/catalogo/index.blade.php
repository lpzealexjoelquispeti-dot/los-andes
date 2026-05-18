<x-public-layout>
    {{-- CONTENEDOR ALPINE PARA EL CATÁLOGO Y EL MODAL --}}
    <div x-data="{ 
            openModal: false, 
            currentTraje: {}, 
            activePhoto: 0,
            showTraje(traje) {
                this.currentTraje = traje;
                this.activePhoto = 0;
                this.openModal = true;
                document.body.classList.add('overflow-hidden');
            },
            closeModal() {
                this.openModal = false;
                document.body.classList.remove('overflow-hidden');
            }
         }" 
         class="max-w-7xl mx-auto py-16 px-4 min-h-screen">
        
        {{-- ==========================================
             CABECERA E INTELIGENCIA DE BÚSQUEDA
             ========================================== --}}
       

{{-- Búsquedas populares --}}

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

    <form x-ref="form" action="{{ route('public.catalogo.index') }}" method="GET" class="relative group">
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

    {{-- DROPDOWN SUGERENCIAS --}}
    <div x-show="mostrar"
         x-cloak
         class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100
                rounded-2xl overflow-hidden shadow-2xl z-50">
        <template x-for="s in sugerencias" :key="s.cod_termino">
            <button @click="elegir(s.termino_usuario)"
                    class="w-full px-6 py-3 text-left flex items-center justify-between
                           hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                <span class="font-bold text-sm uppercase text-gray-700"
                      x-text="s.termino_usuario"></span>
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

    {{-- Términos relacionados --}}
    @if(isset($terminosRelacionados) && $terminosRelacionados->count())
        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-3 px-2">
            También relacionado:
            <span class="text-gray-600">{{ $terminosRelacionados->implode(', ') }}</span>
        </p>
    @endif

    {{-- Limpiar búsqueda --}}
    @if(request('q'))
        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2 px-2">
            Resultados para: <span class="text-gray-700">{{ request('q') }}</span>
            — <a href="{{ route('public.catalogo.index') }}"
                 class="underline hover:text-andes-verde transition">Limpiar</a>
        </p>
    @endif

    {{-- Búsquedas populares --}}
    @if(!request('q') && isset($populares) && $populares->count())
        <div class="mt-4">
            <p class="text-gray-400 text-[9px] font-black uppercase tracking-widest mb-2 px-1">
                Búsquedas frecuentes
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($populares as $popular)
                    <a href="{{ route('public.catalogo.index', ['q' => $popular]) }}"
                       class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black uppercase
                              text-gray-500 hover:bg-andes-verde hover:text-white transition">
                        {{ $popular }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
        {{-- ==========================================
             GRID DE TRAJES (Catálogo)
             ========================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($trajes as $traje)
                @php 
                    // Extraemos los colores y datos de la tienda para este traje específico
                    $colorTienda = $traje->tienda->diseno->color_primario ?? '#16a34a';
                    
                    // Preparamos el JSON para Alpine (igual que en tu vista de tiendas)
                    $trajeJson = $traje->toArray();
                    $trajeJson['imagenes'] = $traje->imagenes;
                    $trajeJson['tienda_nombre'] = $traje->tienda->nom_tie;
                    $trajeJson['color_tienda'] = $colorTienda;
                    $trajeJson['danza_nombre'] = $traje->danza->nom_danza ?? 'General';
                    $trajeJson['whatsapp'] = $traje->tienda->diseno->link_whatsapp ?? '';
                @endphp
                
                {{-- CARTA DEL TRAJE (Click abre el modal) --}}
                <div @click="showTraje({{ json_encode($trajeJson) }})" 
                     class="group relative bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col h-full cursor-pointer">
                    
                    {{-- Área de la Imagen (CORREGIDO: ruta_img) --}}
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
                        
                        {{-- Badge de Danza --}}
                        <div class="absolute top-6 left-6">
                            <span class="bg-white/90 backdrop-blur-md text-gray-900 text-[9px] font-black uppercase px-4 py-2 rounded-full shadow-lg tracking-widest border border-gray-100">
                                {{ $traje->danza->nom_danza ?? 'General' }}
                            </span>
                        </div>
                        
                        {{-- Overlay al Hover --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="bg-white text-gray-900 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl scale-90 group-hover:scale-100 transition-transform">
                                Ver Detalles
                            </span>
                        </div>
                    </div>

                    {{-- Información --}}
                    <div class="p-8 flex-grow">
                        <h3 class="text-xl font-black text-gray-800 uppercase leading-tight mb-2 tracking-tighter">
                            {{ $traje->nom_traje }}
                        </h3>
                        
                        <div class="flex items-center gap-2 mt-4">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $colorTienda }}"></div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tienda: <span class="text-gray-800">{{ $traje->tienda->nom_tie }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Footer de la Carta --}}
                    <div class="px-8 pb-8 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Precio</span>
                            <span class="text-sm font-black uppercase" style="color: {{ $colorTienda }}">Bs. {{ number_format($traje->pre_alquiler, 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl group-hover:bg-gray-100 transition-colors">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>

            @empty
                {{-- EMPTY STATE: Si no hay resultados del buscador --}}
                <div class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-4 border-dashed border-gray-100">
                    <div class="mb-6 opacity-20 flex justify-center">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-400 uppercase tracking-tighter">No encontramos resultados para "{{ $query }}"</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">Intenta buscar por el nombre de la danza o un accesorio</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-16 flex justify-center">
            {{ $trajes->links() }}
        </div>


        {{-- ==========================================
             MODAL DE DETALLES DEL TRAJE (Alpine.js)
             ========================================== --}}
        <div x-show="openModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
             x-transition.opacity
             x-cloak
             style="display: none;">
            
            <div class="bg-white w-full max-w-5xl max-h-[90vh] rounded-[3rem] overflow-hidden shadow-2xl relative flex flex-col lg:flex-row" 
                 @click.away="closeModal()">
                
                {{-- Botón Cerrar --}}
                <button @click="closeModal()" 
                        class="absolute top-6 right-6 z-50 text-white bg-black/20 hover:bg-black/50 p-2 rounded-full backdrop-blur-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                {{-- CARRUSEL DE IMÁGENES (Izquierda) --}}
                <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col h-[40vh] lg:h-auto">
                    <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-gray-200">
                        {{-- Foto Activa --}}
                        <template x-if="currentTraje.imagenes && currentTraje.imagenes.length > 0">
                            <img :src="'/storage/' + currentTraje.imagenes[activePhoto].ruta_img" 
                                 class="w-full h-full object-cover">
                        </template>
                        
                        {{-- Controles Anterior/Siguiente (Solo si hay más de 1 foto) --}}
                        <template x-if="currentTraje.imagenes?.length > 1">
                            <div class="absolute inset-0 flex items-center justify-between px-4">
                                <button @click="activePhoto = (activePhoto > 0) ? activePhoto - 1 : currentTraje.imagenes.length - 1" 
                                        class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="activePhoto = (activePhoto < currentTraje.imagenes.length - 1) ? activePhoto + 1 : 0" 
                                        class="bg-white/30 hover:bg-white/60 text-white p-2 rounded-full backdrop-blur-md transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Miniaturas --}}
                    <div class="p-4 bg-white border-t flex gap-2 overflow-x-auto justify-center hide-scrollbar">
                        <template x-for="(img, index) in currentTraje.imagenes" :key="index">
                            <button @click="activePhoto = index" 
                                    class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="activePhoto === index ? 'shadow-lg scale-110' : 'opacity-40 border-transparent'"
                                    :style="activePhoto === index ? 'border-color: ' + currentTraje.color_tienda : ''">
                                <img :src="'/storage/' + img.ruta_img" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- INFORMACIÓN DEL TRAJE (Derecha) --}}
                <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col overflow-y-auto">
                    {{-- Etiqueta de la Tienda Dueña --}}
                    <div class="inline-flex items-center gap-2 mb-6 bg-gray-50 px-4 py-2 rounded-full w-fit border border-gray-100">
                        <div class="w-2 h-2 rounded-full" :style="'background-color: ' + currentTraje.color_tienda"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">Tienda: <span class="text-gray-900" x-text="currentTraje.tienda_nombre"></span></span>
                    </div>

                    <h2 class="text-3xl lg:text-4xl font-black uppercase leading-tight mb-2 tracking-tighter" 
                        :style="'color: ' + currentTraje.color_tienda" 
                        x-text="currentTraje.nom_traje"></h2>
                    
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-8">
                        Danza Oficial: <span class="text-gray-800" x-text="currentTraje.danza_nombre"></span>
                    </p>
                    
                    {{-- Precios y Tallas --}}
                    <div class="grid grid-cols-2 gap-6 py-6 border-y border-gray-100 mb-8 bg-gray-50/50 rounded-3xl px-6">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Precio Alquiler</p>
                            <p class="text-3xl font-black" :style="'color: ' + currentTraje.color_tienda">
                                Bs. <span x-text="currentTraje.pre_alquiler"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Talla Disponible</p>
                            <p class="text-2xl font-black text-gray-800" x-text="currentTraje.talla_traje"></p>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="flex-grow">
                        <h4 class="text-[10px] font-black text-gray-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            Detalles del Traje
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed italic" x-text="currentTraje.des_traje || 'Sin descripción adicional detallada.'"></p>
                    </div>

                    {{-- Botón WhatsApp Dinámico --}}
                    <div class="mt-10">
                        <a :href="'https://wa.me/591' + currentTraje.whatsapp + '?text=Hola! Vengo del catálogo global. Me interesa alquilar el traje: ' + currentTraje.nom_traje" 
                           target="_blank"
                           class="w-full py-5 rounded-2xl text-white font-black uppercase text-[11px] tracking-[0.2em] shadow-xl flex items-center justify-center gap-3 transition transform hover:scale-105"
                           :style="'background-color: ' + currentTraje.color_tienda">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.481 5.229 3.481 8.404c0 6.556-5.332 11.888-11.888 11.888-2.01 0-3.986-.511-5.741-1.483l-6.243 1.608zm11.888-21.726c-5.43 0-9.85 4.42-9.85 9.85 0 2.111.666 4.064 1.811 5.672l-1.008 3.679 3.768-1.01c1.53.899 3.284 1.373 5.079 1.373 5.43 0 9.85-4.42 9.85-9.85 0-5.43-4.42-9.85-9.85-9.85zm4.847 7.039c-.266-.134-1.576-.777-1.821-.865-.246-.089-.425-.134-.605.134-.18.267-.695.865-.85 1.042-.156.177-.311.201-.577.067-.266-.134-1.124-.414-2.141-1.321-.792-.706-1.326-1.578-1.482-1.845-.156-.267-.016-.411.117-.544.12-.119.266-.311.399-.467.133-.156.177-.267.266-.444.089-.177.045-.333-.023-.467-.067-.134-.605-1.458-.828-2.001-.217-.521-.437-.45-.605-.459-.156-.008-.336-.009-.516-.009s-.472.067-.719.333c-.246.267-.941.921-.941 2.247s.964 2.61 1.097 2.788c.134.178 1.897 2.897 4.594 4.059.64.276 1.141.441 1.531.565.644.203 1.23.174 1.692.105.515-.077 1.576-.644 1.799-1.266.223-.622.223-1.155.156-1.266-.067-.111-.246-.177-.512-.311z"/></svg>
                            Alquilar en WhatsApp
                        </a>
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
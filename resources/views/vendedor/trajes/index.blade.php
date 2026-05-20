<x-app-layout>
    <x-slot name="header">
        Mi Catálogo de Trajes
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4" x-data="{ 
        openModal: false, 
        activePhoto: 0,
        generoActivo: 'Masculino',
        
        trajePadre: null,    
        trajeHijo: null,     
        currentTraje: {},    
        tieneVariante: false,

        showTraje(traje, tieneHijo, hijoData) {
            this.trajePadre = traje;
            this.tieneVariante = tieneHijo;
            this.trajeHijo = hijoData;
            
            // Inicialización limpia al abrir el modal
            this.currentTraje = this.trajePadre;
            this.generoActivo = 'Masculino';
            this.activePhoto = 0;
            this.openModal = true;
        },
        
        switchGenero(genero) {
            this.generoActivo = genero;
            this.activePhoto = 0;

            if (genero === 'Femenino' && this.trajeHijo) {
                this.currentTraje = this.trajeHijo;
            } else {
                this.currentTraje = this.trajePadre;
            }
        }
    }">
        
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-black text-andes-oscuro uppercase">Trajes Publicados</h2>
            <a href="{{ route('vendedor.trajes.create') }}" class="bg-andes-verde text-white px-6 py-3 rounded-xl font-black shadow-lg hover:scale-105 transition uppercase text-sm">
                + Nuevo Traje
            </a>
        </div>

        {{-- GRID DE TRAJES CONSOLIDADOS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($trajes as $traje)
                @php
                    // 1. Saltamos preventivamente los hijos directos ya que se agrupan con el padre
                    if ($traje->cod_traje_padre !== null && $traje->cod_traje_padre != 0) {
                        continue;
                    }

                    // 2. CRUCIAL: Recuperamos la variante forzando la lectura incluso si está borrada lógicamente
                    $hijo = $traje->varianteFemenina()->withTrashed()->first(); 

                    // 3. Determinar estado de baja lógica global de la colección
                    $ambosBorrados = false;
                    if ($hijo) {
                        $ambosBorrados = $traje->trashed() && $hijo->trashed();
                    } else {
                        $ambosBorrados = $traje->trashed();
                    }

                    // 4. Preparación de payloads JSON para inyectar a JavaScript (Padre)
                    $trajeJson = $traje->toArray();
                    $trajeJson['is_trashed'] = $traje->trashed();
                    $trajeJson['danza'] = $traje->danza;
                    $trajeJson['imagenes'] = $traje->imagenes;
                    $trajeJson['unidades'] = $traje->unidades;

                    // 5. Preparación de payloads JSON para JavaScript (Hijo)
                    $hijoJson = null;
                    if ($hijo) {
                        $hijoJson = $hijo->toArray();
                        $hijoJson['is_trashed'] = $hijo->trashed();
                        $hijoJson['danza'] = $traje->danza;
                        $hijoJson['imagenes'] = $hijo->imagenes;
                        $hijoJson['unidades'] = $hijo->unidades;
                    }
                @endphp

                <div @click="showTraje({{ json_encode($trajeJson) }}, {{ $hijo ? 'true' : 'false' }}, {{ json_encode($hijoJson) }})" 
                     class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden cursor-pointer group flex flex-col h-full relative {{ $ambosBorrados ? 'opacity-60 grayscale-[0.6]' : '' }}">
                    
                    {{-- Badge de Estado Unificado Inteligente --}}
                    <div class="absolute top-2 right-2 z-10 flex flex-col gap-1 items-end">
                        @if($ambosBorrados)
                            <span class="bg-gray-800 text-white text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-tighter">Colección Inactiva</span>
                        @elseif($hijo && ($traje->trashed() || $hijo->trashed()))
                            <span class="bg-amber-500 text-white text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-tighter shadow-sm">Parcial Inactivo</span>
                        @else
                            <span class="bg-andes-verde text-white text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-tighter shadow-sm">En Vitrina</span>
                        @endif
                    </div>

                    {{-- CONTENEDOR DE LA FOTO + BOTONES DE ATAJO TRANSACCIONAL --}}
                    <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                        
                        {{-- ATAJO RÁPIDO: Dar de Baja Colección Completa (Si está activa) --}}
                        @if(!$ambosBorrados)
                            <div class="absolute top-2 left-2 z-30">
                                <button type="button" 
                                        @click.stop="if(confirm('¿Quieres dar de baja la colección completa (Varón y Damas)?')) { document.getElementById('delete-total-form-{{ $traje->cod_traje }}').submit(); }" 
                                        class="bg-black/60 backdrop-blur-sm text-white p-2 rounded-lg hover:bg-andes-rojo hover:scale-105 transition shadow-md flex items-center justify-center group"
                                        title="Desactivar Colección Completa">
                                    <svg class="w-4 h-4 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- ATAJO RÁPIDO: Restaurar Colección Completa (Si está inactiva) --}}
                        @if($ambosBorrados)
                            <div class="absolute top-2 left-2 z-30">
                                <button type="button" 
                                        @click.stop="if(confirm('¿Quieres reactivar la colección completa (Varón y Damas) en la vitrina pública?')) { document.getElementById('restore-total-form-{{ $traje->cod_traje }}').submit(); }" 
                                        class="bg-black/60 backdrop-blur-sm text-white p-2 rounded-lg hover:bg-andes-verde hover:scale-105 transition shadow-md flex items-center justify-center group"
                                        title="Reactivar Colección Completa">
                                    <svg class="w-4 h-4 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @if($traje->imagenes->first())
                            <img src="{{ asset('storage/' . $traje->imagenes->first()->ruta_img) }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                            </div>
                        @endif

                        <div class="absolute bottom-2 left-2 flex gap-1 flex-wrap z-10">
                            <span class="bg-andes-rojo/90 backdrop-blur-sm text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">
                                {{ $traje->danza->nom_danza }}
                            </span>
                            @if($hijo)
                                <span class="bg-blue-600/90 backdrop-blur-sm text-white text-[9px] font-black px-1.5 py-0.5 rounded-md uppercase tracking-wider">👫 Pareja</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-3 flex flex-col flex-grow">
                        <h3 class="font-black text-gray-800 text-sm uppercase leading-tight line-clamp-2 mb-2 h-10">
                            {{ str_replace(' - Varón', '', $traje->nom_traje) }}
                        </h3>
                        
                        <div class="mt-auto pt-2 border-t border-gray-50 flex justify-between items-end">
                            <div>
                                <p class="text-andes-verde font-black text-base leading-none">
                                    <span class="text-xs">Bs.</span> {{ number_format($traje->pre_alquiler, 0) }}
                                </p>
                            </div>
                            <div class="text-right flex gap-1">
                                @php
                                    $tallasPadre = $traje->unidades->pluck('talla')->toArray();
                                    $tallasHijo = $hijo ? $hijo->unidades->pluck('talla')->toArray() : [];
                                    $tallasTotales = array_unique(array_merge($tallasPadre, $tallasHijo));
                                    sort($tallasTotales);
                                @endphp
                                @foreach($tallasTotales as $t)
                                    <span class="text-[9px] font-bold text-gray-500 bg-gray-100 px-1 rounded">{{ $t }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold uppercase tracking-widest">No tienes trajes registrados</p>
                </div>
            @endforelse
        </div>

        {{-- MODAL DE DETALLE INTEGRADO CON SELECTOR INTERACTIVO --}}
        <div x-show="openModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-andes-oscuro/90 backdrop-blur-md"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            style="display: none;">
            
            <div class="bg-white w-full max-w-5xl max-h-[90vh] rounded-3xl overflow-hidden shadow-2xl relative flex flex-col lg:flex-row" 
                @click.away="openModal = false">
                
                <button @click="openModal = false" class="absolute top-4 right-4 z-50 bg-andes-rojo text-white p-2 rounded-full hover:scale-110 transition shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                {{-- COLUMNA IZQUIERDA: CARRUSEL DE FOTOS --}}
                <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col">
                    <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-black aspect-video lg:aspect-auto">
                        <template x-if="currentTraje.imagenes && currentTraje.imagenes.length > 0">
                            <img :src="'/storage/' + currentTraje.imagenes[activePhoto].ruta_img" 
                                class="max-w-full max-h-full object-contain transition-all duration-500">
                        </template>

                        <template x-if="currentTraje.imagenes?.length > 1">
                            <div class="absolute inset-0 flex items-center justify-between px-4">
                                <button @click="activePhoto = (activePhoto > 0) ? activePhoto - 1 : currentTraje.imagenes.length - 1" 
                                        class="bg-white/20 hover:bg-white/40 text-white p-2 rounded-full backdrop-blur-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="activePhoto = (activePhoto < currentTraje.imagenes.length - 1) ? activePhoto + 1 : 0" 
                                        class="bg-white/20 hover:bg-white/40 text-white p-2 rounded-full backdrop-blur-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="p-4 bg-white border-t flex gap-2 overflow-x-auto justify-center">
                        <template x-for="(img, index) in currentTraje.imagenes" :key="index">
                            <button @click="activePhoto = index" 
                                    class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="activePhoto === index ? 'border-andes-verde scale-110 shadow-lg' : 'border-transparent opacity-50'">
                                <img :src="'/storage/' + img.ruta_img" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: DATOS ESPECÍFICOS --}}
                <div class="w-full lg:w-1/2 p-8 lg:p-10 flex flex-col overflow-y-auto">
                    
                    {{-- SELECTOR DE GÉNERO CON INTERCAMBIO DE MEMORIA ESTABLE --}}
                    <template x-if="tieneVariante">
                        <div class="mb-6 grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-2xl border">
                            <button type="button" @click="switchGenero('Masculino')"
                                    :class="generoActivo === 'Masculino' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'"
                                    class="py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1">
                                ♂️ Bloque Varón
                            </button>
                            <button type="button" @click="switchGenero('Femenino')"
                                    :class="generoActivo === 'Femenino' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'"
                                    class="py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1">
                                ♀️ Bloque Damas
                            </button>
                        </div>
                    </template>

                    <div class="mb-4 flex items-center gap-2">
                        <span class="bg-andes-rojo/10 text-andes-rojo text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest" x-text="currentTraje.danza?.nom_danza"></span>
                        <template x-if="currentTraje.deleted_at">
                            <span class="bg-gray-800 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Variante Inactiva</span>
                        </template>
                    </div>
                    
                    <h2 class="text-3xl font-black text-andes-oscuro uppercase leading-tight mb-4" x-text="currentTraje.nom_traje"></h2>
                    
                    <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-100 mt-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Precio Alquiler</p>
                            <p class="text-2xl font-black text-andes-verde">Bs. <span x-text="currentTraje.pre_alquiler"></span></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Color Dominante</p>
                            <p class="text-lg font-black text-gray-800" x-text="currentTraje.color_traje"></p>
                        </div>
                    </div>

                    {{-- TALLAS DINÁMICAS ASOCIADAS A LA VARIANTE CARGADA --}}
                    <div class="py-4 border-b border-gray-100 mb-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tallas en Existencia (Este Bloque)</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-if="currentTraje.unidades && currentTraje.unidades.length > 0">
                                <template x-for="unidad in currentTraje.unidades" :key="unidad.cod_unidad">
                                    <span class="bg-emerald-50 border border-emerald-200 text-andes-verde font-bold text-xs px-3 py-1.5 rounded-xl uppercase shadow-sm">
                                        Talla <span x-text="unidad.talla"></span>
                                    </span>
                                </template>
                            </template>
                            <template x-if="!currentTraje.unidades || currentTraje.unidades.length === 0">
                                <span class="text-xs font-bold text-gray-400 italic">Sin existencias (Requiere cargar stock)</span>
                            </template>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Descripción de la Variante</h4>
                        <p class="text-gray-600 text-sm leading-relaxed" x-text="currentTraje.des_traje"></p>
                    </div>

                    {{-- CAPA DE CONTROL TRANSACCIONAL --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 flex gap-3">
                        <a :href="'/vendedor/trajes/' + currentTraje.cod_traje + '/edit'" 
                           class="flex-1 bg-andes-oscuro hover:bg-black text-white py-4 rounded-xl font-black text-center uppercase tracking-widest transition shadow-lg text-xs">
                            Editar Variante
                        </a>

                        <template x-if="!currentTraje.deleted_at">
                            <button @click="if(confirm('¿Seguro que quieres desactivar únicamente esta variante del catálogo público?')) document.getElementById('delete-form-' + currentTraje.cod_traje).submit()" 
                                    class="bg-gray-100 text-gray-400 px-6 rounded-xl hover:bg-andes-rojo hover:text-white transition flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </template>

                        <template x-if="currentTraje.deleted_at">
                            <button @click="document.getElementById('restore-form-' + currentTraje.cod_traje).submit()" 
                                    class="bg-andes-verde text-white px-6 rounded-xl hover:bg-green-700 transition flex items-center gap-2 font-black text-xs uppercase tracking-widest">
                                Reactivar Bloque
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORMULARIOS OCULTOS DE CONTROL INDIVIDUAL Y TOTAL --}}
        @foreach($trajes as $traje)
            
            <form id="delete-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.destroy', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf 
                @method('DELETE')
            </form>
            
            <form id="delete-total-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.destroyTotal', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf
            </form>

            <form id="restore-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.restore', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf
            </form>

            <form id="restore-total-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.restoreTotal', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf
            </form>
            
            {{-- Condicional de formularios para variantes hijas con SoftDeletes --}}
            @if($traje->varianteFemenina()->withTrashed()->exists())
                @php $hijoId = $traje->varianteFemenina()->withTrashed()->first()->cod_traje; @endphp
                <form id="delete-form-{{ $hijoId }}" action="{{ route('vendedor.trajes.destroy', $hijoId) }}" method="POST" style="display: none;">
                    @csrf 
                    @method('DELETE')
                </form>
                <form id="restore-form-{{ $hijoId }}" action="{{ route('vendedor.trajes.restore', $hijoId) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif

        @endforeach
    </div>
</x-app-layout>
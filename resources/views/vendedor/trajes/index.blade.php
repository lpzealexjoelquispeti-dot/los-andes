<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<x-app-layout>
    <x-slot name="header">
        Mi Catálogo de Trajes
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4" x-data="{ 
        openModal: false, 
        currentTraje: {}, 
        activePhoto: 0,
        showTraje(traje) {
            this.currentTraje = traje;
            this.activePhoto = 0;
            this.openModal = true;
        }
    }">
        
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-black text-andes-oscuro uppercase">Trajes Publicados</h2>
            <a href="{{ route('vendedor.trajes.create') }}" class="bg-andes-verde text-white px-6 py-3 rounded-xl font-black shadow-lg hover:scale-105 transition uppercase text-sm">
                + Nuevo Traje
            </a>
        </div>

        {{-- GRID DE TRAJES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($trajes as $traje)
                @php
                    $trajeJson = $traje->toArray();
                    $trajeJson['is_trashed'] = $traje->trashed();
                    $trajeJson['danza'] = $traje->danza;
                    $trajeJson['imagenes'] = $traje->imagenes;
                @endphp

                <div @click="showTraje({{ json_encode($trajeJson) }})" 
                     class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden cursor-pointer group flex flex-col h-full relative {{ $traje->trashed() ? 'opacity-75 grayscale-[0.5]' : '' }}">
                    
                    {{-- Badge de Estado (ACTIVO / INACTIVO) --}}
                    <div class="absolute top-2 right-2 z-10">
                        @if($traje->trashed())
                            <span class="bg-gray-800 text-white text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-tighter">Inactivo</span>
                        @else
                            <span class="bg-andes-verde text-white text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-tighter shadow-sm">En Vitrina</span>
                        @endif
                    </div>

                    <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                        @if($traje->imagenes->first())
                            <img src="{{ asset('storage/' . $traje->imagenes->first()->ruta_img) }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                            </div>
                        @endif

                        <div class="absolute bottom-2 left-2">
                            <span class="bg-andes-rojo/90 backdrop-blur-sm text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">
                                {{ $traje->danza->nom_danza }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-3 flex flex-col flex-grow">
                        <h3 class="font-black text-gray-800 text-sm uppercase leading-tight line-clamp-2 mb-2 h-10">
                            {{ $traje->nom_traje }}
                        </h3>
                        
                        <div class="mt-auto pt-2 border-t border-gray-50 flex justify-between items-end">
                            <div>
                                <p class="text-andes-verde font-black text-base leading-none">
                                    <span class="text-xs">Bs.</span> {{ number_format($traje->pre_alquiler, 0) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2 py-1 rounded-md">
                                    {{ $traje->talla_traje }}
                                </span>
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

        {{-- MODAL DE DETALLE --}}
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

                <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col">
                    <div class="relative flex-grow flex items-center justify-center overflow-hidden bg-black">
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

                <div class="w-full lg:w-1/2 p-8 lg:p-10 flex flex-col overflow-y-auto">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="bg-andes-rojo/10 text-andes-rojo text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest" x-text="currentTraje.danza?.nom_danza"></span>
                        <template x-if="currentTraje.deleted_at">
                            <span class="bg-gray-800 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Inactivo</span>
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

                    <div class="py-4 border-b border-gray-100 mb-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tallas Disponibles en Tienda</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-if="currentTraje.unidades && currentTraje.unidades.length > 0">
                                <template x-for="unidad in currentTraje.unidades" :key="unidad.cod_unidad">
                                    <span class="bg-emerald-50 border border-emerald-200 text-andes-verde font-bold text-xs px-3 py-1.5 rounded-xl uppercase shadow-sm">
                                        Talla <span x-text="unidad.talla"></span>
                                    </span>
                                </template>
                            </template>
                            <template x-if="!currentTraje.unidades || currentTraje.unidades.length === 0">
                                <span class="text-xs font-bold text-gray-400 italic">Sin existencias registradas</span>
                            </template>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Descripción</h4>
                        <p class="text-gray-600 text-sm leading-relaxed" x-text="currentTraje.des_traje"></p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex gap-3">
                        {{-- BOTÓN EDITAR --}}
                        <a :href="'/vendedor/trajes/' + currentTraje.cod_traje + '/edit'" 
                        class="flex-1 bg-andes-oscuro hover:bg-black text-white py-4 rounded-xl font-black text-center uppercase tracking-widest transition shadow-lg text-xs">
                            Editar Traje
                        </a>

                        {{-- BOTÓN DINÁMICO CON SWEETALERT2 --}}
                        <template x-if="!currentTraje.deleted_at">
                            <button @click="
                                Swal.fire({
                                    title: '¿Seguro que quieres desactivar este traje?',
                                    text: 'No aparecerá en el catálogo público hasta que lo reactives.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Sí, desactivar',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('delete-form-' + currentTraje.cod_traje).submit();
                                    }
                                })
                            " 
                            class="bg-gray-100 text-gray-400 px-6 rounded-xl hover:bg-andes-rojo hover:text-white transition group flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </template>

                        <template x-if="currentTraje.deleted_at">
                            <button @click="document.getElementById('restore-form-' + currentTraje.cod_traje).submit()" 
                                    class="bg-andes-verde text-white px-6 rounded-xl hover:bg-green-700 transition flex items-center gap-2 font-black text-xs uppercase tracking-widest">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Reactivar
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORMULARIOS OCULTOS PARA ACCIONES --}}
        @foreach($trajes as $traje)
            <form id="delete-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.destroy', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf @method('DELETE')
            </form>
            <form id="restore-form-{{ $traje->cod_traje }}" action="{{ route('vendedor.trajes.restore', $traje->cod_traje) }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endforeach
    </div>
</x-app-layout>

{{-- Alertas globales de éxito para el CRUD --}}
@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Excelente!',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false
        });
    </script>
@endif
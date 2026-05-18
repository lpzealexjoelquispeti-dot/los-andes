<x-app-layout>
    <x-slot name="header">Inteligencia de Búsqueda - Tesauro</x-slot>

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            openModal: false,
            danzaActiva: {
                nom_danza: '',
                clasificacion: '',
                imagen_danza: '',
                tesauros: []
            },
            scrollPos: 0,

            abrirModal(danza) {
                this.danzaActiva = danza
                this.openModal = true
                document.body.classList.add('overflow-hidden')
            },

            cerrarModal() {
                this.openModal = false
                document.body.classList.remove('overflow-hidden')
            },

            scrollCarrusel(dir) {
                const el = document.getElementById('carrusel')
                el.scrollBy({ left: dir * 220, behavior: 'smooth' })
            }
         }">

        {{-- CARRUSEL --}}
        <div class="mb-12">
            <div class="flex justify-between items-end mb-6 ml-2">
                <div>
                    <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Explorar Inteligencia por Danza</h4>
                    <p class="text-[10px] text-andes-verde font-bold uppercase mt-1">Haga clic para ver el glosario específico</p>
                </div>
                <a href="{{ route('admin.tesauro.create') }}"
                   class="bg-andes-oscuro text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Término
                </a>
            </div>

            {{-- Track --}}
            <div id="carrusel" class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide scroll-smooth">
                @foreach($danzas as $danza)
                <button type="button"
                        @click='abrirModal(@json($danza))'
                        class="flex-none w-48 group">
                    <div class="bg-white p-4 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-xl hover:scale-105 transition-all duration-300 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden border-2 border-gray-50 shadow-inner group-hover:border-andes-verde transition-colors">
                            <img src="{{ asset('storage/' . $danza->imagen_danza) }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <h5 class="text-[10px] font-black uppercase text-gray-700 truncate tracking-tighter">
                            {{ $danza->nom_danza }}
                        </h5>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                            {{ $danza->clasificacion }}
                        </p>
                    </div>
                </button>
                @endforeach
            </div>

            {{-- Botones de navegación --}}
            <div class="flex items-center justify-center gap-4 mt-6">
                <button @click="scrollCarrusel(-1)"
                        class="group w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-andes-verde transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-gray-300">Deslizar</p>

                <button @click="scrollCarrusel(1)"
                        class="group w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-andes-verde transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Término</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Nivel / Tipo</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Danza Ref.</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($terminos as $termino)
                    <tr class="{{ $termino->trashed() ? 'bg-gray-50/50 opacity-60' : 'hover:bg-gray-50/30' }} transition-colors">
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $termino->trashed() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $termino->trashed() ? 'Inactivo' : 'Activo' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 font-black text-gray-800 uppercase text-sm tracking-tight">{{ $termino->termino_usuario }}</td>
                        <td class="px-8 py-5">
                            @php
                                $color = ['ortografia' => 'text-emerald-500', 'sinonimo' => 'text-amber-500', 'referencia' => 'text-purple-500'][$termino->tipo] ?? 'text-gray-400';
                            @endphp
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $termino->tipo }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-xs font-bold text-gray-400 italic">{{ $termino->danza->nom_danza ?? 'S/N' }}</td>
                        <td class="px-8 py-5 text-right flex justify-end gap-2">
                            @if(!$termino->trashed())
                                <a href="{{ route('admin.tesauro.edit', $termino->cod_termino) }}" class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:scale-110 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.tesauro.destroy', $termino->cod_termino) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="p-2 bg-red-50 text-red-600 rounded-xl hover:scale-110 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.tesauro.restore', $termino->cod_termino) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="p-2 bg-green-50 text-green-600 rounded-xl hover:scale-110 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MODAL --}}
        <div x-show="openModal"
             x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-md">

            <div class="relative bg-[#0F172A] w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] border border-slate-700"
                 @click.away="cerrarModal()"
                 @keydown.escape.window="cerrarModal()">

                <div class="p-10">

                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h2 class="text-3xl font-black text-white uppercase tracking-tighter italic" x-text="'TESAURO ' + danzaActiva.nom_danza"></h2>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Variantes regionales y términos de búsqueda</p>
                        </div>
                        <button @click="cerrarModal()" class="text-slate-500 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-5 mb-6 px-1">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Ortografía</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Sinónimo</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-purple-400"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Accesorio</span>
                        </div>
                    </div>

                    <div class="space-y-4">

                        <template x-if="danzaActiva.tesauros.filter(i => i.tipo == 'ortografia').length > 0">
                            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-[2rem] p-6">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-400 mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)] inline-block"></span>
                                    Ortografía
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <template x-for="t in danzaActiva.tesauros.filter(i => i.tipo == 'ortografia')" :key="t.cod_termino">
                                        <div class="px-4 py-2 bg-emerald-400/15 border border-emerald-400/30 rounded-full">
                                            <span class="text-[10px] font-black text-emerald-300 uppercase tracking-widest" x-text="t.termino_usuario"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="danzaActiva.tesauros.filter(i => i.tipo == 'sinonimo').length > 0">
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-[2rem] p-6">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-amber-400 mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.8)] inline-block"></span>
                                    Sinónimo
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <template x-for="t in danzaActiva.tesauros.filter(i => i.tipo == 'sinonimo')" :key="t.cod_termino">
                                        <div class="px-4 py-2 bg-amber-400/15 border border-amber-400/30 rounded-full">
                                            <span class="text-[10px] font-black text-amber-300 uppercase tracking-widest" x-text="t.termino_usuario"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="danzaActiva.tesauros.filter(i => i.tipo == 'referencia').length > 0">
                            <div class="bg-purple-500/10 border border-purple-500/20 rounded-[2rem] p-6">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-purple-400 mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400 shadow-[0_0_8px_rgba(192,132,252,0.8)] inline-block"></span>
                                    Accesorio
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <template x-for="t in danzaActiva.tesauros.filter(i => i.tipo == 'referencia')" :key="t.cod_termino">
                                        <div class="px-4 py-2 bg-purple-400/15 border border-purple-400/30 rounded-full">
                                            <span class="text-[10px] font-black text-purple-300 uppercase tracking-widest" x-text="t.termino_usuario"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="danzaActiva.tesauros.length === 0">
                            <div class="flex flex-col items-center justify-center py-14 opacity-30">
                                <svg class="w-12 h-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Sin variantes registradas</p>
                            </div>
                        </template>

                    </div>

                    <div class="mt-8 bg-amber-500/5 border border-amber-500/10 p-5 rounded-2xl flex gap-4 items-center">
                        <div class="text-amber-500 bg-amber-500/10 p-2 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[9px] text-slate-400 font-medium leading-relaxed italic">
                            <strong class="text-amber-500 uppercase">Tip de Ingeniería:</strong> El sistema de tesauro permite que si un usuario busca un término coloquial, el motor relacione automáticamente la danza oficial para mejorar la conversión.
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
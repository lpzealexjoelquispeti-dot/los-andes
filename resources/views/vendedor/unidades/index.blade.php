<x-app-layout>
    <x-slot name="header">
        Inventario: {{ str_replace(' - Varón', '', $trajeActivo->nom_traje) }}
    </x-slot>

    @php
        // Recuperamos la variante forzando la lectura de SoftDeletes
        $hijo = $trajeActivo->varianteFemenina()->withTrashed()->first();

        // Formateamos los payloads estables para Alpine.js
        $padrePayload = $trajeActivo->toArray();
        $padrePayload['unidades'] = $trajeActivo->unidades()->withTrashed()->get()->toArray();
        $padrePayload['danza'] = $trajeActivo->danza;

        $hijoPayload = null;
        if ($hijo) {
            $hijoPayload = $hijo->toArray();
            $hijoPayload['unidades'] = $hijo->unidades()->withTrashed()->get()->toArray();
            $hijoPayload['danza'] = $trajeActivo->danza;
        }
    @endphp

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            scrollCarrusel(dir) {
                const el = document.getElementById('carrusel-trajes');
                el.scrollBy({ left: dir * 240, behavior: 'smooth' })
            },
            
            // VARIABLES DE ESTADO INTEGRADO (EL TRUCO)
            generoActivo: 'Masculino',
            trajePadre: {{ json_encode($padrePayload) }},
            trajeHijo: {{ json_encode($hijoPayload) }},
            currentTraje: {},
            tieneVariante: {{ $hijo ? 'true' : 'false' }},

            init() {
                // Al arrancar, el inventario apunta por defecto al Varón (Padre)
                this.currentTraje = this.trajePadre;
            },

            switchGenero(genero) {
                this.generoActivo = genero;
                if (genero === 'Femenino' && this.trajeHijo) {
                    this.currentTraje = this.trajeHijo;
                } else {
                    this.currentTraje = this.trajePadre;
                }
                
                // Desparamos un evento para que el Perchero actualice sus filas al instante
                $dispatch('genero-cambiado', { unidades: this.currentTraje.unidades });
            },

            countTalla(talla) {
                if(!this.currentTraje.unidades) return 0;
                return this.currentTraje.unidades.filter(u => u.talla === talla && !u.deleted_at && u.disponibilidad).length;
            }
         }">

        {{-- ══ 1. CARRUSEL SUPERIOR DE TRAJES ══ --}}
        <div class="mb-10">
            <div class="flex justify-between items-end mb-6 ml-2">
                <div>
                    <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Navegar por tus Trajes</h4>
                    <p class="text-[10px] text-andes-verde font-bold uppercase mt-1">Selecciona un traje del carrusel para gestionar su stock físico</p>
                </div>

                <div class="flex items-center gap-3">
                    <a :href="'/vendedor/trajes/' + currentTraje.cod_traje + '/unidades/danos'"
                       class="bg-red-600 text-white px-5 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Control de Daños
                    </a>

                    <a :href="'/vendedor/trajes/' + currentTraje.cod_traje + '/unidades/nueva'"
                       class="bg-andes-oscuro text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir Prenda Física
                    </a>
                </div>
            </div>

            {{-- Track del Carrusel --}}
            <div id="carrusel-trajes" class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide scroll-smooth">
                @foreach($todosLosTrajes as $t)
                    @php
                        // Contamos el stock combinando ambas ramas para reflejar el total real de la tienda
                        $stockTotal = $t->unidades->where('disponibilidad', true)->where('deleted_at', null)->count() + 
                                      ($t->varianteFemenina ? $t->varianteFemenina->unidades->where('disponibilidad', true)->where('deleted_at', null)->count() : 0);
                        
                        $primeraFoto = $t->imagenes->first()
                            ? asset('storage/' . $t->imagenes->first()->ruta_img)
                            : asset('img/default-traje.jpg');
                        $esElActivo = $t->cod_traje == $trajeActivo->cod_traje;
                    @endphp

                    <a href="{{ route('vendedor.trajes.unidades.index', $t->cod_traje) }}"
                       class="flex-none w-52 group focus:outline-none">
                        <div class="bg-white p-5 rounded-[2.5rem] border transition-all duration-300 text-center relative shadow-sm
                                    {{ $esElActivo ? 'border-andes-verde ring-2 ring-andes-verde/20 shadow-lg scale-105' : 'border-gray-100 hover:shadow-md hover:scale-102' }}">

                            <span class="absolute top-4 right-4 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-tight
                                         {{ $stockTotal > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                {{ $stockTotal }} Uds
                            </span>

                            <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-2 border-gray-50 shadow-inner
                                        {{ $esElActivo ? 'border-andes-verde' : 'group-hover:border-andes-verde' }} transition-colors">
                                <img src="{{ $primeraFoto }}" class="w-full h-full object-cover" alt="{{ $t->nom_traje }}">
                            </div>

                            <h5 class="text-[11px] font-black uppercase text-gray-700 truncate tracking-tight px-1">
                                {{ str_replace(' - Varón', '', $t->nom_traje) }}
                            </h5>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                {{ $t->danza->nom_danza ?? 'General' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Controles Deslizar --}}
            <div class="flex items-center justify-center gap-4 mt-4">
                <button @click="scrollCarrusel(-1)" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <p class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-300">Deslizar trajes</p>
                <button @click="scrollCarrusel(1)" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- BOTONERA DE CONTROL INTERACTIVO DE GÉNERO --}}
        <template x-if="tieneVariante">
            <div class="mb-6 max-w-md grid grid-cols-2 gap-2 p-1 bg-white rounded-2xl border shadow-sm">
                <button type="button" @click="switchGenero('Masculino')"
                        :class="generoActivo === 'Masculino' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
                        class="py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1">
                    ♂️ Inventario Varón
                </button>
                <button type="button" @click="switchGenero('Femenino')"
                        :class="generoActivo === 'Femenino' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
                        class="py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1">
                    ♀️ Inventario Damas
                </button>
            </div>
        </template>

        {{-- ══ 2. PANEL DE TALLAS DINÁMICO (CAMBIA SU COLOR CON EL GÉNERO) ══ --}}
        <div class="mb-10 p-6 rounded-[2rem] shadow-xl border text-white transition-all duration-500"
             :class="generoActivo === 'Femenino' ? 'bg-zinc-900 border-pink-500/30' : 'bg-andes-oscuro border-slate-800'">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div>
                        <span class="text-[8px] font-black bg-andes-verde/20 text-andes-verde border border-andes-verde/30 px-3 py-1 rounded-full uppercase tracking-widest"
                              :class="generoActivo === 'Femenino' ? 'bg-pink-500/10 text-pink-400 border-pink-500/20' : ''">
                            Disponibilidad Actual en Vitrina
                        </span>
                        <h3 class="text-xl font-black uppercase tracking-tight mt-1"
                            :class="generoActivo === 'Femenino' ? 'text-pink-400' : 'text-andes-amarillo'"
                            x-text="currentTraje.nom_traje">
                        </h3>
                    </div>

                    <a :href="'/vendedor/trajes/' + currentTraje.cod_traje + '/unidades/nueva'"
                       class="inline-flex items-center gap-2 bg-andes-verde hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest shadow-md transition-all transform active:scale-95 shrink-0 h-fit sm:mt-4">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Añadir Stock
                    </a>
                </div>

                {{-- Contadores de tallas reactivos --}}
                <div class="flex flex-wrap gap-3">
                    <template x-for="talla in ['S', 'M', 'L', 'XL', 'Personalizado']">
                        <div class="px-4 py-2 rounded-xl border flex flex-col items-center min-w-[70px] transition-all"
                             :class="countTalla(talla) > 0 ? 'bg-black/30 border-white/10' : 'bg-red-500/5 border-red-500/10 opacity-30'">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest" x-text="talla"></span>
                            <span class="text-base font-black mt-0.5" 
                                  :class="countTalla(talla) > 0 ? 'text-andes-verde' : 'text-red-400'"
                                  x-text="countTalla(talla) + ' Pz'">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ══ 3. TABLA MATRIZ DE STOCK DINÁMICA ══ --}}
        <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-gray-100 mb-10">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Nombre del Traje</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Danza / Categoría</th>
                        <th class="px-4 py-6 text-[10px] font-black uppercase text-gray-400 text-center">S</th>
                        <th class="px-4 py-6 text-[10px] font-black uppercase text-gray-400 text-center">M</th>
                        <th class="px-4 py-6 text-[10px] font-black uppercase text-gray-400 text-center">L</th>
                        <th class="px-4 py-6 text-[10px] font-black uppercase text-gray-400 text-center">XL</th>
                        <th class="px-4 py-6 text-[10px] font-black uppercase text-gray-400 text-center">Pers.</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 text-right">Precio Base</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="transition-colors" :class="currentTraje.deleted_at ? 'bg-gray-50/70 opacity-60' : ''">
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase"
                                  :class="currentTraje.deleted_at ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'"
                                  x-text="currentTraje.deleted_at ? 'Inactivo' : 'Activo'">
                            </span>
                        </td>
                        <td class="px-6 py-5 font-black text-gray-800 uppercase tracking-tight" x-text="currentTraje.nom_traje"></td>
                        <td class="px-8 py-5 text-xs font-bold text-gray-400 italic" x-text="currentTraje.danza ? currentTraje.danza.nom_danza : 'Sin Danza'"></td>
                        
                        <template x-for="t in ['S', 'M', 'L', 'XL', 'Personalizado']">
                            <td class="px-4 py-5 text-center font-bold text-sm" 
                                :class="countTalla(t) > 0 ? 'text-gray-800' : 'text-gray-300'"
                                x-text="countTalla(t)">
                            </td>
                        </template>

                        <td class="px-8 py-5 text-right font-black text-andes-verde text-sm">
                            Bs. <span x-text="currentTraje.pre_alquiler"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ══ 4. TABLA PERCHERO FÍSICO DINÁMICO ══ --}}
        @include('vendedor.unidades._perchero', ['unidades' => $trajeActivo->unidades])

    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
    </style>
</x-app-layout>
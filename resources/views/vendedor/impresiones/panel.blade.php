<x-app-layout>
    <x-slot name="header">
        Centro de Etiquetado Termosensible
    </x-slot>

    @php
        $hijo = $trajeActivo->varianteFemenina;

        $padrePayload = $trajeActivo->toArray();
        $padrePayload['unidades'] = $trajeActivo->unidades;
        $padrePayload['cant_fotos'] = $trajeActivo->imagenes()->count();

        $hijoPayload = null;
        if ($hijo) {
            $hijoPayload = $hijo->toArray();
            $hijoPayload['unidades'] = $hijo->unidades;
            $hijoPayload['cant_fotos'] = $hijo->imagenes()->count();
        }
    @endphp

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            scrollCarrusel(dir) {
                const el = document.getElementById('carrusel-trajes');
                el.scrollBy({ left: dir * 240, behavior: 'smooth' })
            },
            generoActivo: 'Masculino',
            trajePadre: {{ json_encode($padrePayload) }},
            trajeHijo: {{ json_encode($hijoPayload) }},
            currentTraje: {},
            tieneVariante: {{ $hijo ? 'true' : 'false' }},
            unidadesSeleccionadas: [],

            init() {
                this.currentTraje = this.trajePadre;
                this.seleccionarTodas();
            },

            switchGenero(genero) {
                this.generoActivo = genero;
                this.currentTraje = (genero === 'Femenino' && this.trajeHijo) ? this.trajeHijo : this.trajePadre;
                this.seleccionarTodas();
            },

            seleccionarTodas() {
                this.unidadesSeleccionadas = this.currentTraje.unidades.map(u => u.cod_unidad);
            }
         }">

        {{-- ══ 1. CARRUSEL SUPERIOR DE TRAJES (NUEVO) ══ --}}
        <div class="mb-10">
            <div class="flex justify-between items-end mb-6 ml-2">
                <div>
                    <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Navegar por el Catálogo</h4>
                    <p class="text-[10px] text-blue-600 font-bold uppercase mt-1">Selecciona una colección para configurar su lote de impresión térmica</p>
                </div>
            </div>

            {{-- Track de Desplazamiento Horizontal --}}
            <div id="carrusel-trajes" class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide scroll-smooth">
                @foreach($todosLosTrajes as $t)
                    @php
                        // Contamos el stock total del lote (Varón + Damas)
                        $stockTotal = $t->unidades->where('deleted_at', null)->count() + 
                                      ($t->varianteFemenina ? $t->varianteFemenina->unidades->where('deleted_at', null)->count() : 0);
                        
                        $primeraFoto = $t->imagenes->first()
                            ? asset('storage/' . $t->imagenes->first()->ruta_img)
                            : asset('img/default-traje.jpg');
                        $esElActivo = $t->cod_traje == $trajeActivo->cod_traje;
                    @endphp

                    <a href="{{ route('vendedor.trajes.impresion.panel', $t->cod_traje) }}"
                       class="flex-none w-52 group focus:outline-none">
                        <div class="bg-white p-5 rounded-[2.5rem] border transition-all duration-300 text-center relative shadow-sm
                                    {{ $esElActivo ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-lg scale-105' : 'border-gray-100 hover:shadow-md hover:scale-102' }}">

                            <span class="absolute top-4 right-4 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-tight bg-slate-100 text-slate-700">
                                {{ $stockTotal }} Uds
                            </span>

                            <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-2 border-gray-50 shadow-inner
                                        {{ $esElActivo ? 'border-blue-500' : 'group-hover:border-blue-500' }} transition-colors">
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
                <button @click="scrollCarrusel(-1)" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-blue-500 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <p class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-300">Deslizar colecciones</p>
                <button @click="scrollCarrusel(1)" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-blue-500 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ══ 2. SELECTOR DE GÉNERO INTERACTIVO ══ --}}
        <template x-if="tieneVariante">
            <div class="mb-6 max-w-md grid grid-cols-2 gap-2 p-1 bg-white rounded-2xl border shadow-sm">
                <button type="button" @click="switchGenero('Masculino')"
                        :class="generoActivo === 'Masculino' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
                        class="py-2.5 rounded-xl font-black text-xs uppercase transition flex items-center justify-center gap-1">
                    ♂️ Impresión Varón
                </button>
                <button type="button" @click="switchGenero('Femenino')"
                        :class="generoActivo === 'Femenino' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
                        class="py-2.5 rounded-xl font-black text-xs uppercase transition flex items-center justify-center gap-1">
                    ♀️ Impresión Damas
                </button>
            </div>
        </template>

        {{-- ══ 3. PANEL DE CONTROL OSCURO (CORREGIDO DE CARACTERES) ══ --}}
        <div class="mb-8 p-6 bg-slate-900 text-white rounded-[2rem] border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-xl">
            <div>
                <span class="text-[8px] font-black bg-andes-verde/20 text-andes-verde border border-andes-verde/30 px-3 py-1 rounded-full uppercase tracking-widest">Motor de Impresión por Lotes</span>
                <h3 class="text-xl font-black mt-2 uppercase text-andes-amarillo" x-text="currentTraje.nom_traje"></h3>
                <p class="text-xs text-slate-400 mt-1">Este traje tiene <span class="text-andes-verde font-black" x-text="currentTraje.cant_fotos"></span> fotos cargadas. Se multiplicarán automáticamente en el PDF.</p>
            </div>

            <form :action="`/vendedor/trajes/${currentTraje.cod_traje}/impresion/pdf`" method="POST" target="_blank" class="w-full md:w-auto text-right">
                @csrf
                <template x-for="id in unidadesSeleccionadas" :key="id">
                    <input type="hidden" name="unidades[]" :value="id">
                </template>

                <button type="submit" class="w-full md:w-auto bg-andes-verde hover:bg-green-600 px-6 py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg transition transform active:scale-95">
                    🖨️ Descargar Plancha PDF
                </button>
            </form>
        </div>

        {{-- ══ 4. TABLA DE FILTRADO Y SELECCIÓN DE PRENDAS FÍSICAS ══ --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px] text-center w-20">Selección</th>
                        <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Código de Barra Base</th>
                        <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Talla</th>
                        <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Condición</th>
                        <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Etiquetas del Lote</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="u in currentTraje.unidades" :key="u.cod_unidad">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 text-center">
                                <input type="checkbox" :value="u.cod_unidad" x-model="unidadesSeleccionadas"
                                       class="w-4 h-4 text-andes-verde border-gray-300 rounded focus:ring-andes-verde cursor-pointer">
                            </td>
                            <td class="px-6 py-4 font-black text-gray-900 font-mono text-sm" x-text="u.nro_serie_interno"></td>
                            <td class="px-6 py-4 font-bold text-gray-500"><span class="bg-gray-100 px-2 py-1 rounded text-[10px]" x-text="u.talla"></span></td>
                            <td class="px-6 py-4 font-black" :class="u.disponibilidad ? 'text-andes-verde' : 'text-amber-500'" x-text="u.disponibilidad ? 'Disponible' : 'Alquilado'"></td>
                            <td class="px-6 py-4 font-black text-slate-700" x-text="currentTraje.cant_fotos + ' Piezas / Stickers'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
    </style>
</x-app-layout>
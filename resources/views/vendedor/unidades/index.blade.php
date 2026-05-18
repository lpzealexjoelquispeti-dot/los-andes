<x-app-layout>
    <x-slot name="header">
        Inventario: {{ $trajeActivo->nom_traje }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            scrollCarrusel(dir) {
                const el = document.getElementById('carrusel-trajes');
                el.scrollBy({ left: dir * 240, behavior: 'smooth' })
            }
         }">

        {{-- ══ 1. CARRUSEL SUPERIOR DE TRAJES ══ --}}
        <div class="mb-10">
            <div class="flex justify-between items-end mb-6 ml-2">
                <div>
                    <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Navegar por tus Trajes</h4>
                    <p class="text-[10px] text-andes-verde font-bold uppercase mt-1">Selecciona un traje del carrusel para gestionar su stock físico</p>
                </div>

                {{-- Grupo de botones de acción --}}
                <div class="flex items-center gap-3">
                    {{-- Botón: Centro de Daños (nueva vista separada) --}}
                    <a href="{{ route('vendedor.trajes.unidades.danos', $trajeActivo->cod_traje) }}"
                       class="bg-red-600 text-white px-5 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Control de Daños
                    </a>

                    {{-- Botón: Añadir prenda física --}}
                    <a href="{{ route('vendedor.trajes.unidades.create', $trajeActivo->cod_traje) }}"
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
                        $stockTotal  = $t->unidades->where('disponibilidad', true)->where('deleted_at', null)->count();
                        $primeraFoto = $t->imagenes->first()
                            ? asset('storage/' . $t->imagenes->first()->ruta_img)
                            : asset('img/default-traje.jpg');
                        $esElActivo  = $t->cod_traje == $trajeActivo->cod_traje;
                    @endphp

                    <a href="{{ route('vendedor.trajes.unidades.index', $t->cod_traje) }}"
                       class="flex-none w-52 group focus:outline-none">
                        <div class="bg-white p-5 rounded-[2.5rem] border transition-all duration-300 text-center relative shadow-sm
                                    {{ $esElActivo
                                        ? 'border-andes-verde ring-2 ring-andes-verde/20 shadow-lg scale-105'
                                        : 'border-gray-100 hover:shadow-md hover:scale-102' }}">

                            {{-- Badge de stock --}}
                            <span class="absolute top-4 right-4 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-tight
                                         {{ $stockTotal > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                {{ $stockTotal }} Uds
                            </span>

                            <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-2 border-gray-50 shadow-inner
                                        {{ $esElActivo ? 'border-andes-verde' : 'group-hover:border-andes-verde' }} transition-colors">
                                <img src="{{ $primeraFoto }}" class="w-full h-full object-cover" alt="{{ $t->nom_traje }}">
                            </div>

                            <h5 class="text-[11px] font-black uppercase text-gray-700 truncate tracking-tight px-1">
                                {{ $t->nom_traje }}
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
                <button @click="scrollCarrusel(-1)"
                        class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <p class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-300">Deslizar trajes</p>
                <button @click="scrollCarrusel(1)"
                        class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:border-andes-verde transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ══ 2. PANEL DE TALLAS DEL TRAJE SELECCIONADO ══ --}}
        @php
            $tallasMapa = [
                'S'             => $trajeActivo->unidades->where('talla', 'S')->where('disponibilidad', true)->where('deleted_at', null)->count(),
                'M'             => $trajeActivo->unidades->where('talla', 'M')->where('disponibilidad', true)->where('deleted_at', null)->count(),
                'L'             => $trajeActivo->unidades->where('talla', 'L')->where('disponibilidad', true)->where('deleted_at', null)->count(),
                'XL'            => $trajeActivo->unidades->where('talla', 'XL')->where('disponibilidad', true)->where('deleted_at', null)->count(),
                'Personalizado' => $trajeActivo->unidades->where('talla', 'Personalizado')->where('disponibilidad', true)->where('deleted_at', null)->count(),
            ];
        @endphp

        <div class="mb-10 bg-andes-oscuro text-white p-6 rounded-[2rem] shadow-xl border border-slate-800">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div>
                        <span class="text-[8px] font-black bg-andes-verde/20 text-andes-verde border border-andes-verde/30 px-3 py-1 rounded-full uppercase tracking-widest">
                            Disponibilidad Actual en Tienda
                        </span>
                        <h3 class="text-xl font-black uppercase tracking-tight mt-1 text-andes-amarillo">
                            {{ $trajeActivo->nom_traje }}
                        </h3>
                    </div>

                    <a href="{{ route('vendedor.trajes.unidades.create', $trajeActivo->cod_traje) }}"
                       class="inline-flex items-center gap-2 bg-andes-verde hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest shadow-md transition-all transform active:scale-95 shrink-0 h-fit sm:mt-4">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir Stock
                    </a>
                </div>

                {{-- Badges de tallas --}}
                <div class="flex flex-wrap gap-3">
                    @foreach($tallasMapa as $talla => $cantidad)
                        <div class="px-4 py-2 rounded-xl border flex flex-col items-center min-w-[70px] transition
                                    {{ $cantidad > 0 ? 'bg-slate-800/50 border-slate-700' : 'bg-red-500/10 border-red-500/20 opacity-40' }}">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $talla }}</span>
                            <span class="text-base font-black mt-0.5 {{ $cantidad > 0 ? 'text-andes-verde' : 'text-red-400' }}">
                                {{ $cantidad }} Pz
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ 3. TABLA MATRIZ DE STOCK ══ --}}
        <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-gray-100">
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
                    @php $isTrashed = $trajeActivo->trashed(); @endphp
                    <tr class="{{ $isTrashed ? 'bg-gray-50/70 opacity-60' : '' }} transition-colors">

                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase
                                         {{ $isTrashed ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                                {{ $isTrashed ? 'Inactivo' : 'Activo' }}
                            </span>
                        </td>

                        <td class="px-6 py-5 font-black text-gray-800 uppercase tracking-tight">
                            {{ $trajeActivo->nom_traje }}
                        </td>

                        <td class="px-8 py-5 text-xs font-bold text-gray-400 italic">
                            {{ $trajeActivo->danza->nom_danza ?? 'Sin Danza' }}
                        </td>

                        @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $t)
                            @php
                                $count = $trajeActivo->unidades
                                    ->where('talla', $t)
                                    ->where('disponibilidad', true)
                                    ->where('deleted_at', null)
                                    ->count();
                            @endphp
                            <td class="px-4 py-5 text-center font-bold text-sm {{ $count > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                                {{ $count }}
                            </td>
                        @endforeach

                        <td class="px-8 py-5 text-right font-black text-andes-verde text-sm">
                            Bs. {{ $trajeActivo->pre_alquiler }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ══ 4. TABLA PERCHERO FÍSICO (partial reutilizable) ══ --}}
        @include('vendedor.unidades._perchero', ['unidades' => $trajeActivo->unidades])

    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
    </style>
</x-app-layout>
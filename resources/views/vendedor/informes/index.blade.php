<x-app-layout>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        {{-- CABECERA ENLAZADA A SU DISEÑO --}}
        <div class="mb-8 flex items-center justify-between border-b pb-6">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900">
                    Módulo de Analítica Comercial
                </h1>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-1">
                    Panel exclusivo para: <span class="font-black text-gray-700">{{ $tienda->nom_tie }}</span>
                </p>
            </div>
            <div class="px-4 py-2 rounded-full text-white font-black text-xs uppercase shadow" 
                 style="background-color: {{ $colorTienda }}">
                ✨ Datos en tiempo real
            </div>
        </div>

        {{-- GRID DE TARJETAS INFORMATIVAS (KPIs) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            
            {{-- KPI 1: Salud de Colecciones --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Variantes de Pareja</span>
                    <h3 class="text-4xl font-black text-gray-800 mt-2">
                        {{ $trajesConVariante }} <span class="text-xl text-gray-300">/ {{ $totalTrajesMaestros }}</span>
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-medium mt-4">
                    Trajes maestros con opción de catálogo integrado Damas/Varón.
                </p>
            </div>

            {{-- KPI 2: Prendas Bloqueadas / Mantenimiento --}}
            @php 
                $mantenimiento = $estadoInventario->firstWhere('estado_unidad', 'Mantenimiento')->total ?? 0;
                $disponibles = $estadoInventario->firstWhere('estado_unidad', 'Disponible')->total ?? 0;
            @endphp
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-400">Riesgo en Taller</span>
                    <h3 class="text-4xl font-black text-red-600 mt-2">
                        {{ $mantenimiento }} <span class="text-xl text-gray-300">piezas</span>
                    </h3>
                </div>
                <p class="text-xs text-red-400/80 font-bold uppercase tracking-wider mt-4">
                    ⚠️ Stock fuera de circulación por daños.
                </p>
            </div>

            {{-- KPI 3: Stock de Ataque --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-green-400">Listo para Alquilar</span>
                    <h3 class="text-4xl font-black text-green-600 mt-2">
                        {{ $disponibles }} <span class="text-xl text-gray-300">unidades</span>
                    </h3>
                </div>
                <p class="text-xs text-gray-400 font-medium mt-4">
                    Prendas limpias y disponibles en percheros para entrega inmediata.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- TABLA: TOP REPETICIÓN DE STOCK (MAYOR VOLUMEN INVENTARIO) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 mb-6">
                    🚀 Enfoque de Producción (Mayor Stock Físico)
                </h3>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-100">
                        @foreach($trajesPopulares as $item)
                            <li class="py-4 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 uppercase text-sm">{{ $item->nom_traje }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Danza: {{ $item->danza->nom_danza ?? 'General' }}</span>
                                </div>
                                <span class="px-4 py-2 rounded-xl font-black text-xs uppercase"
                                      style="background-color: {{ $colorTienda }}20; color: {{ $colorTienda }}">
                                    {{ $item->unidades_count }} Piezas
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- GRAFICO/RESUMEN OPERATIVO DEL PERCHERO --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 mb-2">
                        📊 Situación de los Percheros
                    </h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-6">Desglose de unidades físicas</p>
                    
                    <div class="space-y-4">
                        @foreach($estadoInventario as $est)
                            <div>
                                <div class="flex justify-between text-xs font-black uppercase mb-1 text-gray-600">
                                    <span>{{ $est->estado_unidad }}</span>
                                    <span>{{ $est->total }} un.</span>
                                </div>
                                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" 
                                         style="width: {{ ($est->total / max($disponibles + $mantenimiento, 1)) * 100 }}%; 
                                                background-color: {{ $est->estado_unidad === 'Disponible' ? '#16a34a' : ($est->estado_unidad === 'Mantenimiento' ? '#dc2626' : '#9ca3af') }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-50 text-[11px] text-gray-400 font-bold uppercase text-center tracking-widest">
                    💡 Consejo: Si "Mantenimiento" supera el 15%, urge acelerar las reparaciones con el sastre.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
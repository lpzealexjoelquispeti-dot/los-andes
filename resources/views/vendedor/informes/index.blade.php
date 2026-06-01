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
                $mantenimiento = $estadoInventario->filter(fn ($item) => str_contains($item->estado_unidad, 'Repar'))->sum('total');
                $disponibles = $unidadesDisponibles ?? 0;
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

        {{-- SECCIÓN CENTRAL DE REPORTES GRÁFICOS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            
            {{-- TABLA: TOP REPETICIÓN DE STOCK (MAYOR VOLUMEN INVENTARIO) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 mb-6">
                    🚀 Enfoque de Production (Mayor Stock Físico)
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
                                                background-color: {{ in_array($est->estado_unidad, ['Nuevo', 'Buen Estado'], true) ? '#16a34a' : (str_contains($est->estado_unidad, 'Repar') ? '#dc2626' : '#9ca3af') }}">
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

        {{-- ── NUEVA SECCIÓN: BUSCADOR PARAMETRIZADO E INVENTARIO DETALLADO ── --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm mb-12">
            <div class="mb-6">
                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800">
                    🔍 Buscador Parametrizado de Inventario
                </h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">
                    Filtra y audita el perchero en tiempo real
                </p>
            </div>

            {{-- FORMULARIO DE FILTROS --}}
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-gray-50 p-5 rounded-3xl">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Nombre del Traje</label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Ej: Rey Moreno..." 
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 placeholder-gray-300 font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Filtrar por Danza</label>
                    <select name="danza" class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todas las Danzas --</option>
                        @foreach($danzasDisponibles ?? [] as $danza)
                            <option value="{{ $danza->cod_danza }}" {{ request('danza') == $danza->cod_danza ? 'selected' : '' }}>
                                {{ $danza->nom_danza }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Estado Físico</label>
                    <select name="estado" class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todos los Estados --</option>
                        <option value="Nuevo" {{ request('estado') === 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                        <option value="Buen Estado" {{ request('estado') === 'Buen Estado' ? 'selected' : '' }}>Buen Estado</option>
                        <option value="Desgastado" {{ request('estado') === 'Desgastado' ? 'selected' : '' }}>Desgastado</option>
                        <option value="En Reparación" {{ request('estado') === 'En Reparación' ? 'selected' : '' }}>En Reparación</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full text-white text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl shadow transition duration-150 hover:opacity-90"
                            style="background-color: {{ $colorTienda }}">
                        Filtrar Perchero
                    </button>
                    @if(request()->hasAny(['busqueda', 'danza', 'estado']))
                        <a href="{{ url()->current() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-600 text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl transition duration-150 text-center">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- REPOSITORIO DE RESULTADOS --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="pb-3 pl-2">Código Barra / ID</th>
                            <th class="pb-3">Prenda Coreográfica</th>
                            <th class="pb-3">Talla</th>
                            <th class="pb-3">Estado Operativo</th>
                            <th class="pb-3 text-right pr-2">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                        @forelse($unidadesFiltradas ?? [] as $unidad)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 pl-2 font-mono text-xs text-gray-400">
                                    #{{ $unidad->cod_unidad }}
                                </td>
                                <td class="py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900">{{ $unidad->traje->nom_traje }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">
                                            {{ $unidad->traje->danza->nom_danza ?? 'Folklore General' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-black rounded-md uppercase">
                                        {{ $unidad->talla ?? 'U' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-tight
                                        {{ $unidad->disponibilidad ? 'bg-green-50 text-green-700' : (str_contains($unidad->estado_fisico, 'Repar') ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $unidad->disponibilidad ? 'bg-green-600' : (str_contains($unidad->estado_fisico, 'Repar') ? 'bg-red-600' : 'bg-gray-500') }}"></span>
                                        {{ $unidad->estado_fisico }}
                                    </span>
                                </td>
                                <td class="py-4 text-right pr-2">
                                    <a href="{{ route('vendedor.unidades.edit', $unidad->cod_unidad) }}" 
                                       class="text-xs font-black uppercase tracking-wider hover:underline"
                                       style="color: {{ $colorTienda }}">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 font-bold uppercase text-xs tracking-wider">
                                    ❌ No se encontraron unidades físicas en el perchero con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if(isset($unidadesFiltradas) && method_exists($unidadesFiltradas, 'links'))
                <div class="mt-6 border-t pt-4">
                    {{ $unidadesFiltradas->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>

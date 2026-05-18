<x-app-layout>
    <x-slot name="header">Reportes del Tesauro</x-slot>

    <div class="max-w-7xl mx-auto py-10 px-6">

        {{-- ENCABEZADO --}}
        <div class="flex justify-between items-start mb-10">
            <div>
                <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tighter italic">Inteligencia de Búsqueda</h2>
                <p class="text-andes-verde text-[10px] font-black uppercase tracking-[0.3em]">Análisis Estadístico del Tesauro</p>
            </div>
            {{-- BOTONES DE EXPORTACIÓN --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.reportes.tesauro.excel', request()->query()) }}"
                   class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('admin.reportes.tesauro.pdf', request()->query()) }}"
                   class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- FILTROS --}}
        <form method="GET" action="{{ route('admin.reportes.tesauro') }}"
              class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ $fechaDesde }}"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium
                                  focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all"/>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium
                                  focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all"/>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tipo de Resultado</label>
                    <select name="tipo"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all appearance-none">
                        <option value="">Todos</option>
                        <option value="tesauro"       {{ $tipoFiltro == 'tesauro'       ? 'selected' : '' }}>Tesauro</option>
                        <option value="danza"         {{ $tipoFiltro == 'danza'         ? 'selected' : '' }}>Danza Directa</option>
                        <option value="texto"         {{ $tipoFiltro == 'texto'         ? 'selected' : '' }}>Texto Libre</option>
                        <option value="sin_resultado" {{ $tipoFiltro == 'sin_resultado' ? 'selected' : '' }}>Sin Resultado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Danza</label>
                    <select name="danza_id"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all appearance-none">
                        <option value="">Todas</option>
                        @foreach($danzas as $danza)
                            <option value="{{ $danza->cod_danza }}" {{ $danzaFiltro == $danza->cod_danza ? 'selected' : '' }}>
                                {{ $danza->nom_danza }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="flex justify-end mt-4 gap-3">
                <a href="{{ route('admin.reportes.tesauro') }}"
                   class="px-6 py-3 rounded-2xl border border-gray-200 text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all">
                    Limpiar
                </a>
                <button type="submit"
                        class="bg-andes-verde text-white px-8 py-3 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all">
                    Aplicar Filtros
                </button>
            </div>
        </form>

        {{-- TARJETAS DE ESTADÍSTICAS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Búsquedas</p>
                <p class="text-4xl font-black text-gray-800">{{ number_format($totalBusquedas) }}</p>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Vía Tesauro</p>
                <p class="text-4xl font-black text-andes-verde">{{ number_format($busquedasTesauro) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ $totalBusquedas > 0 ? round($busquedasTesauro / $totalBusquedas * 100) : 0 }}% del total
                </p>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Danza Directa</p>
                <p class="text-4xl font-black text-blue-500">{{ number_format($busquedasDanza) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ $totalBusquedas > 0 ? round($busquedasDanza / $totalBusquedas * 100) : 0 }}% del total
                </p>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Sin Resultado</p>
                <p class="text-4xl font-black text-red-500">{{ number_format($sinResultado) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Términos a agregar</p>
            </div>

        </div>

        {{-- GRÁFICAS FILA 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Gráfica de línea: Actividad diaria --}}
            <div class="lg:col-span-2 bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Actividad de Búsqueda Diaria</h3>
                <canvas id="actividadChart" height="100"></canvas>
            </div>

            {{-- Gráfica de dona: Distribución por tipo --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Distribución por Tipo</h3>
                <canvas id="distribucionChart"></canvas>
            </div>

        </div>

        {{-- GRÁFICAS FILA 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- Top términos --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Top 10 Términos Más Buscados</h3>
                <canvas id="topTerminosChart" height="200"></canvas>
            </div>

            {{-- Top danzas --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Top Danzas Más Buscadas</h3>
                <canvas id="topDanzasChart" height="200"></canvas>
            </div>

        </div>

        {{-- TABLAS FILA 3 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- Tesauro más usado --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tesauro Más Usado</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase text-gray-400">#</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase text-gray-400">Término</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase text-gray-400">Tipo</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase text-gray-400">Danza</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black uppercase text-gray-400">Búsquedas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tesauroMasUsado as $i => $termino)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-4 text-xs font-black text-gray-300">{{ $i + 1 }}</td>
                            <td class="px-8 py-4 font-bold text-gray-800 uppercase text-sm">{{ $termino->termino_usuario }}</td>
                            <td class="px-8 py-4">
                                <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase
                                    {{ $termino->tipo === 'sinonimo' ? 'bg-blue-100 text-blue-600' : ($termino->tipo === 'ortografia' ? 'bg-yellow-100 text-yellow-600' : 'bg-purple-100 text-purple-600') }}">
                                    {{ $termino->tipo }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-xs text-gray-400 uppercase">{{ $termino->danza->nom_danza ?? '—' }}</td>
                            <td class="px-8 py-4 text-right font-black text-andes-verde">{{ $termino->veces_buscado }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Términos sin resultado --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Términos Sin Resultado</h3>
                    <span class="text-[9px] font-black uppercase bg-red-100 text-red-600 px-3 py-1 rounded-full">Agregar al Tesauro</span>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase text-gray-400">Término</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black uppercase text-gray-400">Veces</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black uppercase text-gray-400">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($terminosSinResultado as $termino)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-4 font-bold text-gray-800 uppercase text-sm">{{ $termino->termino_buscado }}</td>
                            <td class="px-8 py-4 text-right font-black text-red-500">{{ $termino->total }}</td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('admin.tesauro.create', ['termino' => $termino->termino_buscado]) }}"
                                   class="text-[9px] font-black uppercase text-andes-verde hover:underline">
                                    + Agregar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-8 text-center text-gray-300 text-xs font-bold uppercase">
                                Sin términos sin resultado en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos desde el controlador
        const actividadLabels = @json($actividadDiaria->pluck('fecha'));
        const actividadData   = @json($actividadDiaria->pluck('total'));

        const distribucionLabels = ['Tesauro', 'Danza', 'Texto Libre', 'Sin Resultado'];
        const distribucionData   = @json(array_values($distribucionTipo));

        const topTerminosLabels = @json($topTerminos->pluck('termino_buscado'));
        const topTerminosData   = @json($topTerminos->pluck('total'));

        const topDanzasLabels = @json($topDanzas->map(fn($d) => $d->danza->nom_danza ?? 'N/A'));
        const topDanzasData   = @json($topDanzas->pluck('total'));

        const verde   = '#007a3d';
        const azul    = '#3b82f6';
        const amarillo = '#f59e0b';
        const rojo    = '#ef4444';

        // ── Actividad diaria (línea) ──
        new Chart(document.getElementById('actividadChart'), {
            type: 'line',
            data: {
                labels: actividadLabels,
                datasets: [{
                    label: 'Búsquedas',
                    data: actividadData,
                    borderColor: verde,
                    backgroundColor: verde + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: verde,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ── Distribución por tipo (dona) ──
        new Chart(document.getElementById('distribucionChart'), {
            type: 'doughnut',
            data: {
                labels: distribucionLabels,
                datasets: [{
                    data: distribucionData,
                    backgroundColor: [verde, azul, amarillo, rojo],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10, weight: 'bold' }, padding: 15 }
                    }
                }
            }
        });

        // ── Top términos (barras horizontales) ──
        new Chart(document.getElementById('topTerminosChart'), {
            type: 'bar',
            data: {
                labels: topTerminosLabels,
                datasets: [{
                    label: 'Búsquedas',
                    data: topTerminosData,
                    backgroundColor: verde + 'cc',
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    y: { grid: { display: false } }
                }
            }
        });

        // ── Top danzas (barras) ──
        new Chart(document.getElementById('topDanzasChart'), {
            type: 'bar',
            data: {
                labels: topDanzasLabels,
                datasets: [{
                    label: 'Búsquedas',
                    data: topDanzasData,
                    backgroundColor: azul + 'cc',
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    y: { grid: { display: false } }
                }
            }
        });
    </script>

</x-app-layout>
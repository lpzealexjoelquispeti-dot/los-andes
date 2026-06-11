<x-app-layout>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-6 gap-4">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-9-9a9 9 0 0118 0c0 7-9 13-9 13S6 18 6 11a9 9 0 01 0 0z"/>
                    </svg>
                    Trajes Más Gastados
                </h1>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-1">
                    Reporte parametrizado por puesta (1ra/2da/3ra+) y nivel acumulado
                </p>
            </div>

            <a href="{{ route('vendedor.reportes.trajes_mas_gastados.pdf', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </a>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Top</span>
                    <h3 class="text-4xl font-black text-gray-900 mt-2">{{ $top }}</h3>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">
                    Trajes mostrados
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">1ra</span>
                <h3 class="text-4xl font-black text-emerald-600 mt-2">{{ $puestaCounts['1ra'] }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">nivel = 0</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest text-sky-500">2da</span>
                <h3 class="text-4xl font-black text-sky-600 mt-2">{{ $puestaCounts['2da'] }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">nivel 1..3</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest text-amber-500">3ra+</span>
                <h3 class="text-4xl font-black text-amber-600 mt-2">{{ $puestaCounts['3ra+'] }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase mt-4">nivel ≥ 4</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Tienda</label>
                    <select name="cod_tienda" class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="">-- Todas mis tiendas --</option>
                        @foreach($tiendas as $t)
                            <option value="{{ $t->cod_tienda }}" {{ ($filtros['cod_tienda'] ?? '') == $t->cod_tienda ? 'selected' : '' }}>
                                {{ $t->nom_tie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Desde</label>
                    <input type="date" name="fec_desde" value="{{ $filtros['fec_desde'] ?? $fechaDesde }}"
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 text-gray-700 font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Hasta</label>
                    <input type="date" name="fec_hasta" value="{{ $filtros['fec_hasta'] ?? $fechaHasta }}"
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 text-gray-700 font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Top</label>
                    <input type="number" name="top" value="{{ $top }}" min="1" max="50"
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 text-gray-700 font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Puesta mínima</label>
                    <select name="puesta_min" class="w-full rounded-xl border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-medium text-gray-700">
                        <option value="all" {{ ($puestaMin ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
                        <option value="1" {{ ($puestaMin ?? '') === '1' ? 'selected' : '' }}>1ra</option>
                        <option value="2" {{ ($puestaMin ?? '') === '2' ? 'selected' : '' }}>2da</option>
                        <option value="3" {{ ($puestaMin ?? '') === '3' ? 'selected' : '' }}>3ra+</option>
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex items-end gap-2">
                    <button type="submit" class="flex-1 text-white text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl shadow bg-gray-900 hover:bg-gray-800 transition duration-150">
                        Filtrar
                    </button>
                    <a href="{{ url()->current() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-600 text-xs font-black uppercase tracking-wider py-3 px-4 rounded-xl transition duration-150 text-center">
                        Limpiar
                    </a>
                </div>

            </form>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Distribución por Puesta (en Top)
                </h3>
                <div class="relative min-h-[220px] flex items-center justify-center">
                    <canvas id="chartPuesta"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Top por Nivel de Uso
                </h3>
                <div class="relative min-h-[220px] flex items-center justify-center">
                    <canvas id="chartTop"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm overflow-x-auto">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-md font-black uppercase tracking-wider text-gray-800">
                    Ranking de Trajes
                </h2>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                    Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                </p>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">
                    <th class="pb-3 pl-2">#</th>
                    <th class="pb-3">Traje</th>
                    <th class="pb-3">Danza</th>
                    <th class="pb-3">Puesta</th>
                    <th class="pb-3 text-right pr-2">Nivel</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                @foreach($trajes as $i => $t)
                    @php
                        $nivel = (int) ($t->nivel_uso_alquileres ?? 0);
                        $puesta = $nivel === 0 ? '1ra' : ($nivel >= 1 && $nivel <= 3 ? '2da' : '3ra+');
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 pl-2 font-mono text-xs text-gray-400">#{{ $i+1 }}</td>
                        <td class="py-4 font-bold">{{ $t->nom_traje }}</td>
                        <td class="py-4 text-gray-500">{{ $t->danza->nom_danza ?? '—' }}</td>
                        <td class="py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-tight border {{ $puesta === '1ra' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($puesta === '2da' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                {{ $puesta }}
                            </span>
                        </td>
                        <td class="py-4 text-right pr-2 font-black text-gray-900">{{ $nivel }}</td>
                    </tr>
                @endforeach
                @if($trajes->isEmpty())
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 font-bold uppercase text-xs">No hay datos</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labelsPuesta = ['1ra','2da','3ra+'];
            const dataPuesta = [
                {{ (int)$puestaCounts['1ra'] }},
                {{ (int)$puestaCounts['2da'] }},
                {{ (int)$puestaCounts['3ra+'] }},
            ];

            const ctxPuesta = document.getElementById('chartPuesta').getContext('2d');
            new Chart(ctxPuesta, {
                type: 'doughnut',
                data: {
                    labels: labelsPuesta,
                    datasets: [{
                        data: dataPuesta,
                        backgroundColor: ['#10b981', '#0284c7', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });

            const labelsTop = @json($trajes->map(fn($t)=> $t->nom_traje)->values());
            const dataTop = @json($trajes->map(fn($t)=> (int)($t->nivel_uso_alquileres ?? 0))->values());

            const ctxTop = document.getElementById('chartTop').getContext('2d');
            new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: labelsTop,
                    datasets: [{
                        label: 'Nivel uso',
                        data: dataTop,
                        backgroundColor: '#4f46e5',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#4b5563', font: { size: 9, weight: 'bold' } }, grid: { display: false } },
                        y: { ticks: { color: '#4b5563' }, grid: { color: '#f3f4f6' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>


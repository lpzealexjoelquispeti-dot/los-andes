<x-app-layout>
    <x-slot name="header">
        Control de Daños: {{ str_replace(' - Varón', '', $trajeActivo->nom_traje) }}
    </x-slot>

    {{-- Calculamos el prefijo de la danza antes de iniciar el HTML --}}
    @php
        $danzaNombre = $trajeActivo->danza->nom_danza ?? 'TJ';
        $prefijoDanza = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));
        $hijo = $trajeActivo->varianteFemenina;

        // Combinamos todas las unidades activas del bloque (Padre + Hijo) para alimentar la memoria de búsqueda de Alpine
        $unidadesPadre = $trajeActivo->unidades()->get();
        $unidadesHijo = $hijo ? $hijo->unidades()->get() : collect();
        $unidadesTotales = $unidadesPadre->merge($unidadesHijo);
    @endphp

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            searchTalla: 'M',
            searchGenero: 'M', {{-- Registra el género en el buscador híbrido: M = Varón, F = Damas --}}
            searchExt: '',
            unidadesActivas: {{ json_encode($unidadesTotales) }},
            todasDanadas: {{ json_encode($unidadesDanadas) }},
            modalDanoOpen: false,
            unidadEditar: { id: '', serial: '', estado: '', observaciones: '' },
            prefijo: '{{ $prefijoDanza }}',
            tieneVariante: {{ $hijo ? 'true' : 'false' }},

            abrirMantenimiento(id, serial, estado, obs) {
                this.unidadEditar = { id: id, serial: serial, estado: estado, observaciones: obs || '' };
                this.modalDanoOpen = true;
            },

            filtrarPrendaFisica() {
                if (!this.searchExt) { Swal.fire({ icon: 'warning', text: 'Por favor, escribe un numero de extension.' }); return; }

                let termino = this.searchExt.trim().toUpperCase();
                let encontrada = null;

                // CASO 1: Si ingresó un código completo o lo escaneó con pistola lectora (Ej: MOR-15-M-XL-01)
                if (termino.includes('-')) {
                    encontrada = this.unidadesActivas.find(u => u.nro_serie_interno === termino) ||
                                 this.todasDanadas.find(u => u.nro_serie_interno === termino);
                } 
                // CASO 2: El método rápido manual de la tienda (Talla + Género + Extensión)
                else {
                    let extPad = termino.padStart(2, '0'); // Convierte '1' en '01'
                    
                    // BÚSQUEDA BINARIA MULTI-PROPIEDAD:
                    encontrada = this.unidadesActivas.find(u => 
                        u.talla.toUpperCase() === this.searchTalla.toUpperCase() && 
                        u.nro_serie_interno.includes('-' + this.searchGenero + '-') &&
                        u.nro_serie_interno.endsWith('-' + extPad)
                    ) || this.todasDanadas.find(u => 
                        u.talla.toUpperCase() === this.searchTalla.toUpperCase() && 
                        u.nro_serie_interno.includes('-' + this.searchGenero + '-') &&
                        u.nro_serie_interno.endsWith('-' + extPad)
                    );
                }

                if (encontrada) {
                    this.abrirMantenimiento(
                        encontrada.cod_unidad, 
                        encontrada.nro_serie_interno, 
                        encontrada.estado_fisico, 
                        encontrada.observaciones
                    );
                    this.searchExt = '';
                } else {
                    Swal.fire({ icon: 'warning', text: 'No existe ninguna prenda fisica en el sistema con esa combinacion.' });
                }
            }
         }">

        {{-- ══ HEADER DE SECCIÓN ══ --}}
        <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                {{-- Breadcrumb de regreso --}}
                <a href="{{ route('vendedor.trajes.unidades.index', $trajeActivo->cod_traje) }}"
                   class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase text-gray-400 hover:text-andes-verde tracking-widest transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver al Inventario
                </a>
                <h4 class="text-[11px] font-black uppercase text-red-400 tracking-[0.3em] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    Control de Daños y Reporte de Mermas
                </h4>
                <h2 class="text-2xl font-black uppercase text-gray-800 mt-1">
                    {{ str_replace(' - Varón', '', $trajeActivo->nom_traje) }}
                </h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                    Prefijo del Lote: <span class="text-andes-verde font-black">[{{ $prefijoDanza }}]</span> • Filtro inteligente integrado para código de barras
                </p>
            </div>

            {{-- FORMULARIO DE BÚSQUEDA HÍBRIDO AVANZADO --}}
            <div class="flex flex-wrap items-center gap-3 bg-gray-50 p-2.5 rounded-2xl border w-full lg:w-auto">
                
                {{-- Mini Selector de Género para la búsqueda rápida manual --}}
                <template x-if="tieneVariante">
                    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border text-[10px] font-black uppercase">
                        <button type="button" @click="searchGenero = 'M'"
                                :class="searchGenero === 'M' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                                class="px-2 py-1 rounded-lg transition">♂️ Varón</button>
                        <button type="button" @click="searchGenero = 'F'"
                                :class="searchGenero === 'F' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                                class="px-2 py-1 rounded-lg transition">♀️ Damas</button>
                    </div>
                </template>

                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400">Talla:</span>
                <select x-model="searchTalla"
                        class="px-3 py-1.5 text-xs font-black rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:border-andes-verde">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="Personalizado">Pers.</option>
                </select>

                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400">Nro Prenda:</span>
                <input type="text" x-model="searchExt" placeholder="01"
                       @keydown.enter="filtrarPrendaFisica()"
                       class="w-24 px-3 py-1.5 text-xs font-black rounded-xl border border-gray-100 text-gray-800 focus:outline-none focus:border-andes-verde">

                <button type="button" @click="filtrarPrendaFisica()"
                        class="bg-andes-oscuro text-white text-[9px] font-black uppercase tracking-widest px-4 py-2 rounded-xl hover:bg-black transition">
                    Buscar
                </button>
            </div>
        </div>

        {{-- ══ CONTENIDO PRINCIPAL: TABLA + TIP ══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Tabla de prendas dañadas --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 p-6 shadow-sm">
                <h5 class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-4">
                    Prendas en Reparación / Desgastadas actualmente (Este Bloque Colectivo)
                </h5>
                <div class="overflow-hidden rounded-2xl border border-gray-50">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Código Físico</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Bloque</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Talla</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Condición</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Reporte de Daños</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px] text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            {{-- RENDERIZADO DIRECTO DESDE EL SERVIDOR DE POSTGRESQL PARA EVITAR CACHÉ --}}
                            @forelse($unidadesDanadas as $ud)
                                <tr class="hover:bg-red-50/10 transition-colors">
                                    <td class="px-6 py-4 font-black text-gray-800 font-mono text-sm tracking-tight">{{ $ud->nro_serie_interno }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded shadow-sm {{ str_contains($ud->nro_serie_interno, '-F-') ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ str_contains($ud->nro_serie_interno, '-F-') ? '💃 Damas' : '🤵 Varón' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-500">{{ $ud->talla }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase
                                                     {{ $ud->estado_fisico == 'En Reparación' ? 'bg-amber-100 text-amber-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $ud->estado_fisico }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-500 italic truncate max-w-[180px]"
                                        title="{{ $ud->observaciones }}">
                                        {{ $ud->observaciones ?? 'Sin especificar detalles del daño.' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button"
                                                @click="abrirMantenimiento('{{ $ud->cod_unidad }}', '{{ $ud->nro_serie_interno }}', '{{ $ud->estado_fisico }}', '{{ addslashes($ud->observaciones) }}')"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-andes-oscuro hover:text-white transition rounded-xl font-black uppercase text-[9px] tracking-tight shadow-sm">
                                            Sanar Prenda
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium italic">
                                        🎉 ¡Excelente! No tienes ninguna prenda reportada con daños en esta fraternidad.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Protocolo / Tip técnico --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-[2.5rem] p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h6 class="text-xs font-black uppercase tracking-widest text-andes-amarillo">Protocolo de Taller</h6>
                    <p class="text-[11px] text-slate-400 leading-relaxed mt-2 italic">
                        Cuando una prenda física es marcada como <strong class="text-white">"Desgastada"</strong> o
                        <strong class="text-white">"En Reparación"</strong>, el sistema la retira de la vitrina pública 
                        de alquileres de forma automatizada. Al concluir la costura o compostura, cámbiala a "Buen Estado" para habilitar su reserva.
                    </p>
                </div>
                <div class="text-[9px] text-slate-500 font-bold border-t border-slate-800/60 pt-4 uppercase tracking-tighter">
                    Lector de Código de barras indexado a PostgreSQL
                </div>
            </div>
        </div>

        {{-- ══ MODAL: FICHA DE HOSPITAL / TALLER ══ --}}
        <div x-show="modalDanoOpen"
             x-transition.opacity
             class="fixed inset-0 z-[110] flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
             x-cloak>
            <div class="bg-white w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl border border-gray-100 p-8"
                 @click.away="modalDanoOpen = false">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="text-[9px] font-black uppercase text-andes-verde tracking-widest">Ficha de Hospital / Taller</h4>
                        <h3 class="text-lg font-black text-gray-800 uppercase mt-0.5 font-mono tracking-tight text-andes-oscuro" x-text="unidadEditar.serial"></h3>
                    </div>
                    <button type="button" @click="modalDanoOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Sincronizado al endpoint resource del controlador --}}
                <form :action="'/vendedor/unidades/' + unidadEditar.id" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5">
                            Nueva Condición Física
                        </label>
                        {{-- ENORME CORRECCIÓN AQUÍ: Eliminado el name duplicado. Ahora solo viaja estado_fisico --}}
                        <select name="estado_fisico" x-model="unidadEditar.estado"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs uppercase font-black text-gray-700 tracking-wider focus:bg-white focus:border-andes-verde focus:outline-none">
                            <option value="Nuevo">✨ Re-Confeccionado / Como Nuevo</option>
                            <option value="Buen Estado">👍 Sano / Buen Estado (Devolver al Catálogo)</option>
                            <option value="Desgastado">⚠️ Desgastado (Mantiene detalles)</option>
                            <option value="En Reparación">🛠️ En Reparación (Se queda en Taller)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5">
                            Diagnóstico de Daños
                        </label>
                        <textarea name="observaciones" x-model="unidadEditar.observaciones" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-gray-700 focus:bg-white focus:border-andes-verde focus:outline-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-andes-oscuro hover:bg-black text-white py-3.5 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg transition">
                        Actualizar Historial de Taller
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

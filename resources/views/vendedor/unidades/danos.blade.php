<x-app-layout>
    <x-slot name="header">
        Control de Daños: {{ $trajeActivo->nom_traje }}
    </x-slot>

    {{-- Calculamos el prefijo real de la danza antes de iniciar el HTML --}}
    @php
        $danzaNombre = $trajeActivo->danza->nom_danza ?? 'TJ';
        $prefijoDanza = strtoupper(substr(str_replace(' ', '', $danzaNombre), 0, 3));
    @endphp

    <div class="max-w-7xl mx-auto py-10 px-6"
         x-data="{
            searchTalla: 'M',
            searchExt: '',
            unidadesActivas: {{ json_encode($trajeActivo->unidades) }},
            todasDanadas: {{ json_encode($unidadesDanadas) }},
            modalDanoOpen: false,
            unidadEditar: { id: '', serial: '', estado: '', observaciones: '' },
            prefijo: '{{ $prefijoDanza }}', {{-- Le pasamos el prefijo real de la danza a Alpine --}}

            abrirMantenimiento(id, serial, estado, obs) {
                this.unidadEditar = { id: id, serial: serial, estado: estado, observaciones: obs || '' };
                this.modalDanoOpen = true;
            },

            filtrarPrendaFisica() {
    if (!this.searchExt) { alert('Por favor, escribe un número de extensión.'); return; }

    let termino = this.searchExt.trim().toUpperCase();
    let encontrada = null;

    // CASO 1: Si ingresó un guion '-', asumimos que escribió o escaneó el código COMPLETO (Ej: MOR-11-M-01 o TJ-11-M-01)
    if (termino.includes('-')) {
        encontrada = this.unidadesActivas.find(u => u.nro_serie_interno === termino) ||
                     this.todasDanadas.find(u => u.nro_serie_interno === termino);
    }
    // CASO 2: El método rápido de la tienda (Selecciona Talla + Sube/Baja el Número de Extensión)
    else {
        let extPad = termino.padStart(2, '0'); // Convierte '1' en '01'
        
        // BUSQUEDA INTELIGENTE POR PROPIEDADES:
        // No importa si el prefijo es TJ-, TMP- o MOR-. Solo valida que coincida la Talla 
        // y que el código físico termine exactamente en '-XX'
        encontrada = this.unidadesActivas.find(u => 
            u.talla.toUpperCase() === this.searchTalla.toUpperCase() && 
            u.nro_serie_interno.endsWith('-' + extPad)
        );
    }

    // Busca este bloque al final de tu función filtrarPrendaFisica() y déjalo así:
if (encontrada) {
    // CAMBIADO: 'encontrar' corregido por 'encontrada'
    this.abrirMantenimiento(
        encontrada.cod_unidad, 
        encontrada.nro_serie_interno, 
        encontrada.estado_fisico, 
        encontrada.observaciones
    );
    this.searchExt = '';
} else {
    alert('No existe la prenda física con esa combinación de Talla y Extensión para este traje.');
}
}
         }">

        {{-- ══ HEADER DE SECCIÓN ══ --}}
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
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
                <h2 class="text-2xl font-black uppercase text-gray-800 mt-1">{{ $trajeActivo->nom_traje }}</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                    Prefijo del Lote: <span class="text-andes-verde font-black">[{{ $prefijoDanza }}]</span> • Sintoniza la talla y el correlativo para auditar
                </p>
            </div>

            {{-- FORMULARIO DE BÚSQUEDA HÍBRIDO --}}
            <div class="flex flex-wrap items-center gap-2 bg-gray-50 p-2 rounded-2xl border w-full sm:w-auto">
                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400 pl-2">Talla:</span>
                <select x-model="searchTalla"
                        class="px-3 py-1.5 text-xs font-black rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:border-andes-verde">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="Personalizado">Pers.</option>
                </select>

                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400">Nro:</span>
                <input type="number" x-model="searchExt" placeholder="01"
                       @keydown.enter="filtrarPrendaFisica()"
                       class="w-16 px-3 py-1.5 text-xs font-black rounded-xl border border-gray-100 text-gray-800 focus:outline-none focus:border-andes-verde">

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
                    Prendas en Reparación / Desgastadas actualmente
                </h5>
                <div class="overflow-hidden rounded-2xl border border-gray-50">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Código Físico</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Talla</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Condición</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px]">Reporte de Daños</th>
                                <th class="px-6 py-4 font-black uppercase text-gray-400 text-[9px] text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($unidadesDanadas as $ud)
                                <tr class="hover:bg-red-50/10 transition-colors">
                                    <td class="px-6 py-4 font-black text-gray-800 uppercase">{{ $ud->nro_serie_interno }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-500">{{ $ud->talla }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase
                                                     {{ $ud->estado_fisico == 'En Reparación' ? 'bg-amber-100 text-amber-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $ud->estado_fisico }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-500 italic truncate max-w-[200px]"
                                        title="{{ $ud->observaciones }}">
                                        {{ $ud->observaciones ?? 'Sin especificar detalles del daño.' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button"
                                                @click="abrirMantenimiento('{{ $ud->cod_unidad }}', '{{ $ud->nro_serie_interno }}', '{{ $ud->estado_fisico }}', '{{ addslashes($ud->observaciones) }}')"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-andes-oscuro hover:text-white transition rounded-xl font-black uppercase text-[9px] tracking-tight">
                                            Sanar Prenda
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium italic">
                                        🎉 ¡Excelente! No tienes ninguna prenda reportada con daños en tu tienda.
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
                    <h6 class="text-xs font-black uppercase tracking-widest text-andes-amarillo">Protocolo de Mantenimiento</h6>
                    <p class="text-[11px] text-slate-400 leading-relaxed mt-2 italic">
                        Cuando una prenda física es marcada como <strong class="text-white">"Desgastada"</strong> o
                        <strong class="text-white">"En Reparación"</strong>, el sistema la da de baja del stock público
                        de forma automática. Al solucionar el desperfecto, cámbiala a "Buen Estado" o "Nuevo" para
                        devolverla a la vitrina de alquileres.
                    </p>
                </div>
                <div class="text-[9px] text-slate-500 font-bold border-t border-slate-800/60 pt-4 uppercase tracking-tighter">
                    Filtros rápidos vinculados a PostgreSQL
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
                        <h3 class="text-lg font-black text-gray-800 uppercase mt-0.5" x-text="unidadEditar.serial"></h3>
                    </div>
                    <button type="button" @click="modalDanoOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'/vendedor/unidades/' + unidadEditar.id" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5">
                            Nueva Condición Física
                        </label>
                        <select name="estado_fisico" x-model="unidadEditar.estado"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs uppercase font-black text-gray-700 tracking-wider focus:bg-white focus:border-andes-verde">
                            <option value="Nuevo">✨ Re-Confeccionado / Como Nuevo</option>
                            <option value="Buen Estado">👍 Sano / Buen Estado (Listo para alquilar)</option>
                            <option value="Desgastado">⚠️ Desgastado (Sigue con detalles)</option>
                            <option value="En Reparación">🛠️ En Reparación (Se queda en taller)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5">
                            Diagnóstico de Daños
                        </label>
                        <textarea name="observaciones" x-model="unidadEditar.observaciones" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-gray-700 focus:bg-white focus:border-andes-verde"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-andes-oscuro text-white py-3.5 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg hover:bg-black transition">
                        Actualizar Historial Médico
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
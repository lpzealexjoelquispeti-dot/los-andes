{{--
    PARTIAL: vendedor/unidades/_perchero.blade.php
    Uso: @include('vendedor.unidades._perchero', ['unidades' => $trajeActivo->unidades])
    Requiere: colección $unidades (Eloquent collection con withTrashed())
--}}

<div class="mt-14"
     x-data="{
        unidades: {{ json_encode($unidades) }},

        modalEditarOpen: false,
        editando: {
            id: null,
            serial: '',
            tallaOriginal: '',
            talla: '',
            estado: '',
            tallaAdvertencia: false,
        },

        abrirEditar(u) {
            this.editando = {
                id:               u.cod_unidad,
                serial:           u.nro_serie_interno,
                tallaOriginal:    u.talla,
                talla:            u.talla,
                estado:           u.estado_fisico,
                tallaAdvertencia: false,
            };
            this.modalEditarOpen = true;
        },

        detectarCambioTalla() {
            this.editando.tallaAdvertencia = (this.editando.talla !== this.editando.tallaOriginal);
        },
     }">

    {{-- ══ ENCABEZADO ══ --}}
    <div class="border-t border-gray-100 pt-10 mb-6 ml-2">
        <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Perchero Físico Completo</h4>
        <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
            Todas las prendas registradas en el sistema — activas y dadas de baja
        </p>
    </div>

    {{-- ══ TABLA DE UNIDADES ══ --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest">Código Físico</th>
                    <th class="px-5 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest text-center">Talla</th>
                    <th class="px-5 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest">Condición</th>
                    <th class="px-5 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest">Observaciones</th>
                    <th class="px-5 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="u in unidades" :key="u.cod_unidad">
                    <tr class="transition-colors"
                        :class="u.deleted_at
                            ? 'bg-red-50/40 opacity-60 text-gray-400'
                            : 'hover:bg-gray-50/80'">

                        {{-- 1. Código — solo lectura siempre --}}
                        <td class="px-5 py-3">
                            <span class="font-black uppercase font-mono tracking-tight"
                                  :class="u.deleted_at ? 'text-gray-400 line-through' : 'text-gray-800'"
                                  x-text="u.nro_serie_interno"></span>
                            <template x-if="u.deleted_at">
                                <span class="ml-1.5 text-[8px] font-black bg-red-100 text-red-500 px-1.5 py-0.5 rounded uppercase">baja</span>
                            </template>
                        </td>

                        {{-- 2. Talla --}}
                        <td class="px-5 py-3 text-center">
                            <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-[10px] font-black uppercase"
                                  x-text="u.talla"></span>
                        </td>

                        {{-- 3. Condición con colores por estado --}}
                        <td class="px-5 py-3">
                            <template x-if="u.deleted_at">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-red-600 text-white">
                                    🛑 Baja Lógica
                                </span>
                            </template>
                            <template x-if="!u.deleted_at">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase"
                                      :class="{
                                          'bg-blue-100 text-blue-700':     u.estado_fisico === 'Nuevo',
                                          'bg-green-100 text-green-700':   u.estado_fisico === 'Buen Estado',
                                          'bg-orange-100 text-orange-700': u.estado_fisico === 'Desgastado',
                                          'bg-amber-100 text-amber-700':   u.estado_fisico === 'En Reparación',
                                      }"
                                      x-text="u.estado_fisico"></span>
                            </template>
                        </td>

                        {{-- 4. Observaciones --}}
                        <td class="px-5 py-3 font-medium italic text-gray-400 truncate max-w-[160px]"
                            :title="u.observaciones">
                            <span x-text="u.deleted_at
                                ? 'Prenda removida del stock'
                                : (u.observaciones || 'Sin novedades')"></span>
                        </td>

                        {{-- 5. Acciones --}}
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">

                                {{-- Formularios ocultos --}}
                                <form :id="'delete-form-' + u.cod_unidad"
                                      :action="'/vendedor/unidades/' + u.cod_unidad"
                                      method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <form :id="'restore-form-' + u.cod_unidad"
                                      :action="'/vendedor/unidades/' + u.cod_unidad + '/restore'"
                                      method="POST" class="hidden">
                                    @csrf
                                </form>

                                {{-- Prenda ACTIVA --}}
                                <template x-if="!u.deleted_at">
                                    <div class="flex gap-1.5">
                                        <button type="button"
                                                @click="abrirEditar(u)"
                                                class="p-1.5 bg-gray-100 hover:bg-blue-50 text-blue-600 rounded-lg transition"
                                                title="Editar prenda">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                @click="if(confirm('¿Dar de BAJA LÓGICA esta prenda? Quedará archivada y podrás reactivarla después.')) { document.getElementById('delete-form-' + u.cod_unidad).submit(); }"
                                                class="p-1.5 bg-gray-100 hover:bg-red-50 text-red-500 rounded-lg transition"
                                                title="Dar de baja">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- Prenda en BAJA: solo Reactivar --}}
                                <template x-if="u.deleted_at">
                                    <button type="button"
                                            @click="if(confirm('¿Reactivar esta prenda y devolverla al stock?')) { document.getElementById('restore-form-' + u.cod_unidad).submit(); }"
                                            class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white border border-emerald-200 hover:border-transparent rounded-lg font-black uppercase text-[9px] tracking-tight transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Reactivar
                                    </button>
                                </template>

                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ══ MODAL DE EDICIÓN ══ --}}
    <div x-show="modalEditarOpen"
         x-transition.opacity
         class="fixed inset-0 z-[120] flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
         x-cloak>

        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden"
             @click.away="modalEditarOpen = false">

            {{-- Header oscuro con el serial --}}
            <div class="bg-andes-oscuro px-8 py-6 flex justify-between items-start">
                <div>
                    <p class="text-[9px] font-black uppercase text-andes-verde tracking-widest">Editar Prenda Física</p>
                    <h3 class="text-base font-black text-white uppercase mt-0.5 font-mono tracking-tight"
                        x-text="editando.serial"></h3>
                    <p class="text-[10px] text-slate-500 mt-1">El código identificador no es editable.</p>
                </div>
                <button type="button" @click="modalEditarOpen = false"
                        class="text-slate-400 hover:text-white transition mt-1 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Cuerpo del formulario --}}
            <form :action="'/vendedor/unidades/' + editando.id" method="POST" class="px-8 py-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Talla --}}
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5">
                        Talla de la Prenda
                    </label>
                    <select name="talla"
                            x-model="editando.talla"
                            @change="detectarCambioTalla()"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-black uppercase text-gray-700 focus:bg-white focus:border-andes-verde focus:outline-none transition">
                        <option value="S">S — Small</option>
                        <option value="M">M — Medium</option>
                        <option value="L">L — Large</option>
                        <option value="XL">XL — Extra Large</option>
                        <option value="Personalizado">Personalizado</option>
                    </select>

                    {{-- Advertencia si cambia la talla --}}
                    <div x-show="editando.tallaAdvertencia"
                         x-transition
                         class="mt-2.5 flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl px-3.5 py-3">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-[10px] font-black text-amber-700 uppercase tracking-wide">⚠ Código quedará desincronizado</p>
                            <p class="text-[10px] text-amber-600 mt-0.5 leading-relaxed">
                                El código <strong x-text="editando.serial"></strong> no cambiará automáticamente.
                                Si fue un error al crear la prenda, continúa. Si no, cancela, da de baja esta prenda y crea una nueva en la talla correcta.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Estado Físico como tarjetas radio --}}
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-2">
                        Condición Física Actual
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="opcion in [
                            { valor: 'Nuevo',         emoji: '✨', label: 'Como Nuevo',    activo: 'border-blue-300 bg-blue-50 text-blue-700' },
                            { valor: 'Buen Estado',   emoji: '👍', label: 'Buen Estado',   activo: 'border-green-300 bg-green-50 text-green-700' },
                            { valor: 'Desgastado',    emoji: '⚠️', label: 'Desgastado',    activo: 'border-orange-300 bg-orange-50 text-orange-700' },
                            { valor: 'En Reparación', emoji: '🛠️', label: 'En Reparación', activo: 'border-amber-300 bg-amber-50 text-amber-700' },
                        ]" :key="opcion.valor">
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition-all select-none"
                                   :class="editando.estado === opcion.valor
                                       ? opcion.activo
                                       : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200'">
                                <input type="radio" name="estado_fisico"
                                       :value="opcion.valor"
                                       x-model="editando.estado"
                                       class="sr-only">
                                <span x-text="opcion.emoji" class="text-sm leading-none"></span>
                                <span class="text-[10px] font-black uppercase tracking-tight leading-tight" x-text="opcion.label"></span>
                                <svg x-show="editando.estado === opcion.valor"
                                     class="w-3.5 h-3.5 ml-auto shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex gap-3 pt-1">
                    <button type="button"
                            @click="modalEditarOpen = false"
                            class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-500 font-black uppercase text-[10px] tracking-widest hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-andes-oscuro hover:bg-black text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<x-app-layout>
    <x-slot name="header">Ingreso Masivo de Stock</x-slot>

    <div class="max-w-3xl mx-auto py-10 px-6">
        
        <div class="mb-6">
            <a href="{{ route('vendedor.trajes.unidades.index', $traje->cod_traje) }}" 
               class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-andes-verde transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Volver a la Matriz
            </a>
        </div>

        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 p-8 md:p-10">
            
            <div class="flex items-center gap-5 border-b border-gray-100 pb-6 mb-8">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 border shrink-0">
                    <img src="{{ $traje->imagenes->first() ? asset('storage/' . $traje->imagenes->first()->ruta_img) : asset('img/default-traje.jpg') }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.25em]">Producción / Registro de Lote para:</h4>
                    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight mt-0.5">{{ $traje->nom_traje }}</h2>
                    <p class="text-xs font-bold text-andes-verde uppercase mt-0.5">{{ $traje->danza->nom_danza ?? 'General Folclore' }}</p>
                </div>
            </div>

            <form action="{{ route('vendedor.trajes.unidades.store', $traje->cod_traje) }}" method="POST" class="space-y-6">
                @csrf

                {{-- 1. SELECCIÓN DE TALLA --}}
                <div x-data="{ tallaActiva: '{{ old('talla', 'M') }}' }">
                    <label class="block text-[11px] font-black uppercase text-gray-400 tracking-widest mb-3">¿De qué talla es este lote?</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $t)
                            <label class="cursor-pointer">
                                <input type="radio" name="talla" value="{{ $t }}" class="hidden" 
                                       @change="tallaActiva = '{{ $t }}'" {{ old('talla', 'M') == $t ? 'checked' : '' }}>
                                <div class="py-4 rounded-2xl border-2 font-black text-center text-xs uppercase tracking-wider transition-all"
                                     :class="tallaActiva === '{{ $t }}' ? 'border-andes-verde bg-emerald-50 text-andes-verde shadow-sm scale-102' : 'border-gray-100 bg-white text-gray-500 hover:border-gray-200'">
                                    {{ $t === 'Personalizado' ? 'Pers.' : $t }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('talla') <p class="text-red-500 text-xs font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                {{-- 2. NUEVO CAMPO: CANTIDAD DE TRAJES A CREAR --}}
                <div>
                    <label for="cantidad" class="block text-[11px] font-black uppercase text-gray-400 tracking-widest mb-2">
                        ¿Cuántas unidades físicas de esta talla vas a agregar?
                    </label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad', 1) }}" min="1" max="50"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-base font-black text-gray-800 focus:bg-white focus:border-andes-verde focus:ring-4 focus:ring-andes-verde/10 transition">
                    <p class="text-[9px] font-medium text-gray-400 mt-1.5 italic">El sistema generará automáticamente un identificador temporal único para cada una de las prendas ingresadas.</p>
                    @error('cantidad') <p class="text-red-500 text-xs font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                {{-- 3. ESTADO FÍSICO DEL LOTE --}}
                <div>
                    <label for="estado_fisico" class="block text-[11px] font-black uppercase text-gray-400 tracking-widest mb-2">Condición Física de las Prendas</label>
                    <select name="estado_fisico" id="estado_fisico"
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-xs uppercase font-black text-gray-700 tracking-widest focus:bg-white focus:border-andes-verde focus:ring-4 focus:ring-andes-verde/10 transition">
                        <option value="Nuevo" {{ old('estado_fisico') == 'Nuevo' ? 'selected' : '' }}>✨ Todo el lote es Nuevo / Recién Confeccionado</option>
                        <option value="Buen Estado" {{ old('estado_fisico') == 'Buen Estado' ? 'selected' : '' }}>👍 Buen Estado (Usados Operativos)</option>
                        <option value="Desgastado" {{ old('estado_fisico') == 'Desgastado' ? 'selected' : '' }}>⚠️ Desgastados</option>
                        <option value="En Reparación" {{ old('estado_fisico') == 'En Reparación' ? 'selected' : '' }}>🛠️ En Reparación</option>
                    </select>
                    @error('estado_fisico') <p class="text-red-500 text-xs font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                {{-- BOTÓN ENVIAR --}}
                <div class="pt-4 border-t border-gray-50">
                    <button type="submit" 
                            class="w-full bg-andes-oscuro text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-black transition-all transform active:scale-98">
                        Procesar e Inyectar al Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
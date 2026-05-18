<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase">
                Editar Traje: {{ $traje->nom_traje }}
            </h2>
            <a href="{{ route('vendedor.trajes.index') }}" class="text-xs font-black text-gray-400 hover:text-andes-rojo uppercase tracking-widest transition">
                ← Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4" x-data="trajeEdit()">
        
        {{-- Bloque de Errores de Validación --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-andes-rojo text-red-800 rounded-lg shadow-sm">
                <p class="font-black uppercase text-xs mb-2">⚠️ Por favor, corrige lo siguiente:</p>
                <ul class="list-disc ml-5 text-[11px] font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendedor.trajes.update', $traje->cod_traje) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- ── PANEL IZQUIERDO: GESTIÓN DE FOTOS (Compacto) ── --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-6 rounded-2xl shadow-xl border-t-4 border-andes-rojo">
                        <h3 class="text-lg font-black text-andes-oscuro uppercase mb-4 tracking-tight">Fotos del Traje</h3>
                        
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            
                            @foreach($traje->imagenes as $img)
                                <div id="imagen_existente_{{ $img->cod_imagen }}" 
                                     x-data="{ confirm: false }"
                                     class="relative aspect-square">
                                    
                                    <img src="{{ asset('storage/' . $img->ruta_img) }}" 
                                         class="h-24 w-full object-cover rounded-xl border-2 border-gray-100 shadow-sm transition-all"
                                         :class="confirm ? 'opacity-20 blur-sm' : 'opacity-100'">
                                    
                                    <button type="button" @click="confirm = true" x-show="!confirm"
                                            class="absolute top-2 right-2 bg-andes-rojo text-white rounded-full w-7 h-7 flex items-center justify-center shadow-xl border-2 border-white hover:scale-110 transition z-10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>

                                    <div x-show="confirm" x-transition.opacity
                                         class="absolute inset-0 bg-andes-rojo/90 rounded-xl flex flex-col items-center justify-center p-1 z-20">
                                        <p class="text-[8px] text-white font-black uppercase mb-1 leading-none text-center">¿Eliminar?</p>
                                        <div class="flex gap-1">
                                            <button type="button" @click="markExistingForDeletion('{{ $img->cod_imagen }}'); confirm = false" 
                                                    class="bg-white text-andes-rojo text-[9px] px-2 py-0.5 rounded font-black shadow-sm">SÍ</button>
                                            <button type="button" @click="confirm = false" 
                                                    class="bg-andes-oscuro text-white text-[9px] px-2 py-0.5 rounded font-black">NO</button>
                                        </div>
                                    </div>
                                    
                                    <span class="absolute bottom-1 left-1 bg-white/80 text-[7px] font-black px-1.5 rounded-md uppercase text-gray-500 shadow-sm">Actual</span><br>
                                </div>
                            @endforeach

                            <template x-for="(image, index) in previews" :key="index">
                                <div class="relative group">
                                    <img :src="image" class="h-24 w-full object-cover rounded-xl border-2 border-andes-verde shadow-sm">
                                    
                                    <button type="button" @click="removeImage(index)" 
                                            class="absolute top-2 right-2 bg-andes-rojo text-white rounded-full w-7 h-7 flex items-center justify-center shadow-xl border-2 border-white hover:scale-110 transition z-10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>

                            <label x-show="totalPhotosCount() < 7" 
                                   class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition group">
                                <svg class="w-8 h-8 text-gray-400 group-hover:text-andes-amarillo transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-[10px] font-bold text-gray-400 uppercase mt-1 text-center px-1">Añadir JPG</span>
                                <input type="file" id="fileInput" name="fotos_nuevas[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleFiles($event)">
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase tracking-tighter text-gray-400">
                                <span :class="totalPhotosCount() >= 7 ? 'text-andes-rojo' : 'text-andes-verde'" x-text="totalPhotosCount()"></span> de 7 fotos totales
                            </p>
                        </div>
                    </div>

                    <div class="bg-andes-oscuro p-4 rounded-xl text-white shadow-lg">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-1 text-andes-amarillo">Calidad Los Andes:</p>
                        <p class="text-[10px] leading-tight opacity-80 font-medium">
                            Usa fotos nítidas con buena iluminación. Solo se permiten archivos .JPG de cámara.
                        </p>
                    </div>
                </div>

                {{-- ── PANEL DERECHO: FORMULARIO (2/3 de ancho) ── --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-andes-verde">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <x-input-label for="nom_traje" value="Nombre del Traje" class="font-black" />
                                <x-text-input id="nom_traje" name="nom_traje" type="text" 
                                              class="block mt-1 w-full" 
                                              placeholder="Ej: Traje de Caporal" 
                                              pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                              title="Solo se permiten letras y espacios"
                                              :value="old('nom_traje', $traje->nom_traje)" required />
                            </div>

                            <div>
                                <x-input-label for="cod_danza_traje" value="Danza / Categoría" class="font-black" />
                                <select id="cod_danza_traje" name="cod_danza_traje" required 
                                        class="block mt-1 w-full border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm font-bold text-gray-700">
                                    @foreach($danzas as $danza)
                                        <option value="{{ $danza->cod_danza }}" {{ old('cod_danza_traje', $traje->cod_danza_traje) == $danza->cod_danza ? 'selected' : '' }}>
                                            {{ $danza->nom_danza }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="pre_alquiler" value="Precio de Alquiler (Bs)" class="font-black" />
                                <x-text-input id="pre_alquiler" name="pre_alquiler" type="number" step="0.01" min="0"
                                              class="block mt-1 w-full" 
                                              :value="old('pre_alquiler', $traje->pre_alquiler)" required />
                            </div>

                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
    <x-input-label value="Tallas Disponibles para este Traje" class="font-black uppercase text-xs tracking-wider text-gray-500 mb-3" />
    
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @php
            // Definimos las tallas estándar
            $tallasDisponibles = ['S', 'M', 'L', 'XL', 'Personalizado'];
            
            // Obtenemos las tallas ya guardadas si estamos en modo edición, o un array vacío si es creación
            $tallasGuardadas = isset($traje) ? $traje->unidades->pluck('talla')->toArray() : [];
        @endphp

        @foreach($tallasDisponibles as $talla)
            <label class="flex items-center justify-between p-3 bg-white border border-gray-300 rounded-xl cursor-pointer hover:border-andes-verde hover:bg-emerald-50/30 transition shadow-sm select-none group">
                <span class="font-bold text-gray-700 group-hover:text-andes-oscuro text-sm">
                    Talla {{ $talla }}
                </span>
                <input type="checkbox" 
                       name="tallas[]" 
                       value="{{ $talla }}" 
                       {{ in_array($talla, old('tallas', $tallasGuardadas)) ? 'checked' : '' }}
                       class="w-5 h-5 text-andes-verde border-gray-300 rounded focus:ring-andes-verde transition cursor-pointer">
            </label>
        @endforeach
    </div>
    
    <x-input-error :messages="$errors->get('tallas')" class="mt-2" />
</div>

                            <div>
                                <x-input-label for="color_traje" value="Color Dominante" class="font-black" />
                                <x-text-input id="color_traje" name="color_traje" type="text" 
                                              placeholder="Ej: Rojo y Blanco" 
                                              pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                              title="El color debe ser solo texto"
                                              class="block mt-1 w-full" 
                                              :value="old('color_traje', $traje->color_traje)" required />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="des_traje" value="Descripción Detallada" class="font-black" />
                                <textarea id="des_traje" name="des_traje" rows="4" 
                                          class="block mt-1 w-full border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm">{{ old('des_traje', $traje->des_traje) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                            <a href="{{ route('vendedor.trajes.index') }}" class="py-4 px-8 rounded-xl font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition text-xs">Cancelar</a>
                            <button type="submit" 
                                    class="bg-andes-oscuro hover:bg-black text-white font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input Oculto para IDs a Borrar --}}
            <input type="hidden" name="fotos_borrar" id="fotos_borrar_list" value="">
        </form>
    </div>

    <script>
        function trajeEdit() {
            return {
                previews: [],
                dataTransfer: new DataTransfer(),
                deletedImageIds: [],
                initialImageCount: {{ $traje->imagenes->count() }},

                totalPhotosCount() {
                    return (this.initialImageCount - this.deletedImageIds.length) + this.previews.length;
                },

                markExistingForDeletion(imageId) {
                    const el = document.getElementById('imagen_existente_' + imageId);
                    if (el) {
                        el.style.display = 'none';
                        this.deletedImageIds.push(imageId);
                        document.getElementById('fotos_borrar_list').value = this.deletedImageIds.join(',');
                    }
                },

                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    const input = document.getElementById('fileInput');

                    if (this.totalPhotosCount() + files.length > 7) {
                        alert('Atención: El catálogo permite un máximo de 7 fotos por traje.');
                        return;
                    }

                    files.forEach(file => {
                        // Validación estricta de JPG en cliente
                        if (file.type === 'image/jpeg' || file.type === 'image/jpg') {
                            this.dataTransfer.items.add(file);
                            const reader = new FileReader();
                            reader.onload = (e) => this.previews.push(e.target.result);
                            reader.readAsDataURL(file);
                        } else {
                            alert('El archivo "' + file.name + '" no es un formato .JPG válido.');
                        }
                    });
                    input.files = this.dataTransfer.files;
                },

                removeImage(index) {
                    const input = document.getElementById('fileInput');
                    this.previews.splice(index, 1);
                    const newDataTransfer = new DataTransfer();
                    Array.from(this.dataTransfer.files).forEach((file, i) => {
                        if (i !== index) newDataTransfer.items.add(file);
                    });
                    this.dataTransfer = newDataTransfer;
                    input.files = this.dataTransfer.files;
                }
            }
        }
    </script>
</script>
</x-app-layout>
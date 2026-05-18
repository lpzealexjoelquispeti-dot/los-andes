<x-app-layout>
    <x-slot name="header">
        Publicar Nuevo Traje Folklórico
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4" x-data="trajeUpload()">
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-andes-rojo text-red-800 rounded-lg">
        <p class="font-black uppercase text-sm mb-2">⚠️ Errores de validación:</p>
        <ul class="list-disc ml-5 text-xs font-bold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('vendedor.trajes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-6 rounded-2xl shadow-xl border-t-4 border-andes-rojo">
                        <h3 class="text-lg font-black text-andes-oscuro uppercase mb-4">Fotos del Traje</h3>
                        
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <template x-for="(image, index) in previews" :key="index">
                                <div class="relative group">
                                    <img :src="image" class="h-24 w-full object-cover rounded-lg border-2 border-andes-verde shadow-sm">
                                    <button type="button" @click="removeImage(index)" 
                                            class="absolute -top-2 -right-2 bg-andes-rojo text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:scale-110 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>

                            <label x-show="previews.length < 7" 
                                   class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-[10px] font-bold text-gray-400 uppercase mt-1 text-center px-1">Añadir JPG</span>
                                
                                <input type="file" id="fileInput" name="fotos[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleFiles($event)">
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase tracking-tighter" :class="previews.length === 7 ? 'text-andes-rojo' : 'text-gray-400'">
                                <span x-text="previews.length"></span> de 7 fotos seleccionadas
                            </p>
                        </div>

                        <x-input-error :messages="$errors->get('fotos')" class="mt-2" />
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <p class="text-[10px] text-blue-700 leading-tight">
                            <strong>💡 Tip:</strong> Puedes seleccionar varias fotos a la vez o ir agregándolas una por una. Recuerda que solo se permiten archivos <strong>.JPG</strong>.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-andes-verde">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <x-input-label for="nom_traje" value="Nombre del Traje" class="font-black" />
                                <x-text-input id="nom_traje" name="nom_traje" type="text" class="block mt-1 w-full" placeholder="Ej: Traje de Caporal Rey de Bolivia" required />
                                <x-input-error :messages="$errors->get('nom_traje')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cod_danza_traje" value="Danza / Categoría" class="font-black" />
                                <select id="cod_danza_traje" name="cod_danza_traje" required class="block mt-1 w-full border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm font-bold text-gray-700">
                                    <option value="">Selecciona una danza...</option>
                                    @foreach($danzas as $danza)
                                        <option value="{{ $danza->cod_danza }}">{{ $danza->nom_danza }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="pre_alquiler" value="Precio de Alquiler (Bs)" class="font-black" />
                                <x-text-input id="pre_alquiler" name="pre_alquiler" type="number" step="0.01" class="block mt-1 w-full" placeholder="0.00" required />
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
                                <x-text-input id="color_traje" name="color_traje" type="text" placeholder="Ej: Rojo con Dorado" class="block mt-1 w-full" required />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="des_traje" value="Descripción Detallada" class="font-black" />
                                <textarea id="des_traje" name="des_traje" rows="4" class="block mt-1 w-full border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm" placeholder="Describe qué incluye el traje, accesorios, estado, etc."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" 
                                    class="bg-andes-oscuro hover:bg-black text-white font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 uppercase tracking-widest">
                                Publicar en Los Andes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function trajeUpload() {
            return {
                previews: [],
                // DataTransfer acumula los archivos reales para que Laravel los reciba todos
                dataTransfer: new DataTransfer(),

                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    const input = document.getElementById('fileInput');

                    // Validar límite total
                    if (this.previews.length + files.length > 7) {
                        alert('Atención: Solo se permite un máximo de 7 fotos por traje.');
                        return;
                    }

                    files.forEach(file => {
                        if (file.type === 'image/jpeg' || file.type === 'image/jpg') {
                            // Añadir al "almacén" de archivos
                            this.dataTransfer.items.add(file);
                            
                            // Crear preview para la vista
                            const reader = new FileReader();
                            reader.onload = (e) => this.previews.push(e.target.result);
                            reader.readAsDataURL(file);
                        } else {
                            alert('El archivo ' + file.name + ' no es un formato válido (.JPG)');
                        }
                    });

                    // Sincronizar el input oculto con los archivos acumulados
                    input.files = this.dataTransfer.files;
                },

                removeImage(index) {
                    const input = document.getElementById('fileInput');
                    
                    // 1. Quitar de las vistas previas
                    this.previews.splice(index, 1);
                    
                    // 2. Reconstruir el DataTransfer para eliminar el archivo real
                    const newDataTransfer = new DataTransfer();
                    Array.from(this.dataTransfer.files).forEach((file, i) => {
                        if (i !== index) newDataTransfer.items.add(file);
                    });
                    this.dataTransfer = newDataTransfer;

                    // 3. Sincronizar de nuevo el input
                    input.files = this.dataTransfer.files;
                }
            }
        }
    </script>
</x-app-layout>
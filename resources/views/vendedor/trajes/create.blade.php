<x-app-layout>
    <x-slot name="header">
        Publicar en el Catálogo de Los Andes
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4" x-data="trajeForm()">
        
        {{-- MENSAJE RECOIL DE ERRORES DE VALIDACIÓN --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 text-red-800 rounded-xl shadow-sm">
                <p class="font-black uppercase text-sm mb-2 flex items-center gap-1">⚠️ Errores en el registro:</p>
                <ul class="list-disc ml-5 text-xs font-bold space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendedor.trajes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- BLOQUE 1: DATOS GENERALES --}}
            <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-emerald-500 mb-8">
                <h3 class="text-xl font-black text-gray-800 uppercase mb-6 flex items-center gap-2">
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">📦</span> Datos Generales del Catálogo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {{-- 1. Nombre Propio o Modelo del Traje --}}
                    <div class="md:col-span-2">
                        <x-input-label for="nom_traje_base" class="font-black">
                            Nombre / Modelo del Traje <span class="text-red-500 font-bold">*</span>
                        </x-input-label>
                        <x-text-input id="nom_traje_base" name="nom_traje_base" type="text" 
                                      class="block mt-1 w-full @error('nom_traje_base') border-red-500 focus:ring-red-500 @enderror" 
                                      placeholder="Ej: Rey Achachi Galán de Plata" 
                                      value="{{ old('nom_traje_base') }}" required />
                        @error('nom_traje_base') <p class="text-red-500 text-xs mt-1 font-bold">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- 2. Fraternidad Folclórica (Nuevo Campo Adicionado - Separado) --}}
                    <div class="md:col-span-2">
                        <x-input-label for="fraternidad" class="font-black">
                            Fraternidad / Agrupación <span class="text-red-500 font-bold">*</span>
                        </x-input-label>
                        <x-text-input id="fraternidad" name="fraternidad" type="text" 
                                      class="block mt-1 w-full @error('fraternidad') border-red-500 focus:ring-red-500 @enderror" 
                                      placeholder="Ej: Morenada Central Cocani" 
                                      value="{{ old('fraternidad') }}" required />
                        @error('fraternidad') <p class="text-red-500 text-xs mt-1 font-bold">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- 3. Selección de Danza Maestro --}}
                    <div>
                        <x-input-label for="cod_danza_traje" class="font-black">
                            Danza / Categoría <span class="text-red-500 font-bold">*</span>
                        </x-input-label>
                        <select id="cod_danza_traje" name="cod_danza_traje" required 
                                class="block mt-1 w-full border-gray-300 @error('cod_danza_traje') border-red-500 focus:ring-red-500 @else focus:border-emerald-500 focus:ring-emerald-500 @enderror rounded-lg shadow-sm font-bold text-gray-700">
                            <option value="">Selecciona una danza...</option>
                            @foreach($danzas as $danza)
                                <option value="{{ $danza->cod_danza }}" {{ old('cod_danza_traje') == $danza->cod_danza ? 'selected' : '' }}>
                                    {{ $danza->nom_danza }}
                                </option>
                            @endforeach
                        </select>
                        @error('cod_danza_traje') <p class="text-red-500 text-xs mt-1 font-bold">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- 4. Precio de Alquiler Base --}}
                    <div>
                        <x-input-label for="pre_alquiler" class="font-black">
                            Precio Alquiler Base (Bs) <span class="text-red-500 font-bold">*</span>
                        </x-input-label>
                        <x-text-input id="pre_alquiler" name="pre_alquiler" type="number" step="0.50" min="0"
                                      class="block mt-1 w-full @error('pre_alquiler') border-red-500 focus:ring-red-500 @enderror" 
                                      placeholder="Bs. 0.00" 
                                      value="{{ old('pre_alquiler') }}" required   />
                        @error('pre_alquiler') <p class="text-red-500 text-xs mt-1 font-bold">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- 5. Color Dominante --}}
                    <div class="md:col-span-1">
                        <x-input-label for="color_traje" class="font-black">
                            Color Dominante <span class="text-red-500 font-bold">*</span>
                        </x-input-label>
                        <x-text-input id="color_traje" name="color_traje" type="text" 
                                      placeholder="Ej: Turquesa con Plata" 
                                      class="block mt-1 w-full @error('color_traje') border-red-500 focus:ring-red-500 @enderror" 
                                      value="{{ old('color_traje') }}" required />
                        @error('color_traje') <p class="text-red-500 text-xs mt-1 font-bold">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- 6. Selector de Modalidad Operativa (Persistido con Alpine) --}}
                    <div class="md:col-span-1 bg-gray-50 p-3 rounded-xl border border-gray-200 flex flex-col justify-center">
                        <x-input-label value="Composición" class="font-black uppercase text-[10px] tracking-wider text-gray-500 mb-1.5" />
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="modalidad = 'variantes'" 
                                    :class="modalidad === 'variantes' ? 'bg-gray-900 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300'"
                                    class="py-2 rounded-lg font-black text-[10px] uppercase tracking-wide transition">
                                👫 Pareja
                            </button>
                            <button type="button" @click="modalidad = 'unisex'" 
                                    :class="modalidad === 'unisex' ? 'bg-gray-900 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300'"
                                    class="py-2 rounded-lg font-black text-[10px] uppercase tracking-wide transition">
                                🔄 Unisex
                            </button>
                        </div>
                        <input type="hidden" name="modalidad" :value="modalidad">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DINÁMICA A: MODALIDAD PAREJA --}}
            <div x-show="modalidad === 'variantes'" class="space-y-8" x-transition>
                
                {{-- SECCIÓN BLOQUE VARÓN --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-blue-500 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 space-y-4">
                        <h4 class="text-base font-black text-blue-600 uppercase">♂️ Fotos Bloque Varón (Mín 4, Máx 7) <span class="text-red-500">*</span></h4>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="(img, i) in varonPreviews" :key="i">
                                <div class="relative">
                                    <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                    <button type="button" @click="removeVaronImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                                </div>
                            </template>
                            <label x-show="varonPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 border-blue-200">
                                <span class="text-[10px] font-bold text-blue-500 uppercase">Añadir JPG</span>
                                <input type="file" id="varonFiles" name="fotos_varon[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleVaronFiles($event)">
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <x-input-label value="Descripción específica para el Varón" class="font-black" /> <span class="text-red-500">*</span>
                            <textarea name="des_varon" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 font-semibold text-sm text-gray-700" placeholder="Ej: Incluye pollerón pesado, pechera con perlas, máscara de diablo y cetro.">{{ old('des_varon') }}</textarea>
                        </div>
                        <div>
                            <x-input-label value="Tallas en Stock (Generará correlativos automáticos)" class="font-black text-xs text-gray-500 uppercase" /><span class="text-red-500">*</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                    <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                        <input type="checkbox" name="tallas_varon[]" value="{{ $talla }}" 
                                               {{ is_array(old('tallas_varon')) && in_array($talla, old('tallas_varon')) ? 'checked' : '' }}
                                               class="rounded text-blue-500 focus:ring-blue-500">
                                        <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN BLOQUE MUJER --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-pink-500 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 space-y-4">
                        <h4 class="text-base font-black text-pink-600 uppercase">♀️ Fotos Bloque Mujeres (Mín 4, Máx 7) <span class="text-red-500">*</span></h4>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="(img, i) in mujerPreviews" :key="i">
                                <div class="relative">
                                    <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                    <button type="button" @click="removeMujerImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                                </div>
                            </template>
                            <label x-show="mujerPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 border-pink-200">
                                <span class="text-[10px] font-bold text-pink-500 uppercase">Añadir JPG</span>
                                <input type="file" id="mujerFiles" name="fotos_mujer[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleMujerFiles($event)">
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <x-input-label value="Descripción específica para la Mujer" class="font-black" /><span class="text-red-500">*</span>
                            <textarea name="des_mujer" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 font-semibold text-sm text-gray-700" placeholder="Ej: Pollera de satén, manta bordada artesanalmente y sombrero Borsalino.">{{ old('des_mujer') }}</textarea>
                        </div>
                        <div>
                            <x-input-label value="Tallas Existentes en Stock" class="font-black text-xs text-gray-500 uppercase" /><span class="text-red-500">*</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                    <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                        <input type="checkbox" name="tallas_mujer[]" value="{{ $talla }}" 
                                               {{ is_array(old('tallas_mujer')) && in_array($talla, old('tallas_mujer')) ? 'checked' : '' }}
                                               class="rounded text-pink-500 focus:ring-pink-500">
                                        <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DINÁMICA B: MODALIDAD UNISEX --}}
            <div x-show="modalidad === 'unisex'" class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-gray-700 grid grid-cols-1 lg:grid-cols-3 gap-8" x-transition>
                <div class="lg:col-span-1 space-y-4">
                    <h4 class="text-base font-black text-gray-700 uppercase">🔄 Fotos del Traje Unisex (Mín 4, Máx 7) <span class="text-red-500">*</span></h4>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="(img, i) in unisexPreviews" :key="i">
                            <div class="relative">
                                <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                <button type="button" @click="removeUnisexImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        </template>
                        <label x-show="unisexPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 border-gray-300">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Añadir JPG</span>
                            <input type="file" id="unisexFiles" name="fotos_unisex[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleUnisexFiles($event)">
                        </label>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <x-input-label value="Descripción del Traje" class="font-black" />
                        <textarea name="des_unisex" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-700 focus:ring-gray-700 font-semibold text-sm text-gray-700" placeholder="Describe los componentes del traje unisex...">{{ old('des_unisex') }}</textarea>
                    </div>
                    <div>
                        <x-input-label value="Tallas Existentes en Stock" class="font-black text-xs text-gray-500 uppercase" />
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                    <input type="checkbox" name="tallas_unisex[]" value="{{ $talla }}" 
                                           {{ is_array(old('tallas_unisex')) && in_array($talla, old('tallas_unisex')) ? 'checked' : '' }}
                                           class="rounded text-gray-700 focus:ring-gray-700">
                                    <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÓN SUBMIT DE PRODUCCIÓN --}}
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-gray-900 hover:bg-black text-white font-black py-4 px-12 rounded-xl shadow-lg transition transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                    🚀 Publicar Colección Completa
                </button>
            </div>
        </form>
    </div>

    {{-- ALTA INTERACTIVIDAD REACTIVA CON ALPINE.JS --}}
    <script>
        function trajeForm() {
            return {
                // Recupera la modalidad elegida previamente ante un error de validación
                modalidad: '{{ old('modalidad', 'variantes') }}',
                varonPreviews: [],
                mujerPreviews: [],
                unisexPreviews: [],
                vt: new DataTransfer(),
                mt: new DataTransfer(),
                ut: new DataTransfer(),

                handleVaronFiles(e) {
                    const files = Array.from(e.target.files);
                    if(this.varonPreviews.length + files.length > 7) return Swal.fire({ icon: 'warning', text: 'Máximo 7 fotos por bloque' });
                    files.forEach(f => {
                        this.vt.items.add(f);
                        const r = new FileReader(); r.onload = (ev) => this.varonPreviews.push(ev.target.result); r.readAsDataURL(f);
                    });
                    document.getElementById('varonFiles').files = this.vt.files;
                },
                removeVaronImg(i) {
                    this.varonPreviews.splice(i, 1);
                    const dt = new DataTransfer(); Array.from(this.vt.files).forEach((f, idx) => { if(idx !== i) dt.items.add(f); });
                    this.vt = dt; document.getElementById('varonFiles').files = this.vt.files;
                },

                handleMujerFiles(e) {
                    const files = Array.from(e.target.files);
                    if(this.mujerPreviews.length + files.length > 7) return Swal.fire({ icon: 'warning', text: 'Máximo 7 fotos por bloque' });
                    files.forEach(f => {
                        this.mt.items.add(f);
                        const r = new FileReader(); r.onload = (ev) => this.mujerPreviews.push(ev.target.result); r.readAsDataURL(f);
                    });
                    document.getElementById('mujerFiles').files = this.mt.files;
                },
                removeMujerImg(i) {
                    this.mujerPreviews.splice(i, 1);
                    const dt = new DataTransfer(); Array.from(this.mt.files).forEach((f, idx) => { if(idx !== i) dt.items.add(f); });
                    this.mt = dt; document.getElementById('mujerFiles').files = this.mt.files;
                },

                handleUnisexFiles(e) {
                    const files = Array.from(e.target.files);
                    if(this.unisexPreviews.length + files.length > 7) return Swal.fire({ icon: 'warning', text: 'Máximo 7 fotos por bloque' });
                    files.forEach(f => {
                        this.ut.items.add(f);
                        const r = new FileReader(); r.onload = (ev) => this.unisexPreviews.push(ev.target.result); r.readAsDataURL(f);
                    });
                    document.getElementById('unisexFiles').files = this.ut.files;
                },
                removeUnisexImg(i) {
                    this.unisexPreviews.splice(i, 1);
                    const dt = new DataTransfer(); Array.from(this.ut.files).forEach((f, idx) => { if(idx !== i) dt.items.add(f); });
                    this.ut = dt; document.getElementById('unisexFiles').files = this.ut.files;
                }
            }
        }
    </script>
</x-app-layout>
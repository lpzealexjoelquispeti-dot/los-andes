<x-app-layout>
    <x-slot name="header">
        Publicar en el Catálogo de Los Andes
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4" x-data="trajeForm()">
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

            <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-andes-verde mb-8">
                <h3 class="text-xl font-black text-andes-oscuro uppercase mb-6 flex items-center gap-2">
                    <span class="p-2 bg-emerald-50 text-andes-verde rounded-lg">📦</span> Datos Generales del Modelo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <x-input-label for="nom_traje_base" value="Nombre Base de la Fraternidad / Colección" class="font-black" />
                        <x-text-input id="nom_traje_base" name="nom_traje_base" type="text" class="block mt-1 w-full" placeholder="Ej: Morenada Central San Miguel" required />
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
                        <x-input-label for="pre_alquiler" value="Precio de Alquiler Base (Bs)" class="font-black" />
                        <x-text-input id="pre_alquiler" name="pre_alquiler" type="number" step="0.01" class="block mt-1 w-full" placeholder="0.00" required />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="color_traje" value="Color Dominante" class="font-black" />
                        <x-text-input id="color_traje" name="color_traje" type="text" placeholder="Ej: Turquesa con Plata" class="block mt-1 w-full" required />
                    </div>

                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col justify-center">
                        <x-input-label value="¿Cómo se compone esta publicación?" class="font-black uppercase text-xs tracking-wider text-gray-500 mb-2" />
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" @click="modalidad = 'variantes'" 
                                    :class="modalidad === 'variantes' ? 'bg-andes-oscuro text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300'"
                                    class="py-3 rounded-xl font-black text-xs uppercase tracking-wide transition">
                                👫 Varón y Mujer (Pareja)
                            </button>
                            <button type="button" @click="modalidad = 'unisex'" 
                                    :class="modalidad === 'unisex' ? 'bg-andes-oscuro text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300'"
                                    class="py-3 rounded-xl font-black text-xs uppercase tracking-wide transition">
                                🔄 Traje Unisex / Único
                            </button>
                        </div>
                        <input type="hidden" name="modalidad" :value="modalidad">
                    </div>
                </div>
            </div>

            <div x-show="modalidad === 'variantes'" class="space-y-8" x-transition>
                
                <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-blue-500 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 space-y-4">
                        <h4 class="text-base font-black text-blue-600 uppercase">♂️ Fotos Bloque Varón (Mín 4, Máx 7)</h4>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="(img, i) in varonPreviews" :key="i">
                                <div class="relative">
                                    <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                    <button type="button" @click="removeVaronImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                                </div>
                            </template>
                            <label x-show="varonPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Añadir JPG</span>
                                <input type="file" id="varonFiles" name="fotos_varon[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleVaronFiles($event)">
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <x-input-label value="Descripción específica para el Varón" class="font-black" />
                            <textarea name="des_varon" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ej: Incluye pollerón pesado, pechera con perlas, máscara de diablo y cetro."></textarea>
                        </div>
                        <div>
                            <x-input-label value="Tallas Existentes en Stock (Se autogenerará 1 unidad inicial por cada una)" class="font-black text-xs text-gray-500 uppercase" />
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                    <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                        <input type="checkbox" name="tallas_varon[]" value="{{ $talla }}" class="rounded text-blue-500 focus:ring-blue-500">
                                        <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-pink-500 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 space-y-4">
                        <h4 class="text-base font-black text-pink-600 uppercase">♀️ Fotos Bloque Mujeres (Mín 4, Máx 7)</h4>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="(img, i) in mujerPreviews" :key="i">
                                <div class="relative">
                                    <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                    <button type="button" @click="removeMujerImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                                </div>
                            </template>
                            <label x-show="mujerPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Añadir JPG</span>
                                <input type="file" id="mujerFiles" name="fotos_mujer[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleMujerFiles($event)">
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <x-input-label value="Descripción específica para la Mujer" class="font-black" />
                            <textarea name="des_mujer" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ej: Incluye pollera de satén, manta bordada artesanalmente, sombrero Borsalino y ramillete."></textarea>
                        </div>
                        <div>
                            <x-input-label value="Tallas Existentes en Stock" class="font-black text-xs text-gray-500 uppercase" />
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                    <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                        <input type="checkbox" name="tallas_mujer[]" value="{{ $talla }}" class="rounded text-pink-500 focus:ring-pink-500">
                                        <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div x-show="modalidad === 'unisex'" class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-gray-700 grid grid-cols-1 lg:grid-cols-3 gap-8" x-transition>
                <div class="lg:col-span-1 space-y-4">
                    <h4 class="text-base font-black text-gray-700 uppercase">🔄 Fotos del Traje Unisex (Mín 4, Máx 7)</h4>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="(img, i) in unisexPreviews" :key="i">
                            <div class="relative">
                                <img :src="img" class="h-24 w-full object-cover rounded-lg border">
                                <button type="button" @click="removeUnisexImg(i)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        </template>
                        <label x-show="unisexPreviews.length < 7" class="h-24 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Añadir JPG</span>
                            <input type="file" id="unisexFiles" name="fotos_unisex[]" class="hidden" accept=".jpg,.jpeg" multiple @change="handleUnisexFiles($event)">
                        </label>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <x-input-label value="Descripción del Traje" class="font-black" />
                        <textarea name="des_unisex" rows="2" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" placeholder="Describe los componentes del traje unisex..."></textarea>
                    </div>
                    <div>
                        <x-input-label value="Tallas Existentes en Stock" class="font-black text-xs text-gray-500 uppercase" />
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach(['S', 'M', 'L', 'XL', 'Personalizado'] as $talla)
                                <label class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border cursor-pointer select-none">
                                    <input type="checkbox" name="tallas_unisex[]" value="{{ $talla }}" class="rounded text-gray-700 focus:ring-gray-700">
                                    <span class="font-bold text-sm text-gray-700">Talla {{ $talla }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-andes-oscuro hover:bg-black text-white font-black py-4 px-12 rounded-xl shadow-lg transition transform hover:-translate-y-1 uppercase tracking-widest">
                    🚀 Publicar Colección Completa
                </button>
            </div>
        </form>
    </div>

    <script>
        function trajeForm() {
            return {
                modalidad: 'variantes',
                varonPreviews: [],
                mujerPreviews: [],
                unisexPreviews: [],
                vt: new DataTransfer(),
                mt: new DataTransfer(),
                ut: new DataTransfer(),

                handleVaronFiles(e) {
                    const files = Array.from(e.target.files);
                    if(this.varonPreviews.length + files.length > 7) return alert('Máximo 7 fotos');
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
                    if(this.mujerPreviews.length + files.length > 7) return alert('Máximo 7 fotos');
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
                    if(this.unisexPreviews.length + files.length > 7) return alert('Máximo 7 fotos');
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
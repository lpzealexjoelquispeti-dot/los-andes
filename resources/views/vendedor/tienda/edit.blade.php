<x-app-layout>
    <x-slot name="header">
        Editar Datos de la Tienda
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="max-w-5xl mx-auto py-8" x-data="tiendaEditForm()">
        
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl border-t-4 border-andes-amarillo">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-black text-andes-oscuro uppercase tracking-wide">Actualizar Información</h2>
                    <a href="{{ route('vendedor.tienda.index') }}" class="text-gray-500 hover:text-andes-rojo font-bold flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Volver al Perfil
                    </a>
                </div>

                <form method="POST" action="{{ route('vendedor.tienda.update', $tienda->cod_tienda) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between items-end">
                                    <x-input-label for="nom_tie" value="Nombre Comercial" class="font-bold text-gray-700 text-base" />
                                    <span class="text-xs text-gray-400" x-bind:class="nomTie.length > 100 ? 'text-andes-rojo font-bold' : ''" x-text="nomTie.length + '/100'"></span>
                                </div>
                                <input id="nom_tie" x-model="nomTie" maxlength="100" type="text" name="nom_tie" required 
                                    class="block mt-2 w-full bg-gray-50 border-gray-300 focus:border-andes-amarillo focus:ring-andes-amarillo rounded-lg transition-colors shadow-sm"
                                    x-bind:class="{'border-andes-verde ring-1 ring-andes-verde': nomTie.length >= 3, 'border-andes-rojo ring-1 ring-andes-rojo': nomTie.length > 100}" />
                                <x-input-error :messages="$errors->get('nom_tie')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tel_tie" value="WhatsApp (8 dígitos)" class="font-bold text-gray-700 text-base" />
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">+591</span>
                                    </div>
                                    <input id="tel_tie" x-model="telTie" maxlength="8" type="text" name="tel_tie" required 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="block w-full pl-14 bg-gray-50 border-gray-300 focus:border-andes-amarillo focus:ring-andes-amarillo rounded-lg transition-colors font-mono text-lg tracking-wider shadow-sm"
                                        x-bind:class="{'border-andes-verde ring-2 ring-andes-verde bg-green-50': isTelValid, 'border-andes-rojo ring-1 ring-andes-rojo': telTie.length > 0 && !isTelValid}" />
                                    <div x-show="isTelValid" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-6 w-6 text-andes-verde" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('tel_tie')" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex justify-between items-end">
                                    <x-input-label for="dir_tie" value="Dirección Escrita" class="font-bold text-gray-700 text-base" />
                                    <span class="text-xs text-gray-400" x-bind:class="dirTie.length < 10 && dirTie.length > 0 ? 'text-andes-rojo' : ''">Mínimo 10 caracteres</span>
                                </div>
                                <input id="dir_tie" x-model="dirTie" maxlength="255" type="text" name="dir_tie" required 
                                    class="block mt-2 w-full bg-gray-50 border-gray-300 focus:border-andes-amarillo focus:ring-andes-amarillo rounded-lg transition-colors shadow-sm"
                                    x-bind:class="{'border-andes-verde ring-1 ring-andes-verde': dirTie.length >= 10}" />
                                <x-input-error :messages="$errors->get('dir_tie')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label value="Cambiar Foto de Referencia (Opcional)" class="font-bold text-gray-700 text-base" />
                                <p class="text-xs text-gray-500 mb-2">Si no seleccionas una nueva, se mantendrá tu foto actual.</p>
                                
                                <div class="mt-2 flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 relative overflow-hidden" x-bind:class="fileError ? 'border-andes-rojo bg-red-50' : ''">
                                        <img :src="imagePreview || existingImage" class="absolute inset-0 w-full h-full object-cover opacity-80" />
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10 bg-white/50 backdrop-blur-sm p-4 rounded-xl opacity-0 hover:opacity-100 transition-opacity duration-300">
                                            <svg class="w-8 h-8 mb-2 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <p class="mb-2 text-sm text-gray-800 font-bold">Haz clic para cambiar foto</p>
                                        </div>
                                        <input type="file" name="foto_ref" class="hidden" accept=".jpg, .jpeg" @change="handleFile" />
                                    </label>
                                </div>
                                <p class="text-xs font-bold text-andes-rojo mt-1" x-text="fileError"></p>
                                <x-input-error :messages="$errors->get('foto_ref')" class="mt-2" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <x-input-label value="Actualizar Ubicación en el Mapa" class="font-bold text-gray-700 text-base" />
                            <p class="text-xs text-gray-500">Haz clic en la zona permitida para mover el marcador.</p>
                            
                            <div id="mapa-edit" class="w-full h-96 rounded-lg shadow-inner border-2 border-gray-200 z-10" style="min-height: 400px;"></div>
                            
                            <div x-show="mapError" class="text-sm font-bold text-andes-rojo bg-red-50 p-3 rounded-lg" style="display: none;">
                                ❌ Ubicación inválida. Debes mantenerte en la zona de Los Andes.
                            </div>

                            <input type="hidden" name="latitud" x-model="lat" required>
                            <input type="hidden" name="longitud" x-model="lng" required>
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100">
                        <button type="submit" 
                            class="bg-andes-amarillo hover:bg-yellow-600 text-gray-900 font-black py-4 px-8 rounded-xl shadow-lg transition-all transform hover:-translate-y-1 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed"
                            x-bind:disabled="!isFormValid()">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function tiendaEditForm() {
            return {
                nomTie: '{{ old('nom_tie', $tienda->nom_tie) }}',
                dirTie: '{{ old('dir_tie', $tienda->dir_tie) }}',
                telTie: '{{ old('tel_tie', $tienda->tel_tie) }}',
                existingImage: '{{ asset('storage/' . $tienda->foto_ref) }}',
                imagePreview: null,
                fileError: '',
                lat: '{{ old('latitud', $tienda->latitud) }}',
                lng: '{{ old('longitud', $tienda->longitud) }}',
                mapError: false,
                map: null,
                marker: null,

                zonaLosAndes: {
                     norte: -16.4970,
                    sur: -16.4990,
                    este: -68.1435,
                    oeste: -68.1460
                },

                get isTelValid() {
                    return /^[67][0-9]{7}$/.test(this.telTie);
                },

                init() {
                    // LA SOLUCIÓN: setTimeout permite que el navegador termine el layout antes de cargar Leaflet
                    setTimeout(() => {
                        // 1. Inicializar mapa
                        this.map = L.map('mapa-edit').setView([this.lat, this.lng], 17);

                        // 2. Capa de mapa
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(this.map);

                        // 3. Forzar recalculo de tamaño (Vital para que no salga gris)
                        this.map.invalidateSize();

                        // 4. Dibujar zona permitida
                        let bounds = [[this.zonaLosAndes.sur, this.zonaLosAndes.oeste], [this.zonaLosAndes.norte, this.zonaLosAndes.este]];
                        L.rectangle(bounds, {color: "#007A33", weight: 2, fillOpacity: 0.1}).addTo(this.map);

                        // 5. Colocar marcador inicial
                        this.marker = L.marker([this.lat, this.lng]).addTo(this.map);

                        // 6. Lógica de clic para mover el pin
                        this.map.on('click', (e) => {
                            let clickLat = e.latlng.lat;
                            let clickLng = e.latlng.lng;

                            if (clickLat <= this.zonaLosAndes.norte && clickLat >= this.zonaLosAndes.sur && 
                                clickLng <= this.zonaLosAndes.este && clickLng >= this.zonaLosAndes.oeste) {
                                
                                this.mapError = false;
                                this.lat = clickLat.toFixed(8);
                                this.lng = clickLng.toFixed(8);
                                this.marker.setLatLng(e.latlng);
                            } else {
                                this.mapError = true;
                            }
                        });
                    }, 100); // Un pequeño retraso de 100ms es suficiente
                },

                handleFile(event) {
                    const file = event.target.files[0];
                    this.fileError = '';
                    this.imagePreview = null;
                    if (!file) return;
                    if (file.type !== 'image/jpeg') {
                        this.fileError = 'Error: Solo se permite formato .JPG';
                        event.target.value = ''; 
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        this.fileError = 'Error: El archivo supera los 2MB';
                        event.target.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                isFormValid() {
                    return this.nomTie.length >= 3 && 
                           this.dirTie.length >= 10 && 
                           this.isTelValid && 
                           this.lat !== '' && !this.mapError;
                }
            }
        }
    </script>
</x-app-layout>
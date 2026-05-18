<x-app-layout>
    <x-slot name="header">
        Registrar Nueva Tienda
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="max-w-5xl mx-auto py-8" x-data="tiendaForm()">
        
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl border-t-4 border-andes-verde">
            <div class="p-8">
                <h2 class="text-2xl font-black text-andes-oscuro mb-6 uppercase tracking-wide">Datos y Ubicación del Local</h2>

                <form method="POST" action="{{ route('vendedor.tienda.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="nom_tie" value="Nombre Comercial" />
                                <input id="nom_tie" x-model="nomTie" maxlength="100" type="text" name="nom_tie" required class="block mt-1 w-full rounded-lg border-gray-300 focus:border-andes-verde focus:ring-andes-verde" placeholder="Nombre del local" />
                            </div>

                            <div>
                                <x-input-label for="tel_tie" value="WhatsApp (8 dígitos)" />
                                <input id="tel_tie" x-model="telTie" maxlength="8" type="text" name="tel_tie" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-andes-verde focus:ring-andes-verde font-mono tracking-widest" placeholder="12345678"/>
                            </div>

                            <div>
                                <x-input-label for="dir_tie" value="Dirección Escrita" />
                                <input id="dir_tie" x-model="dirTie" maxlength="255" type="text" name="dir_tie" required placeholder="Ej. Calle Los Andes esquina..." class="block mt-1 w-full rounded-lg border-gray-300 focus:border-andes-verde focus:ring-andes-verde" />
                            </div>

                            <div>
                                <x-input-label value="Foto de Referencia del Local (Solo .JPG)" class="font-bold text-gray-700" />
                                <div class="mt-2 flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 relative overflow-hidden" x-bind:class="fileError ? 'border-andes-rojo bg-red-50' : ''">
                                        
                                        <template x-if="imagePreview">
                                            <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover opacity-80" />
                                        </template>

                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10" x-show="!imagePreview">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o arrastra la foto</p>
                                            <p class="text-xs font-bold text-andes-rojo" x-text="fileError || 'Solo formatos .jpg (Max 2MB)'"></p>
                                        </div>
                                        <input type="file" name="foto_ref" class="hidden" accept=".jpg, .jpeg" @change="handleFile" required />
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('foto_ref')" class="mt-2" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <x-input-label value="Ubicación Exacta en el Mapa" class="font-bold text-gray-700" />
                            <p class="text-xs text-gray-500">Haz clic en el mapa para colocar el marcador. <strong class="text-andes-rojo">Solo permitido en la zona de Los Andes.</strong></p>
                            
                            <div id="mapa" class="w-full h-80 rounded-lg shadow-inner border-2 border-gray-200 z-10"></div>
                            
                            <div x-show="mapError" class="text-sm font-bold text-andes-rojo bg-red-50 p-3 rounded-lg" style="display: none;">
                                ❌ Ubicación inválida. Tu tienda debe estar ubicada en el perímetro de la Calle Los Andes / Av. Buenos Aires.
                            </div>

                            <input type="hidden" name="latitud" x-model="lat" required>
                            <input type="hidden" name="longitud" x-model="lng" required>
                            <x-input-error :messages="$errors->get('latitud')" class="mt-2" />
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100">
                        <button type="submit" 
                            class="bg-andes-verde hover:bg-green-800 text-white font-black py-4 px-8 rounded-xl shadow-lg transition-all transform hover:-translate-y-1 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed"
                            x-bind:disabled="!isFormValid()">
                            Guardar Tienda y Ubicación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function tiendaForm() {
            return {
                nomTie: '{{ old('nom_tie', '') }}',
                dirTie: '{{ old('dir_tie', '') }}',
                telTie: '{{ old('tel_tie', '') }}',
                
                // Archivo y Preview
                imagePreview: null,
                fileError: '',
                
                // Mapa y Geocerca
                lat: '',
                lng: '',
                mapError: false,
                map: null,
                marker: null,

                // Bounding Box (Caja delimitadora) de la Zona de Los Andes en La Paz
                // Norte, Sur, Este, Oeste
                zonaLosAndes: {
                    norte: -16.4970,
                    sur: -16.4990,
                    este: -68.1435,
                    oeste: -68.1460
                },

                init() {
                    // Inicializar el mapa en la Paz (Garita de Lima aprox)
                    this.map = L.map('mapa').setView([-16.4950, -68.1420], 16);

                    // Cargar los mosaicos de OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // Dibujar un rectángulo semitransparente para mostrar la zona válida
                    let bounds = [[this.zonaLosAndes.sur, this.zonaLosAndes.oeste], [this.zonaLosAndes.norte, this.zonaLosAndes.este]];
                    L.rectangle(bounds, {color: "#007A33", weight: 2, fillOpacity: 0.1}).addTo(this.map);

                    // Evento al hacer clic en el mapa
                    this.map.on('click', (e) => {
                        let clickLat = e.latlng.lat;
                        let clickLng = e.latlng.lng;

                        // Validar si el clic está DENTRO de la caja de Los Andes
                        if (clickLat <= this.zonaLosAndes.norte && clickLat >= this.zonaLosAndes.sur && 
                            clickLng <= this.zonaLosAndes.este && clickLng >= this.zonaLosAndes.oeste) {
                            
                            this.mapError = false;
                            this.lat = clickLat.toFixed(8);
                            this.lng = clickLng.toFixed(8);

                            // Poner o mover el marcador
                            if (this.marker) {
                                this.marker.setLatLng(e.latlng);
                            } else {
                                this.marker = L.marker(e.latlng).addTo(this.map);
                            }
                        } else {
                            // Clic fuera de la zona
                            this.mapError = true;
                        }
                    });
                },

                handleFile(event) {
                    const file = event.target.files[0];
                    this.fileError = '';
                    this.imagePreview = null;

                    if (!file) return;

                    // Validar formato (Frontend)
                    if (file.type !== 'image/jpeg') {
                        this.fileError = 'Error: El archivo debe ser .JPG';
                        event.target.value = ''; // Limpiar el input
                        return;
                    }

                    // Validar tamaño (Max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        this.fileError = 'Error: El archivo supera los 2MB';
                        event.target.value = '';
                        return;
                    }

                    // Generar Preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                isFormValid() {
                    return this.nomTie.length >= 3 && 
                           this.dirTie.length >= 10 && 
                           /^[67][0-9]{7}$/.test(this.telTie) && 
                           this.lat !== '' && 
                           this.imagePreview !== null;
                }
            }
        }
    </script>
</x-app-layout>
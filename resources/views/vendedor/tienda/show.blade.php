<x-app-layout>
    <x-slot name="header">
        Mi Tienda: {{ $tienda->nom_tie }}
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="max-w-6xl mx-auto py-8 px-4">
        
        <div class="mb-8">
        
            @if(!$tienda->est_tie)
                <div class="bg-yellow-50 border-l-4 border-andes-amarillo p-4 rounded-r-xl shadow-sm flex items-center">
                    <div class="flex-shrink-0 bg-yellow-400 p-2 rounded-full text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-yellow-800 uppercase tracking-tight">Registro en Verificación</h3>
                        <p class="text-sm text-yellow-700">Tu tienda aún no es pública. El administrador está revisando tu información y ubicación en la calle Los Andes.</p>
                    </div>
                </div>
            @else
            
                <div class="bg-green-50 border-l-4 border-andes-verde p-4 rounded-r-xl shadow-sm flex items-center">
                    <div class="flex-shrink-0 bg-andes-verde p-2 rounded-full text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-green-800 uppercase tracking-tight">Tienda Verificada</h3>
                        <p class="text-sm text-green-700">Tu negocio es visible para todos los fraternos y clientes.</p>
                    </div>
                </div>
            @endif
            
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-2 rounded-2xl shadow-lg border border-gray-100">
                    <img src="{{ asset('storage/' . $tienda->foto_ref) }}" 
                         alt="{{ $tienda->nom_tie }}" 
                         class="w-full h-64 object-cover rounded-xl shadow-inner">
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Contacto Directo</h4>
                    <a href="https://wa.me/591{{ $tienda->tel_tie }}" target="_blank" 
                       class="flex items-center justify-center gap-3 w-full py-3 bg-andes-verde text-white font-bold rounded-xl hover:bg-green-800 transition shadow-md">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        +591 {{ $tienda->tel_tie }}
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-black text-andes-oscuro uppercase tracking-wider">Detalles del Negocio</h3>
                        <a href="{{ route('vendedor.tienda.edit', $tienda->cod_tienda) }}" 
                           class="bg-andes-rojo text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition transform hover:-translate-y-0.5">
                            Editar Datos
                        </a>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Dirección en el Sector</p>
                            <p class="text-lg text-gray-800 font-medium">{{ $tienda->dir_tie }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ubicación Geo-referenciada</p>
                            <div id="mapa-ver" class="w-full h-80 rounded-xl border-2 border-gray-100 shadow-inner z-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lat = {{ $tienda->latitud }};
            const lng = {{ $tienda->longitud }};
            
            // Inicializar mapa centrado en la tienda
            const map = L.map('mapa-ver', {
                dragging: false,      // Desactivar arrastre para que sea informativo
                scrollWheelZoom: false // Desactivar zoom con rueda
            }).setView([lat, lng], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Agregar marcador personalizado
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $tienda->nom_tie }}</b>")
                .openPopup();
        });
    </script>
</x-app-layout>
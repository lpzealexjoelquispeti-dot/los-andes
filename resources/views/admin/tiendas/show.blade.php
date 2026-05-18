<x-app-layout>
    <x-slot name="header">
        Verificación de Solicitud: {{ $tienda->nom_tie }}
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-andes-rojo">
                    <div class="p-6 text-center bg-gray-50 border-b">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Datos del Dueño</h3>
                        
                        @if($tienda->vendedor->foto_perfil)
                            <img src="{{ asset('storage/' . $tienda->vendedor->foto_perfil) }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="w-32 h-32 rounded-full mx-auto bg-andes-oscuro text-white flex items-center justify-center text-4xl font-black shadow-lg">
                                {{ substr($tienda->vendedor->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <h2 class="mt-4 text-xl font-black text-gray-800">{{ $tienda->vendedor->name }} {{ $tienda->vendedor->ap_pat }}</h2>
                        <p class="text-sm text-andes-rojo font-bold italic">Vendedor Registrado</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Correo Electrónico</p>
                            <p class="text-sm font-medium text-gray-700">{{ $tienda->vendedor->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Miembro desde</p>
                            <p class="text-sm font-medium text-gray-700">{{ $tienda->vendedor->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-andes-verde">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-2xl font-black text-andes-oscuro uppercase">Información del Local</h3>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.tiendas.aprobar', $tienda->cod_tienda) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="bg-andes-verde text-white px-6 py-2 rounded-xl font-black hover:bg-green-700 transition">APROBAR</button>
                            </form>
                            <form action="{{ route('admin.tiendas.rechazar', $tienda->cod_tienda) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="bg-andes-rojo text-white px-6 py-2 rounded-xl font-black hover:bg-red-700 transition">RECHAZAR</button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <img src="{{ asset('storage/' . $tienda->foto_ref) }}" class="w-full h-48 object-cover rounded-xl shadow-inner border">
                        <div class="space-y-4">
                            <p><strong>Tienda:</strong> {{ $tienda->nom_tie }}</p>
                            <p><strong>Dirección:</strong> {{ $tienda->dir_tie }}</p>
                            <p><strong>WhatsApp:</strong> {{ $tienda->tel_tie }}</p>
                        </div>
                    </div>

                    <h4 class="text-sm font-black text-gray-400 uppercase mb-3">Verificación Geográfica</h4>
                    <div id="mapa-verificacion" class="w-full h-64 rounded-xl border-2 border-gray-100 z-0"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('mapa-verificacion').setView([{{ $tienda->latitud }}, {{ $tienda->longitud }}], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([{{ $tienda->latitud }}, {{ $tienda->longitud }}]).addTo(map).bindPopup("Ubicación declarada").openPopup();
        });
    </script>
</x-app-layout>
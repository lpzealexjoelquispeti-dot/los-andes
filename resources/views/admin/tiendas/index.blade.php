<x-app-layout>
    <x-slot name="header">
        Aprobación de Tiendas Pendientes
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">
        
        @if(session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border-l-8 border-andes-verde text-green-900 rounded-xl shadow-md transform transition-all animate-bounce">
        <svg class="w-6 h-6 text-andes-verde" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-black uppercase tracking-tight">{{ session('success') }}</span>
    </div>
@endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-andes-rojo text-red-800 font-bold rounded-r-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-black text-andes-oscuro uppercase tracking-wider text-center md:text-left">
                    Solicitudes de Registro ({{ $tiendasPendientes->count() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-andes-oscuro text-white uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 font-bold">Local</th>
                            <th class="px-6 py-4 font-bold">Dueño / Contacto</th>
                            <th class="px-6 py-4 font-bold">Ubicación</th>
                            <th class="px-6 py-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tiendasPendientes as $tienda)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ asset('storage/' . $tienda->foto_ref) }}" class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-200">
                                        <div>
                                            <a href="{{ route('admin.tiendas.show', $tienda->cod_tienda) }}" class="font-black text-blue-600 hover:underline text-base">
    {{ $tienda->nom_tie }}
</a>
                                            <p class="text-xs text-gray-500 font-medium">{{ $tienda->dir_tie }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="font-bold text-gray-700">{{ $tienda->vendedor->name }} {{ $tienda->vendedor->ap_pat }}</p>
                                        <p class="text-andes-verde font-bold">Cel: {{ $tienda->tel_tie }}</p>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="https://www.google.com/maps?q={{ $tienda->latitud }},{{ $tienda->longitud }}" target="_blank" 
                                       class="inline-flex items-center gap-1 text-xs font-bold bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full hover:bg-blue-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        VER MAPA
                                    </a>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-3">
                                        <form action="{{ route('admin.tiendas.aprobar', $tienda->cod_tienda) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="bg-andes-verde text-white p-2 rounded-lg hover:bg-green-700 shadow-md transition transform hover:scale-110" title="Aprobar Tienda">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.tiendas.rechazar', $tienda->cod_tienda) }}" method="POST" onsubmit="return confirm('¿Estás seguro de rechazar esta tienda? Se eliminará el registro.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-andes-rojo text-white p-2 rounded-lg hover:bg-red-700 shadow-md transition transform hover:scale-110" title="Rechazar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold italic">
                                    No hay tiendas pendientes de aprobación por ahora.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">Alquileres</x-slot>

    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-xl font-black text-gray-900 uppercase">Gestion de alquileres</h3>
            <p class="text-sm text-gray-500">Control de reservas, entregas, devoluciones y sanciones.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Codigo</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Cliente</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Traje</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Fechas</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($alquileres as $alquiler)
                        <tr>
                            <td class="px-5 py-4 font-black">#{{ $alquiler->cod_alquiler }}</td>
                            <td class="px-5 py-4">{{ $alquiler->cliente?->name }}</td>
                            <td class="px-5 py-4">{{ $alquiler->unidadFisica?->traje?->nom_traje }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500">{{ $alquiler->fec_salida->format('d/m/Y') }} - {{ $alquiler->fec_retorno_prev->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-black uppercase">{{ $alquiler->est_alquiler }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('vendedor.alquileres.show', $alquiler) }}" class="px-4 py-2 rounded-xl bg-andes-oscuro text-white text-xs font-black uppercase">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500 font-bold">No hay alquileres registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5">
            {{ $alquileres->links() }}
        </div>
    </div>
</x-app-layout>

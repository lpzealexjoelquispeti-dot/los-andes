<x-app-layout>
    <x-slot name="header">Detalle de alquiler</x-slot>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <p class="text-xs font-black uppercase text-gray-400">Alquiler #{{ $alquiler->cod_alquiler }}</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $alquiler->unidadFisica?->traje?->nom_traje }}</h3>
                    <p class="text-gray-500">{{ $alquiler->cliente?->name }} - {{ $alquiler->cliente?->email }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-black uppercase">{{ $alquiler->est_alquiler }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-black text-gray-400 uppercase">Evento</p>
                    <p class="font-bold">{{ $alquiler->evento?->nom_evento }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-black text-gray-400 uppercase">Fechas</p>
                    <p class="font-bold">{{ $alquiler->fec_salida->format('d/m/Y') }} - {{ $alquiler->fec_retorno_prev->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-black text-gray-400 uppercase">Monto</p>
                    <p class="font-bold">Bs. {{ number_format($alquiler->monto_total, 2) }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($alquiler->est_alquiler === 'Reservado')
                    <form method="POST" action="{{ route('vendedor.alquileres.entregar', $alquiler) }}" data-confirm="Marcar como entregado?">
                        @csrf
                        @method('PATCH')
                        <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-black uppercase">Entregar</button>
                    </form>
                @endif

                @if(in_array($alquiler->est_alquiler, ['Reservado', 'Entregado', 'En Mora'], true))
                    <form method="POST" action="{{ route('vendedor.alquileres.cancelar', $alquiler) }}" data-confirm="Cancelar este alquiler?">
                        @csrf
                        @method('PATCH')
                        <button class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-black uppercase">Cancelar</button>
                    </form>
                @endif
            </div>

            @if(in_array($alquiler->est_alquiler, ['Entregado', 'En Mora'], true))
                <form method="POST" action="{{ route('vendedor.alquileres.devolver', $alquiler) }}" class="rounded-2xl border border-gray-100 p-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <h4 class="font-black text-gray-900 uppercase">Registrar devolucion</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="date" name="fec_retorno_real" value="{{ now()->format('Y-m-d') }}" class="rounded-xl border-gray-200">
                        <select name="estado_fisico" class="rounded-xl border-gray-200">
                            <option>Nuevo</option>
                            <option>Buen Estado</option>
                            <option>Desgastado</option>
                            <option>En Reparación</option>
                        </select>
                    </div>
                    <textarea name="observaciones" rows="3" class="w-full rounded-xl border-gray-200" placeholder="Observaciones de la prenda"></textarea>
                    <button class="px-4 py-2 rounded-xl bg-andes-verde text-white text-xs font-black uppercase">Guardar devolucion</button>
                </form>
            @endif
        </section>

        <aside class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h4 class="font-black text-gray-900 uppercase">Sanciones</h4>

            <div class="space-y-3">
                @forelse($alquiler->sanciones as $sancion)
                    <div class="rounded-xl bg-red-50 border border-red-100 p-4">
                        <div class="flex justify-between gap-3">
                            <p class="font-black text-red-800">{{ $sancion->tipo_sancion }}</p>
                            <p class="font-black text-red-800">Bs. {{ number_format($sancion->monto_sancion, 2) }}</p>
                        </div>
                        <p class="text-sm text-red-700 mt-1">{{ $sancion->descripcion }}</p>
                        @if($sancion->pagada)
                            <span class="inline-flex mt-2 text-xs font-black text-emerald-700">Pagada</span>
                        @else
                            <form method="POST" action="{{ route('vendedor.sanciones.pagar', $sancion) }}" data-confirm="Marcar sancion como pagada?">
                                @csrf
                                @method('PATCH')
                                <button class="mt-3 px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold">Marcar pagada</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No hay sanciones registradas.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('vendedor.alquileres.sanciones.store', $alquiler) }}" class="space-y-3 border-t border-gray-100 pt-5">
                @csrf
                <select name="tipo_sancion" class="w-full rounded-xl border-gray-200">
                    <option value="Retraso">Retraso</option>
                    <option value="Daño">Daño</option>
                    <option value="Perdida">Perdida</option>
                    <option value="Limpieza">Limpieza</option>
                </select>
                <input type="number" step="0.01" min="0.01" name="monto_sancion" class="w-full rounded-xl border-gray-200" placeholder="Monto Bs.">
                <textarea name="descripcion" rows="3" class="w-full rounded-xl border-gray-200" placeholder="Descripcion"></textarea>
                <button class="w-full px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-black uppercase">Registrar sancion</button>
            </form>
        </aside>
    </div>
</x-app-layout>

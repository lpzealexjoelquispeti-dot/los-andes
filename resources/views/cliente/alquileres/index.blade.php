<x-app-layout>
    <x-slot name="header">Mis alquileres</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-black text-gray-900 uppercase">Historial de alquileres</h3>
                    <p class="text-sm text-gray-500">Reservas, devoluciones, sanciones y valoraciones.</p>
                </div>
                <a href="{{ route('public.catalogo.index') }}" class="px-4 py-2 rounded-xl bg-andes-verde text-white text-xs font-black uppercase">
                    Explorar trajes
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($alquileres as $alquiler)
                    @php
                        $traje = $alquiler->unidadFisica?->traje;
                        $valoracion = $traje?->valoraciones?->firstWhere('cod_usuario_val', auth()->id());
                    @endphp
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-5">
                        <div class="lg:col-span-5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Alquiler #{{ $alquiler->cod_alquiler }}</p>
                            <h4 class="text-lg font-black text-gray-900">{{ $traje?->nom_traje ?? 'Traje no disponible' }}</h4>
                            <p class="text-sm text-gray-500">{{ $traje?->tienda?->nom_tie ?? 'Tienda sin datos' }}</p>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ $alquiler->fec_salida->format('d/m/Y') }} al {{ $alquiler->fec_retorno_prev->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="lg:col-span-3">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-black uppercase
                                {{ $alquiler->est_alquiler === 'Devuelto' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $alquiler->est_alquiler === 'En Mora' ? 'bg-red-100 text-red-700' : '' }}
                                {{ in_array($alquiler->est_alquiler, ['Reservado', 'Entregado'], true) ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $alquiler->est_alquiler === 'Cancelado' ? 'bg-gray-100 text-gray-600' : '' }}">
                                {{ $alquiler->est_alquiler }}
                            </span>
                            <p class="text-sm text-gray-500 mt-3">Monto: <strong>Bs. {{ number_format($alquiler->monto_total, 2) }}</strong></p>
                            <p class="text-sm text-gray-500">Garantia: Bs. {{ number_format($alquiler->garantia, 2) }}</p>
                        </div>

                        <div class="lg:col-span-4 space-y-3">
                            @if($alquiler->sanciones->count())
                                <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-sm text-red-700">
                                    Sanciones pendientes: Bs. {{ number_format($alquiler->sanciones->where('pagada', false)->sum('monto_sancion'), 2) }}
                                </div>
                            @endif

                            @if($alquiler->est_alquiler === 'Reservado')
                                <form method="POST" action="{{ route('cliente.alquileres.cancelar', $alquiler) }}" data-confirm="Cancelar esta reserva?">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-black uppercase">Cancelar reserva</button>
                                </form>
                            @endif

                            @if($alquiler->est_alquiler === 'Devuelto')
                                <form method="POST" action="{{ route('cliente.alquileres.valorar', $alquiler) }}" class="rounded-xl bg-gray-50 p-4 space-y-3">
                                    @csrf
                                    <div class="flex gap-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer text-sm font-black">
                                                <input type="radio" name="puntuacion" value="{{ $i }}" @checked(($valoracion->puntuacion ?? 5) == $i)>
                                                {{ $i }}
                                            </label>
                                        @endfor
                                    </div>
                                    <textarea name="comentario" rows="2" class="w-full rounded-xl border-gray-200 text-sm" placeholder="Comentario opcional">{{ $valoracion->comentario ?? '' }}</textarea>
                                    <button class="px-4 py-2 rounded-xl bg-andes-oscuro text-white text-xs font-black uppercase">
                                        {{ $valoracion ? 'Actualizar valoracion' : 'Guardar valoracion' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-500 font-bold">Todavia no tienes alquileres registrados.</div>
                @endforelse
            </div>
        </div>

        {{ $alquileres->links() }}
    </div>
</x-app-layout>

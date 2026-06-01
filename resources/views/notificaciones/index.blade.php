<x-app-layout>
    <x-slot name="header">Notificaciones</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-gray-900 uppercase">Centro de notificaciones</h3>
                    <p class="text-sm text-gray-500">Trajes nuevos, disponibilidad, alquileres y sanciones.</p>
                </div>
                <form method="POST" action="{{ route('notificaciones.marcar-todas') }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-4 py-2 rounded-xl bg-andes-oscuro text-white text-xs font-black uppercase">Marcar todas</button>
                </form>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($notificaciones as $notificacion)
                    <div class="p-5 flex items-start justify-between gap-4 {{ $notificacion->leido ? 'bg-white' : 'bg-yellow-50/60' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $notificacion->tipo === 'alerta' ? 'bg-red-500' : ($notificacion->tipo === 'exito' ? 'bg-emerald-500' : 'bg-blue-500') }}"></span>
                                <h4 class="font-black text-gray-900">{{ $notificacion->titulo }}</h4>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
    @if(str_contains($notificacion->mensaje, 'Motivo:'))
        {{-- Separa el texto informativo del motivo real --}}
        <span>{{ Str::before($notificacion->mensaje, 'Motivo:') }}</span>
        
        <blockquote class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-xs font-medium text-red-700">
            <span class="font-black uppercase tracking-wider block text-[10px] text-red-500 mb-0.5">Motivo del rechazo:</span>
            "{{ Str::after($notificacion->mensaje, 'Motivo:') }}"
        </blockquote>
    @else
        {{ $notificacion->mensaje }}
    @endif
</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notificacion->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            @unless($notificacion->leido)
                                <form method="POST" action="{{ route('notificaciones.leida', $notificacion) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold">Leida</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('notificaciones.destroy', $notificacion) }}" data-confirm="Eliminar esta notificacion?">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-2 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-500 font-bold">No tienes notificaciones.</div>
                @endforelse
            </div>
        </div>

        {{ $notificaciones->links() }}
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">Notificaciones</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="relative bg-gradient-to-r from-gray-50 to-white px-6 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-6 h-6 text-andes-oscuro" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        Centro de Notificaciones
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">Controla las alertas de trajes nuevos, disponibilidad, alquileres y sanciones.</p>
                </div>
                
                <form method="POST" action="{{ route('notificaciones.marcar-todas') }}" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-andes-oscuro text-white text-xs font-black uppercase tracking-wider transition-all duration-300 hover:bg-black hover:shadow-md active:scale-95">
                        <svg class="w-4 h-4 text-andes-amarillo" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Marcar todas como leídas
                    </button>
                </form>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($notificaciones as $notificacion)
                    <div class="p-5 flex flex-col sm:flex-row items-start justify-between gap-4 transition-all duration-300 relative {{ $notificacion->leido ? 'bg-white' : 'bg-gradient-to-r from-yellow-50/40 to-transparent border-l-4 border-andes-amarillo' }}">
                        
                        <div class="flex items-start gap-4 flex-1 w-full">
                            <div class="shrink-0 mt-0.5">
                                @if($notificacion->tipo === 'alerta')
                                    <div class="p-2 rounded-xl bg-red-50 text-red-600 border border-red-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    </div>
                                @elseif($notificacion->tipo === 'exito')
                                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                    </div>
                                @else
                                    <div class="p-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063 1.06l-.041.02a.75.75 0 01-1.063-1.06zm0 3.5l.041-.02a.75.75 0 111.063 1.06l-.041.02a.75.75 0 01-1.063-1.06zM12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $notificacion->titulo }}</h4>
                                    @unless($notificacion->leido)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-andes-amarillo text-andes-oscuro uppercase tracking-wider shadow-sm animate-pulse">
                                            Nuevo
                                        </span>
                                    @endunless
                                </div>
                                
                                <div class="text-sm text-gray-600 mt-1.5 space-y-2">
                                    @if(str_contains($notificacion->mensaje, 'Motivo:'))
                                        <p class="font-medium text-gray-700">{{ Str::before($notificacion->mensaje, 'Motivo:') }}</p>
                                        <div class="p-3 bg-red-50/80 border-l-4 border-andes-rojo rounded-r-xl shadow-sm">
                                            <span class="font-black uppercase tracking-wider block text-[10px] text-andes-rojo mb-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/></svg>
                                                Motivo del rechazo:
                                            </span>
                                            <p class="text-xs font-semibold text-red-800 italic">"{{ trim(Str::after($notificacion->mensaje, 'Motivo:')) }}"</p>
                                        </div>
                                    @else
                                        <p>{{ $notificacion->mensaje }}</p>
                                    @endif
                                </div>
                                
                                <p class="text-xs text-gray-400 mt-2.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $notificacion->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex sm:flex-col gap-2 w-full sm:w-auto shrink-0 justify-end mt-2 sm:mt-0 pt-3 sm:pt-0 border-t border-gray-100 sm:border-0">
                            @unless($notificacion->leido)
                                <form method="POST" action="{{ route('notificaciones.leida', $notificacion) }}" class="w-full sm:w-auto">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all duration-200 active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        Leída
                                    </button>
                                </form>
                            @endunless
                            
                            <form method="POST" action="{{ route('notificaciones.destroy', $notificacion) }}" data-confirm="¿Estás seguro de eliminar esta notificación?" class="w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 hover:bg-red-50 text-gray-600 hover:text-andes-rojo border border-gray-200 hover:border-red-200 text-xs font-bold shadow-sm transition-all duration-200 active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 003.834.124m-6.2 0a23.848 23.848 0 005.454-1.31M14.857 17.082a23.848 23.848 0 005.454-1.31M9.143 17.082a24.249 24.249 0 01-3.834.124m6.2 0a24.253 24.253 0 003.834-.124m-3.834.124a24.253 24.253 0 01-3.834-.124m6.2 0a24.248 24.248 0 013.834.124m-12 0a23.848 23.848 0 01-5.454-1.31m15.351 1.31a23.848 23.848 0 01-5.454-1.31M18 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg">Bandeja limpia</h4>
                        <p class="text-sm text-gray-400 max-w-xs mx-auto mt-1">No tienes notificaciones pendientes en este momento. ¡Buen trabajo!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-4">
            {{ $notificaciones->links() }}
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">Administración de Usuarios</x-slot>

    <div class="max-w-7xl mx-auto py-10 px-6" x-data="{ openDetail: false, currentUsuario: {} }">

        {{-- ENCABEZADO --}}
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tighter italic">Usuarios Maestros</h2>
                <p class="text-andes-verde text-[10px] font-black uppercase tracking-[0.3em]">Gestión de Accesos e Integridad</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="bg-andes-verde text-white px-8 py-4 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Usuario
            </a>
        </div>

        {{-- ALERTAS DE SESIÓN (manejadas por SweetAlert2 vía JS al final) --}}
        @if (session('status'))
            <span id="session-status" data-status="{{ session('status') }}" class="hidden"></span>
        @endif
        @if (session('error'))
            <span id="session-error" data-error="{{ session('error') }}" class="hidden"></span>
        @endif

        {{-- TABLA DE DATOS --}}
        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Nombre / Identificador</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Rol Asignado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($usuarios as $usuario)
                    <tr class="{{ $usuario->trashed() ? 'bg-gray-50/50 opacity-60' : 'hover:bg-gray-50/30' }} transition-colors">

                        {{-- ESTADO --}}
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $usuario->trashed() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $usuario->trashed() ? 'Inactivo' : 'Activo' }}
                            </span>
                        </td>

                        {{-- NOMBRE + EMAIL --}}
                        <td class="px-8 py-5">
                            <div class="font-bold text-gray-800 uppercase text-sm">{{ $usuario->name }}</div>
                            <div class="text-[11px] text-gray-400 normal-case font-medium mt-0.5">{{ $usuario->email }}</div>
                        </td>

                        {{-- ROL --}}
                        <td class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $usuario->getRoleNames()->implode(', ') }}</td>

                        {{-- ACCIONES --}}
                        <td class="px-8 py-5 text-right flex justify-end gap-2">

                            {{-- BOTÓN OJITO: abre modal con datos del usuario --}}
                           {{-- BOTÓN OJITO MODIFICADO CON SPATIE --}}
                        <button
                            type="button"
                            @click="currentUsuario = {
                                name: '{{ addslashes($usuario->name) }}',
                                email: '{{ addslashes($usuario->email) }}',
                                rol: '{{ addslashes($usuario->getRoleNames()->implode(', ')) }}',
                                deleted_at: '{{ $usuario->deleted_at }}'
                            }; openDetail = true"
                            class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:scale-110 transition shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>

                            @if(!$usuario->trashed())
                                {{-- EDITAR --}}
                                <a href="{{ route('admin.users.edit', $usuario->id) }}"
                                   class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:scale-110 transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- DAR DE BAJA (SweetAlert2) --}}
                                @if(auth()->id() !== $usuario->id)
                                    <form
                                        action="{{ route('admin.users.destroy', $usuario->id) }}"
                                        method="POST"
                                        class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                onclick="confirmarBaja(this)"
                                                class="p-2 bg-red-50 text-red-600 rounded-xl hover:scale-110 transition shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- RESTAURAR (SweetAlert2) --}}
                                <form
                                    action="{{ route('admin.users.restore', $usuario->id) }}"
                                    method="POST"
                                    class="restore-form">
                                    @csrf @method('PATCH')
                                    <button type="button"
                                            onclick="confirmarRestauro(this)"
                                            class="p-2 bg-green-50 text-andes-verde rounded-xl hover:scale-110 transition shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-6">
            {{ $usuarios->links() }}
        </div>

        {{-- MODAL DE DETALLES --}}
        <div x-show="openDetail"
             x-cloak
             style="display:none"
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-6">

            <div class="bg-white w-full max-w-2xl rounded-[2rem] overflow-hidden shadow-2xl"
                 @click.away="openDetail=false">

                {{-- CABECERA CON AVATAR --}}
                <div class="h-48 bg-gradient-to-br from-andes-verde/10 to-andes-verde/30 relative flex items-center justify-center overflow-hidden">
                    <div class="w-24 h-24 rounded-full bg-andes-verde/20 border-4 border-andes-verde/40 flex items-center justify-center">
                        <svg class="w-12 h-12 text-andes-verde/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    <button @click="openDetail=false"
                            class="absolute top-5 right-5 bg-black/40 text-white p-2 rounded-full hover:bg-red-500 transition">
                        ✕
                    </button>
                </div>

                {{-- CONTENIDO --}}
                <div class="p-10">

                    <div class="flex gap-3 mb-4">
                        <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-[10px] font-black uppercase"
                              x-text="currentUsuario.rol">
                        </span>
                        <template x-if="currentUsuario.deleted_at">
                            <span class="bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">
                                Inactivo
                            </span>
                        </template>
                    </div>

                    <h2 class="text-4xl font-black text-gray-800 uppercase mb-2"
                        x-text="currentUsuario.name">
                    </h2>

                    <p class="text-gray-400 text-sm font-medium mb-6"
                       x-text="currentUsuario.email">
                    </p>

                    <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">
                        Rol del Sistema
                    </h4>

                    <p class="text-gray-500 leading-relaxed italic"
                       x-text="currentUsuario.rol ? currentUsuario.rol : 'Sin rol asignado.'">
                    </p>

                    <div class="mt-8 pt-6 border-t border-gray-100 text-right">
                        <button @click="openDetail=false"
                                class="text-gray-400 font-black uppercase text-[10px] tracking-widest hover:text-andes-verde">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirmar baja lógica
        function confirmarBaja(btn) {
            const form = btn.closest('form');
            Swal.fire({
                title: '¿Dar de baja?',
                text: 'El usuario quedará inactivo. Podrás restaurarlo después.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, dar de baja',
                cancelButtonText: 'Cancelar',
                borderRadius: '1.5rem',
                customClass: {
                    popup: 'rounded-3xl font-sans',
                    confirmButton: 'rounded-xl font-black uppercase text-xs',
                    cancelButton: 'rounded-xl font-black uppercase text-xs',
                }
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        }

        // Confirmar restauración
        function confirmarRestauro(btn) {
            const form = btn.closest('form');
            Swal.fire({
                title: '¿Restaurar usuario?',
                text: 'El usuario volverá a estar activo en el sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007a3d',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl font-sans',
                    confirmButton: 'rounded-xl font-black uppercase text-xs',
                    cancelButton: 'rounded-xl font-black uppercase text-xs',
                }
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        }

    </script>

</x-app-layout>
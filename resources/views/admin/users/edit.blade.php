<x-app-layout>
    <x-slot name="header">Editar Usuario</x-slot>

    <div class="max-w-3xl mx-auto py-10 px-6">

        {{-- ENCABEZADO --}}
        <div class="mb-10">
            <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tighter italic">Modificar Usuario</h2>
            <p class="text-andes-verde text-[10px] font-black uppercase tracking-[0.3em]">Edición de Acceso e Identidad</p>
        </div>

        {{-- ALERTAS DE SESIÓN INTERNAS --}}
        @if (session('status') && session('status') === 'clave-regenerada')
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl text-xs font-black uppercase tracking-wide">
                🔄 Éxito: Nueva contraseña generada y despachada al buzón del usuario.
            </div>
        @endif

        {{-- TARJETA PRINCIPAL DEL FORMULARIO ORDINARIO --}}
        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden mb-8">
            <div class="h-2 bg-andes-verde w-full"></div>

            <div class="p-10">
                <p class="text-xs text-gray-400 font-medium mb-8 leading-relaxed">
                    Modifica los datos base del usuario. Para resguardar la seguridad del sistema, la contraseña ya no se edita manualmente desde este formulario.
                </p>

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- GRID 1: NOMBRE + EMAIL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nombre Completo</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all" />
                            @error('name') <p class="mt-2 text-[11px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Correo Electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all" />
                            @error('email') <p class="mt-2 text-[11px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- GRID 2: APELLIDOS (Alineado con tus restricciones PostgreSQL) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="ap_pat" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Apellido Paterno</label>
                            <input id="ap_pat" name="ap_pat" type="text" value="{{ old('ap_pat', $user->ap_pat) }}" required class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all" />
                            @error('ap_pat') <p class="mt-2 text-[11px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="ap_mat" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Apellido Materno <span class="normal-case font-normal text-gray-300">(Opcional)</span></label>
                            <input id="ap_mat" name="ap_mat" type="text" value="{{ old('ap_mat', $user->ap_mat) }}" class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all" />
                        </div>
                    </div>

                    {{-- ROL (Spatie) --}}
                    <div>
                        <label for="rol" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Rol del Sistema (Spatie)</label>
                        <select id="rol" name="rol" class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all appearance-none cursor-pointer">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('rol', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ACCIONES DE ENVÍO --}}
                    <div class="border-t border-gray-100 pt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-7 py-3.5 rounded-2xl border border-gray-200 text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-andes-verde text-white px-8 py-3.5 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🛡️ SECCIÓN PERIFÉRICA: ZONA DE SEGURIDAD (Restablecer Acceso) --}}
        <div class="bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200 p-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-sm">
            <div class="max-w-md">
                <h4 class="text-sm font-black text-gray-700 uppercase tracking-tight">Zona de Control de Credenciales</h4>
                <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                    ¿El usuario olvidó su clave de acceso? Presiona el botón lateral para forzar el vaciado de credenciales. El sistema inyectará una contraseña segura aleatoria y le notificará las nuevas instrucciones a su buzón.
                </p>
            </div>

            {{-- Formulario exclusivo con SweetAlert2 para la clave --}}
            <form action="{{ route('admin.users.password.regenerate', $user->id) }}" method="POST" id="form-regenerar-clave">
                @csrf
                @method('PATCH')
                <button type="button" onclick="confirmarRegeneracion()" class="bg-gray-800 hover:bg-black text-white px-6 py-4 rounded-2xl font-black uppercase text-[11px] tracking-wider transition shadow-md whitespace-nowrap">
                    🔄 Restablecer Clave
                </button>
            </form>
        </div>

    </div>

    {{-- SWEETALERT2 INTEGRADO --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarRegeneracion() {
            Swal.fire({
                title: '¿Restablecer contraseña?',
                text: 'Se generará una clave temporal automatizada y se le enviará un correo con las nuevas instrucciones de onboarding de inmediato.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e293b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, forzar cambio',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl font-sans',
                    confirmButton: 'rounded-xl font-black uppercase text-xs px-4 py-2',
                    cancelButton: 'rounded-xl font-black uppercase text-xs px-4 py-2',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-regenerar-clave').submit();
                }
            });
        }
    </script>
</x-app-layout>
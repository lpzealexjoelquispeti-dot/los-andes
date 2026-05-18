<x-app-layout>
    <x-slot name="header">Registrar Usuario</x-slot>

    <div class="max-w-3xl mx-auto py-10 px-6">

        {{-- ENCABEZADO idéntico al index --}}
        <div class="mb-10">
            <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tighter italic">Nuevo Usuario</h2>
            <p class="text-andes-verde text-[10px] font-black uppercase tracking-[0.3em]">Registro de Acceso e Identidad</p>
        </div>

        {{-- TARJETA DEL FORMULARIO --}}
        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">

            {{-- FRANJA SUPERIOR DECORATIVA --}}
            <div class="h-2 bg-andes-verde w-full"></div>

            <div class="p-10">

                <p class="text-xs text-gray-400 font-medium mb-8 leading-relaxed">
                    El sistema generará una clave híbrida aleatoria segura y la enviará automáticamente
                    por correo electrónico al destinatario registrado.
                </p>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    {{-- NOMBRE --}}
                    <div>
                        <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Nombre Completo
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autocomplete="off"
                            placeholder="Ej. Juan Carlos Mamani"
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde
                                   placeholder:text-gray-300 transition-all"
                        />
                        @error('name')
                            <p class="mt-2 text-[11px] font-bold text-red-500 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                        {{-- Pega esto justo debajo del bloque de NOMBRE COMPLETO --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- APELLIDO PATERNO --}}
    <div>
        <label for="ap_pat" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
            Apellido Paterno
        </label>
        <input
            id="ap_pat"
            name="ap_pat"
            type="text"
            value="{{ old('ap_pat') }}"
            required
            placeholder="Ej. Mamani"
            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all"
        />
        @error('ap_pat')
            <p class="mt-2 text-[11px] font-bold text-red-500 uppercase tracking-wide">{{ $message }}</p>
        @enderror
    </div>

    {{-- APELLIDO MATERNO --}}
    <div>
        <label for="ap_mat" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
            Apellido Materno <span class="normal-case font-normal text-gray-300">(Opcional)</span>
        </label>
        <input
            id="ap_mat"
            name="ap_mat"
            type="text"
            value="{{ old('ap_mat') }}"
            placeholder="Ej. Quispe"
            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde transition-all"
        />
        @error('ap_mat')
            <p class="mt-2 text-[11px] font-bold text-red-500 uppercase tracking-wide">{{ $message }}</p>
        @enderror
    </div>
</div>
                    </div>

                    {{-- EMAIL --}}
                    <div>
                        <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Correo Electrónico <span class="normal-case font-normal">(Para envío de accesos)</span>
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="usuario@ejemplo.com"
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde
                                   placeholder:text-gray-300 transition-all"
                        />
                        @error('email')
                            <p class="mt-2 text-[11px] font-bold text-red-500 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ROL (Spatie) --}}
                    <div>
                        <label for="rol" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Nivel de Privilegios <span class="normal-case font-normal">(Rol del Sistema)</span>
                        </label>
                        <select
                            id="rol"
                            name="rol"
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-andes-verde/40 focus:border-andes-verde
                                   transition-all appearance-none cursor-pointer"
                        >
                            <option value="" disabled {{ old('rol') ? '' : 'selected' }} class="text-gray-300">
                                — Seleccionar rol —
                            </option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('rol') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol')
                            <p class="mt-2 text-[11px] font-bold text-red-500 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SEPARADOR --}}
                    <div class="border-t border-gray-100 pt-6 flex justify-end gap-3">

                        <a href="{{ route('admin.users.index') }}"
                           class="px-7 py-3.5 rounded-2xl border border-gray-200 text-gray-400 text-xs font-black uppercase tracking-widest
                                  hover:bg-gray-50 transition-all">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="bg-andes-verde text-white px-8 py-3.5 rounded-2xl font-black uppercase text-xs
                                       shadow-lg hover:scale-105 transition-all flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Guardar y Enviar Accesos
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>
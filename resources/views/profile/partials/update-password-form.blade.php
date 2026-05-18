<section>
    <header>
        <h2 class="text-xl font-semibold text-white">
            {{ __('Actualizar Contraseña') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            {{ __('Usa una contraseña segura para proteger tu acceso al sistema.') }}
        </p>
    </header>

    {{-- Inicializamos los estados de visibilidad con Alpine.js --}}
    <form method="post" action="{{ route('password.update') }}" 
          class="mt-6 space-y-6" 
          x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="col-span-full md:col-span-1">
                <x-input-label for="current_password" :value="__('Contraseña Actual')" class="text-gray-300" />
                <div class="relative mt-1">
                    <x-text-input id="current_password" name="current_password" 
                        ::type="showCurrent ? 'text' : 'password'" 
                        class="block w-full bg-andes-dark border-gray-700 text-black pr-10 focus:border-andes-rojo focus:ring-andes-rojo" 
                        autocomplete="current-password" />
                    
                    {{-- Botón del Ojito --}}
                    <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-andes-rojo transition">
                        <svg x-show="!showCurrent" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showCurrent" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.138-4.403m2.454-1.314A10.044 10.044 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 4.403m-5.412-5.412L12 12m0 0l-3.375-3.375M12 12l3.375 3.375m-7.012-1.314A3 3 0 1112 9" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div class="hidden md:block"></div>

            <div>
                <x-input-label for="password" :value="__('Nueva Contraseña')" class="text-gray-300" />
                <div class="relative mt-1">
                    <x-text-input id="password" name="password" 
                        ::type="showNew ? 'text' : 'password'" 
                        class="block w-full bg-andes-dark border-gray-700 text-black pr-10 focus:border-andes-rojo focus:ring-andes-rojo" 
                        autocomplete="new-password" />
                    
                    <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-andes-rojo transition">
                        <svg x-show="!showNew" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showNew" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.138-4.403m2.454-1.314A10.044 10.044 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 4.403m-5.412-5.412L12 12m0 0l-3.375-3.375M12 12l3.375 3.375m-7.012-1.314A3 3 0 1112 9" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" class="text-gray-300" />
                <div class="relative mt-1">
                    <x-text-input id="password_confirmation" name="password_confirmation" 
                        ::type="showConfirm ? 'text' : 'password'" 
                        class="block w-full bg-andes-dark border-gray-700 text-black pr-10 focus:border-andes-rojo focus:ring-andes-rojo" 
                        autocomplete="new-password" />
                    
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-andes-rojo transition">
                        <svg x-show="!showConfirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showConfirm" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.138-4.403m2.454-1.314A10.044 10.044 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 4.403m-5.412-5.412L12 12m0 0l-3.375-3.375M12 12l3.375 3.375m-7.012-1.314A3 3 0 1112 9" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-andes-rojo hover:bg-red-700 shadow-lg shadow-red-900/20 transition-all">
                {{ __('Guardar Contraseña') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-400">
                    {{ __('Contraseña actualizada.') }}
                </p>
            @endif
        </div>
    </form>
</section>
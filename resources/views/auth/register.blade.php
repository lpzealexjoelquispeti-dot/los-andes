<x-guest-layout>
    <div class="min-h-screen flex w-full relative">
        <div class="absolute top-0 left-0 w-full h-2 flex z-50">
            <div class="w-1/3 h-full bg-andes-rojo"></div>
            <div class="w-1/3 h-full bg-andes-amarillo"></div>
            <div class="w-1/3 h-full bg-andes-verde"></div>
        </div>

        <div class="hidden lg:flex lg:w-1/3 bg-andes-oscuro items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 bg-andes-oscuro opacity-60 z-10"></div>
            <div class="absolute inset-0 bg-cover bg-center z-0" 
                 style="background-image: url('{{ asset('img/fondo-registro.jpg') }}');"></div>
            
            <div class="relative z-20 text-center px-8">
                <h2 class="text-4xl font-black text-andes-blanco mb-6 uppercase tracking-tighter">Únete a la Fraternidad</h2>
                <p class="text-gray-300 text-lg font-light">Digitaliza tu tienda en la calle Los Andes y llega a todos los rincones de Bolivia.</p>
                
                <div class="mt-10 flex justify-center space-x-2">
                    <div class="w-3 h-3 bg-andes-amarillo transform rotate-45"></div>
                    <div class="w-3 h-3 bg-andes-verde transform rotate-45"></div>
                    <div class="w-3 h-3 bg-andes-rojo transform rotate-45"></div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-2/3 flex items-center justify-center bg-andes-blanco p-6 md:p-12 overflow-y-auto">
            <div class="w-full max-w-2xl">
                <div class="mb-8 text-center lg:text-left">
                    <h1 class="text-3xl font-extrabold text-andes-oscuro">Registro de Nuevo Usuario</h1>
                    <p class="text-gray-500 mt-2">Crea tu cuenta para empezar a gestionar tus trajes y catálogos.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <div class="md:col-span-2 lg:col-span-1">
                        <x-input-label for="name" value="Nombres" class="font-bold text-andes-oscuro" />
                        <x-text-input id="name" class="block mt-1 w-full bg-gray-50 border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm" 
                                     type="text" name="name" :value="old('name')" required autofocus placeholder="Ej. Juan" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ap_pat" value="Apellido Paterno" class="font-bold text-andes-oscuro" />
                        <x-text-input id="ap_pat" class="block mt-1 w-full bg-gray-50 border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm" 
                                     type="text" name="ap_pat" :value="old('ap_pat')" required placeholder="Apellido del padre" />
                        <x-input-error :messages="$errors->get('ap_pat')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ap_mat" value="Apellido Materno" class="font-bold text-andes-oscuro" />
                        <x-text-input id="ap_mat" class="block mt-1 w-full bg-gray-50 border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm" 
                                     type="text" name="ap_mat" :value="old('ap_mat')" placeholder="Apellido de la madre (Opcional)" />
                        <x-input-error :messages="$errors->get('ap_mat')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="email" value="Correo Electrónico Corporativo o Personal" class="font-bold text-andes-oscuro" />
                        <x-text-input id="email" class="block mt-1 w-full bg-gray-50 border-gray-300 focus:border-andes-verde focus:ring-andes-verde rounded-lg shadow-sm" 
                                     type="email" name="email" :value="old('email')" required placeholder="juan@correo.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }">
                        <x-input-label for="password" value="Contraseña Segura" class="font-bold text-andes-oscuro" />
                        <div class="relative mt-1">
                            <x-text-input id="password" class="block w-full bg-gray-50 border-gray-300 focus:border-andes-rojo focus:ring-andes-rojo rounded-lg shadow-sm pr-10" 
                                        x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Min. 8 caracteres" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-andes-rojo">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-andes-rojo" />
                    </div>

                    <div x-data="{ show: false }">
                        <x-input-label for="password_confirmation" value="Confirmar Contraseña" class="font-bold text-andes-oscuro" />
                        <div class="relative mt-1">
                            <x-text-input id="password_confirmation" class="block w-full bg-gray-50 border-gray-300 focus:border-andes-rojo focus:ring-andes-rojo rounded-lg shadow-sm pr-10" 
                                        x-bind:type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="Repite tu contraseña" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-andes-rojo">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-4">
                        <button type="submit" class="w-full py-4 bg-andes-verde hover:bg-green-800 text-white font-black rounded-xl shadow-lg transition-all transform hover:scale-[1.01] uppercase tracking-wider">
                            Crear mi Cuenta de Usuario
                        </button>
                        
                        <p class="mt-6 text-center text-gray-600">
                            ¿Ya eres parte de Los Andes? 
                            <a href="{{ route('login') }}" class="text-andes-rojo font-bold hover:underline">Inicia sesión aquí</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
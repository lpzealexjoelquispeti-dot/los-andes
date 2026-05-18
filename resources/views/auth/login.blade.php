<x-guest-layout>
    <div class="min-h-screen flex w-full relative">
        
        <div class="absolute top-0 left-0 w-full h-2 flex z-50">
            <div class="w-1/3 h-full bg-andes-rojo"></div>
            <div class="w-1/3 h-full bg-andes-amarillo"></div>
            <div class="w-1/3 h-full bg-andes-verde"></div>
        </div>

        <div class="hidden md:flex w-1/2 bg-andes-oscuro items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 bg-andes-oscuro opacity-70 z-10"></div>
            
            <div class="absolute inset-0 bg-cover bg-center z-0 transform scale-105 transition-transform duration-10000" 
                 style="background-image: url('{{ asset('img/fondo-folklore.jpg') }}');">
            </div>

            <div class="relative z-20 text-center px-12">
                <div class="flex justify-center mb-8 space-x-2">
                    <div class="w-4 h-4 bg-andes-rojo transform rotate-45"></div>
                    <div class="w-4 h-4 bg-andes-amarillo transform rotate-45"></div>
                    <div class="w-4 h-4 bg-andes-verde transform rotate-45"></div>
                    <div class="w-4 h-4 bg-andes-amarillo transform rotate-45"></div>
                    <div class="w-4 h-4 bg-andes-rojo transform rotate-45"></div>
                </div>

                <h1 class="text-5xl font-extrabold text-andes-blanco mb-4 tracking-widest uppercase drop-shadow-xl">
                    Los Andes
                </h1>
                <p class="text-xl text-gray-200 font-light leading-relaxed">
                    El centro del folklore paceño.<br>
                    Gestiona y descubre la identidad de nuestras danzas, desde la Morenada hasta el Pujllay.
                </p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex items-center justify-center bg-andes-blanco p-8 relative">
            
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%231A1A1A\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>

            <div class="w-full max-w-md relative z-10">
                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-extrabold text-andes-oscuro">¡Jallalla! Bienvenido</h2>
                    <p class="text-sm text-gray-500 mt-2">Ingresa tus credenciales para acceder a tu tienda o explorar los catálogos.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" value="Correo Electrónico" class="text-andes-oscuro font-bold" />
                        <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-andes-rojo focus:ring-andes-rojo rounded-lg shadow-sm bg-gray-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ejemplo@correo.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-andes-rojo font-semibold text-sm" />
                    </div>

                    <div class="mt-6" x-data="{ show: false }">
                        <x-input-label for="password" value="Contraseña" class="text-andes-oscuro font-bold" />
                        <div class="relative mt-1">
                            <x-text-input id="password" 
                                         class="block w-full border-gray-300 focus:border-andes-rojo focus:ring-andes-rojo rounded-lg shadow-sm bg-gray-50 pr-10" 
                                         x-bind:type="show ? 'text' : 'password'" 
                                         name="password" required autocomplete="current-password" placeholder="••••••••" />
                            
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-andes-rojo focus:outline-none">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-andes-rojo font-semibold text-sm" />
                    </div>

                    <div class="flex items-center justify-between mt-5">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
<<<<<<< Updated upstream
                            <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="rounded border-gray-300 text-andes-verde shadow-sm focus:ring-andes-verde cursor-pointer">
=======
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-andes-verde shadow-sm focus:ring-andes-verde cursor-pointer">
>>>>>>> Stashed changes
                            <span class="ms-2 text-sm text-gray-700 font-medium">Recordar mis datos</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-andes-rojo hover:text-red-800 font-bold transition-colors" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-extrabold text-white bg-andes-rojo hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-andes-rojo transition-all duration-200 transform hover:-translate-y-0.5">
                            Ingresar al Sistema
                        </button>
                    </div>

                    <div class="mt-8 text-center border-t border-gray-200 pt-6">
                        <p class="text-sm text-gray-600 font-medium">¿Eres un fraterno o nueva tienda? <br>
                            <a href="{{ route('register') }}" class="font-bold text-andes-verde hover:text-green-800 text-base mt-1 inline-block transition-colors">Regístrate aquí</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
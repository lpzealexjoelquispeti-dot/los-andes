<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Los Andes - Folklore Digital</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,600,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-andes-oscuro bg-andes-blanco">

    <header class="fixed w-full z-50 bg-andes-oscuro backdrop-blur-sm border-b border-gray-100 shadow-sm transition-all duration-300">
        {{-- Barra Tricolor Superior --}}
        <div class="flex h-1 w-full">
            <div class="w-1/3 bg-andes-rojo"></div>
            <div class="w-1/3 bg-andes-amarillo"></div>
            <div class="w-1/3 bg-andes-verde"></div>
        </div>

        <nav class="flex items-center justify-between p-6 lg:px-8 max-w-7xl mx-auto">
            <div class="flex lg:flex-1">
                <a href="/" class="text-2xl font-black text-andes-blanco tracking-tighter uppercase">LOS ANDES</a>
            </div>

            <div class="flex gap-x-6 items-center">
                {{-- SI EL USUARIO NO ESTÁ LOGUEADO --}}
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-black uppercase text-gray-900 hover:text-andes-rojo transition">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="bg-andes-oscuro text-white px-6 py-2 rounded-xl text-sm font-black uppercase shadow-lg hover:scale-105 transition">Registrarse</a>
                @endguest

               {{-- SI EL USUARIO YA INICIÓ SESIÓN --}}
@auth
    {{-- NUEVO: LINK AL CATÁLOGO MAESTRO --}}
    <a href="{{ route('public.catalogo.index') }}" 
       class="text-sm font-black uppercase {{ request()->routeIs('public.catalogo.index') ? 'text-andes-blanco' : 'text-gray-100' }} hover:text-andes-verde transition">
        Explorar Trajes
    </a>

    <div class="h-6 w-[1px] bg-gray-600/30"></div> {{-- Separador --}}

    <a href="{{ route('public.tiendas.index') }}" 
       class="text-sm font-black uppercase {{ request()->routeIs('public.tiendas.index') ? 'text-andes-blanco' : 'text-gray-100' }} hover:text-andes-verde transition">
        Ver Tiendas
    </a>
    
    <div class="h-6 w-[1px] bg-gray-600/30"></div>

    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
        <span class="text-sm font-black uppercase text-gray-100 group-hover:text-andes-rojo transition">Mi Perfil</span>
        <div class="w-10 h-10 rounded-full bg-andes-amarillo flex items-center justify-center text-white font-black border-2 border-white shadow-md transition group-hover:scale-110">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </a>
@endauth
            </div>
        </nav>
    </header>

    {{-- Espaciador para que el contenido no quede debajo del header fixed --}}
    <div class="pt-24">
        <main>
            {{ $slot }}
        </main>
    </div>

    <footer class="bg-andes-oscuro text-andes-blanco py-8 border-t-4 border-andes-amarillo mt-20">
        <div class="container mx-auto px-4 text-center">
            <p class="font-bold uppercase tracking-widest text-sm">Proyecto Los Andes &copy; {{ date('Y') }}</p>
            <p class="text-gray-400 text-sm mt-2 font-medium tracking-wide">Tecnología y Cultura en La Paz, Bolivia.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const success = @json(session('success'));
            const errorBag = @json($errors->any() ? $errors->first() : null);

            if (success) {
                Swal.fire({ icon: 'success', title: 'Listo', text: success, timer: 2400, showConfirmButton: false });
            }

            if (errorBag) {
                Swal.fire({ icon: 'error', title: 'Revisa los datos', text: errorBag });
            }
        });
    </script>
</body>
</html>

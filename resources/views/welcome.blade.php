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

    <header class="fixed w-full z-50 bg-andes-oscuro backdrop-blur-sm border-b border-andes-oscuro shadow-sm transition-all duration-300">
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
                    <a href="{{ route('login') }}" class="text-sm font-black uppercase text-gray-100 hover:text-andes-rojo transition">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="bg-andes-oscuro text-white px-6 py-2 rounded-xl text-sm font-black uppercase shadow-lg hover:scale-105 transition">Registrarse</a>
                @endguest

                {{-- SI EL USUARIO YA INICIÓ SESIÓN --}}
                @auth
                    <a href="{{ route('public.catalogo.index') }}" 
                       class="text-sm font-black uppercase {{ request()->routeIs('public.catalogo.index') ? 'text-andes-blanco' : 'text-gray-100' }} hover:text-andes-verde transition">
                         Explorar Trajes
                    </a>

                    <div class="h-6 w-[1px] bg-gray-600/30"></div>

                    <a href="{{ route('public.tiendas.index') }}" 
                       class="text-sm font-black uppercase {{ request()->routeIs('public.tiendas.index') ? 'text-gray-100' : 'text-gray-100' }} hover:text-andes-verde transition">
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

    <main>
        {{-- HERO SECTION --}}
        <div class="relative pt-16 pb-32 flex content-center items-center justify-center min-h-screen">
            <div class="absolute top-0 w-full h-full bg-center bg-cover" style="background-image: url('{{ asset('img/fondo-folklore.jpg') }}');">
                <span id="blackOverlay" class="w-full h-full absolute opacity-75 bg-andes-oscuro"></span>
            </div>
            
            <div class="container relative mx-auto px-4 text-center z-10 mt-16">
                <h1 class="text-5xl md:text-7xl font-black text-white uppercase tracking-tight drop-shadow-xl mb-6">
                    El corazón del <span class="text-andes-amarillo">Folklore</span>
                </h1>
                <p class="mt-4 text-lg md:text-2xl text-gray-200 max-w-3xl mx-auto font-light leading-relaxed">
                    Explora el catálogo digital más grande de la calle Los Andes en La Paz. Encuentra tu traje perfecto, descubre nuevas tiendas y conecta con nuestra identidad cultural a un solo clic.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#catalogo-real" class="bg-andes-verde hover:bg-green-800 text-white font-bold px-8 py-4 rounded-xl shadow-lg transition-transform transform hover:scale-105 uppercase tracking-wide">
                        Ver Catálogo
                    </a>
                    <a href="{{ route('register') }}" class="bg-transparent border-2 border-andes-blanco text-andes-blanco hover:bg-andes-blanco hover:text-andes-oscuro font-bold px-8 py-4 rounded-xl shadow-lg transition-all uppercase tracking-wide">
                        Publicar mi Tienda
                    </a>
                </div>
            </div>
            
            <div class="top-auto bottom-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden h-16" style="transform: translateZ(0);">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                    <polygon class="text-andes-blanco fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>

        {{-- SECCIÓN SERVICIOS / CARACTERÍSTICAS --}}
        <section id="servicios" class="pb-20 bg-andes-blanco -mt-24 relative z-20">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap">
                    
                    <div class="lg:pt-12 pt-6 w-full md:w-4/12 px-4 text-center">
                        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-xl rounded-2xl transform transition hover:-translate-y-2 hover:shadow-2xl border-t-4 border-andes-rojo">
                            <div class="px-6 py-8 flex-auto">
                                <div class="text-andes-rojo p-3 text-center inline-flex items-center justify-center w-16 h-16 mb-5 shadow-md rounded-full bg-red-50">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                                <h6 class="text-2xl font-bold text-andes-oscuro uppercase">Para Fraternos</h6>
                                <p class="mt-4 mb-4 text-gray-600 font-medium leading-relaxed">
                                    Navega por miles de trajes para entradas folklóricas. Compara precios, verifica disponibilidad de tallas y contacta directamente con los artesanos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-4/12 px-4 text-center">
                        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-xl rounded-2xl transform transition hover:-translate-y-2 hover:shadow-2xl border-t-4 border-andes-amarillo">
                            <div class="px-6 py-8 flex-auto">
                                <div class="text-andes-amarillo p-3 text-center inline-flex items-center justify-center w-16 h-16 mb-5 shadow-md rounded-full bg-yellow-50">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <h6 class="text-2xl font-bold text-andes-oscuro uppercase">Búsqueda Inteligente</h6>
                                <p class="mt-4 mb-4 text-gray-600 font-medium leading-relaxed">
                                    Gracias a nuestro tesauro avanzado, busca por términos locales. ¿Buscas una matraca, un combo o una careta? El sistema entiende exactamente lo que necesitas.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 w-full md:w-4/12 px-4 text-center">
                        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-xl rounded-2xl transform transition hover:-translate-y-2 hover:shadow-2xl border-t-4 border-andes-verde">
                            <div class="px-6 py-8 flex-auto">
                                <div class="text-andes-verde p-3 text-center inline-flex items-center justify-center w-16 h-16 mb-5 shadow-md rounded-full bg-green-50">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <h6 class="text-2xl font-bold text-andes-oscuro uppercase">Para Tiendas</h6>
                                <p class="mt-4 mb-4 text-gray-600 font-medium leading-relaxed">
                                    Digitaliza tu negocio. Crea tu perfil de tienda, publica tus catálogos de trajes y recibe contactos directos a tu WhatsApp para multiplicar tus alquileres.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- NUEVA SECCIÓN DINÁMICA: MÓDULO DE DANZAS --}}
        <section class="py-16 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-black text-andes-oscuro uppercase tracking-tight">Danzas Tradicionales</h2>
                    <p class="text-gray-500 font-medium mt-1">Línea cultural gestionada por el Administrador del Sistema.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @forelse($danzas as $danza)
                        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-xl mb-4">
                                🎭
                            </div>
                            <h3 class="font-black text-andes-oscuro text-lg uppercase mb-1">{{ $danza->nom_danza }}</h3>
                            <p class="text-[10px] font-bold text-andes-verde uppercase bg-emerald-50 inline-block px-2 py-0.5 rounded">Vigente en Base de Datos</p>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white rounded-2xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold uppercase tracking-wider text-sm">Sin expresiones culturales activas por el momento.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- NUEVA SECCIÓN DINÁMICA: VITRINA DE TRAJES REALES --}}
        <section id="catalogo-real" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-black text-andes-oscuro uppercase tracking-tight">Últimas Colecciones en Vitrina</h2>
                    <p class="text-gray-500 font-medium mt-1">Prendas añadidas recientemente por los talleres artesanales de la Calle Los Andes.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @forelse($trajes as $traje)
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition flex flex-col h-full">
                            <div class="aspect-[3/4] bg-gray-100 relative">
                                @if($traje->imagenes && $traje->imagenes->first())
                                    <img src="{{ asset('storage/' . $traje->imagenes->first()->ruta_img) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 font-bold text-[11px] uppercase bg-gray-100 p-4 text-center">
                                        <span>🏔️ Los Andes</span>
                                        <span class="text-[9px] font-normal text-gray-400 mt-1">Fotografía en proceso</span>
                                    </div>
                                @endif
                                <span class="absolute bottom-2 left-2 bg-andes-rojo text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase">
                                    {{ $traje->danza->nom_danza ?? 'Folklore' }}
                                </span >
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="font-black text-andes-oscuro text-sm uppercase line-clamp-2 h-10 mb-2">
                                    {{ str_replace([' - Varón', ' - Mujer'], '', $traje->nom_traje) }}
                                </h3>
                                <div class="mt-auto pt-2 border-t border-gray-100 flex justify-between items-center">
                                    <p class="text-andes-verde font-black text-base">
                                        <span class="text-xs">Bs.</span> {{ number_format($traje->pre_alquiler, 0) }}
                                    </p>
                                    <span class="text-[9px] font-bold text-gray-500 uppercase bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $traje->color_traje ?? 'Color Múltiple' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold uppercase tracking-wider text-sm">Los talleres se encuentran actualizando sus catálogos para la siguiente festividad.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-andes-oscuro text-andes-blanco py-8 border-t-4 border-andes-amarillo">
        <div class="container mx-auto px-4 text-center">
            <p class="font-bold uppercase tracking-widest text-sm">Proyecto Los Andes &copy; {{ date('Y') }}</p>
            <p class="text-gray-400 text-sm mt-2">Tecnología y Cultura en La Paz, Bolivia.</p>
        </div>
    </footer>

</body>
</html>
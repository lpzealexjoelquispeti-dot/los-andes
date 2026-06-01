<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Los Andes') }} - Panel</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Estilo para evitar parpadeo de Alpine.js -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed z-50 inset-y-0 left-0 w-64 bg-andes-oscuro text-andes-blanco transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 flex flex-col shadow-2xl">
        
        <div class="flex items-center justify-center h-20 border-b border-gray-800 bg-black/20">
            <div class="flex items-center gap-2">
                <div class="flex flex-col space-y-1">
                    <div class="w-2 h-2 bg-andes-rojo rounded-full"></div>
                    <div class="w-2 h-2 bg-andes-amarillo rounded-full"></div>
                    <div class="w-2 h-2 bg-andes-verde rounded-full"></div>
                </div>
                <span class="font-black text-2xl tracking-widest uppercase text-white shadow-sm">Los Andes</span>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/10 text-white font-bold transition hover:bg-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Mi Panel
            </a>

            {{-- ══ SECCIÓN PARA EL SUPERADMIN ══ --}}
            @role('SuperAdmin')
                <div class="pt-4 pb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Administration</p>
                </div>
                <a href="{{ route('admin.tiendas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                    <svg class="w-5 h-5 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Aprobar Tiendas
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                    <svg class="w-5 h-5 text-andes-rojo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Usuarios y Roles
                </a>
                
                <a href="{{ route('admin.danzas.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition group {{ request()->routeIs('admin.danzas.*') ? 'bg-white/10 text-white' : '' }}">
                    <svg class="w-5 h-5 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Danzas</span>
                </a>
                <a href="{{ route('admin.tesauro.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.tesauro.*') ? 'bg-white/10 text-white shadow-lg' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.tesauro.*') ? 'text-andes-amarillo' : 'text-andes-verde' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Tesauro
                </a>

                {{-- REPORTES ADMIN --}}
                <div x-data="{ openReportes: false }" class="pt-2">
                    <button @click="openReportes = !openReportes" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg font-medium transition text-gray-300 hover:text-white hover:bg-white/5">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-andes-rojo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Reportes Admin</span>
                        </div>
                        <svg :class="openReportes ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openReportes" x-cloak x-transition class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('admin.reportes.tesauro') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                            <div class="w-2 h-2 rounded-full bg-andes-amarillo"></div>
                            Reporte Tesauro
                        </a>
                        <a href="{{ route('admin.reportes.vendedores') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                            <div class="w-2 h-2 rounded-full bg-andes-verde"></div>
                            Reporte Vendedores
                        </a>
                    </div>
                </div>
            @endrole

            {{-- ══ SECCIÓN PARA EL VENDEDOR ══ --}}
            @role('Vendedor')
                <div class="pt-4 pb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mi Negocio</p>
                </div>

                <a href="{{ route('vendedor.tienda.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                    <svg class="w-5 h-5 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Perfil de Tienda
                </a>
                
                @if(!empty($tiendaActiva) && $tiendaActiva->est_tie)
                    <a href="{{ route('vendedor.trajes.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                        <svg class="w-5 h-5 text-andes-rojo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Catálogo de Trajes
                    </a>
                    <a href="{{ route('vendedor.alquileres.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                        <svg class="w-5 h-5 text-andes-verde" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z"/>
                        </svg>
                        Alquileres
                    </a>
                    <div class="border-t border-white/10 my-2"></div>
                    <a href="{{ route('vendedor.tienda.diseno') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-andes-amarillo/20 transition group">
                        <!-- Icono cambiado a pincel/diseño diferente para evitar duplicados -->
                        <svg class="w-5 h-5 text-andes-amarillo group-hover:rotate-12 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <span class="font-bold uppercase text-xs tracking-widest">Personalizar Mi Tienda</span>
                    </a>
                @endif

                {{-- Obtención segura de relaciones con operador Null Safe --}}
                @php
                    $tiendaUser = auth()->user()->tiendas?->first();
                    $primerTrajeId = $tiendaUser ? $tiendaUser->trajes?->first()?->cod_traje : null;
                @endphp

                @if($primerTrajeId)
                    <a href="{{ route('vendedor.trajes.unidades.index', $primerTrajeId) }}" 
                       class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg transition font-medium group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-andes-verde transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Inventario</span>
                    </a>

                    <a href="{{ route('vendedor.trajes.impresion.panel', $primerTrajeId) }}" 
                       class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg transition font-medium group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Imprimir Etiquetas</span>
                    </a>
                @endif

                {{-- DROPDOWN DE REPORTES PARA EL VENDEDOR --}}
                <div x-data="{ openReportesVendedor: false }" class="pt-2">
                    <button @click="openReportesVendedor = !openReportesVendedor" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg font-medium transition text-gray-300 hover:text-white hover:bg-white/5">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-andes-verde" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Mis Reportes</span>
                        </div>
                        <svg :class="openReportesVendedor ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openReportesVendedor" x-cloak x-transition class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('vendedor.reportes.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                            <div class="w-2 h-2 rounded-full bg-andes-amarillo"></div>
                            Inventario Parametrizado
                        </a>
                    </div>
                </div>
            @endrole

            @role('Cliente')
                <div class="pt-4 pb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mi Actividad</p>
                </div>
                <a href="{{ route('cliente.alquileres.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                    <svg class="w-5 h-5 text-andes-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Mis Alquileres
                </a>
                <a href="{{ route('notificaciones.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition font-medium">
                    <svg class="w-5 h-5 text-andes-rojo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Notificaciones
                </a>
            @endrole

        </nav>
    </aside>

    <!-- Overlay móvil -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 md:hidden"></div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Header -->
        <header class="bg-white shadow-sm h-20 flex items-center justify-between px-6 z-10 relative">
            <div class="absolute top-0 left-0 w-full h-1 flex">
                <div class="w-1/3 bg-andes-rojo"></div>
                <div class="w-1/3 bg-andes-amarillo"></div>
                <div class="w-1/3 bg-andes-verde"></div>
            </div>

            <div class="flex items-center gap-4 mt-1">
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-900 focus:outline-none md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wide">
                    {{ $header ?? 'Panel de Control' }}
                </h2>
            </div>

            <!-- Menú usuario -->
            <div class="flex items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 text-sm font-bold text-gray-600 hover:text-andes-oscuro focus:outline-none transition border border-gray-200 pl-1.5 pr-4 py-1.5 rounded-full hover:bg-gray-50 shadow-sm">
                            <img class="h-8 w-8 rounded-full object-cover border-2 border-andes-rojo" 
                                 src="{{ auth()->user()->foto_perfil ? asset('storage/'.auth()->user()->foto_perfil) : asset('img/default-avatar.png') }}" 
                                 alt="{{ auth()->user()->name }}">

                            <div class="hidden md:block">{{ Auth::user()->name }}</div>

                            <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-medium text-gray-700">
                            Mi Perfil
                        </x-dropdown-link>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" 
                                onclick="event.preventDefault(); this.closest('form').submit();" 
                                class="text-andes-rojo font-bold">
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            {{ $slot }}
        </main>
        
    </div>
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

            document.querySelectorAll('form[data-confirm]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'question',
                        title: form.dataset.confirm,
                        showCancelButton: true,
                        confirmButtonText: 'Si, continuar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.removeAttribute('data-confirm');
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

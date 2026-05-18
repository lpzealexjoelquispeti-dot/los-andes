<x-public-layout>
    <div class="max-w-7xl mx-auto py-16 px-4">
        
        {{-- Encabezado de la página --}}
        <div class="mb-16 text-center lg:text-left">
            <h1 class="text-5xl font-black text-andes-oscuro uppercase tracking-tighter leading-none">
                Nuestras Tiendas
            </h1>
            <div class="flex items-center gap-3 mt-4 justify-center lg:justify-start">
                <div class="h-[2px] w-12 bg-andes-rojo"></div>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.2em]">Cultura y tradición en cada rincón</p>
            </div>
        </div>

        {{-- Grid de Tiendas (Cartas de Diseño) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($tiendas as $tienda)
                @php 
                    $diseno = $tienda->diseno; 
                    $colorMarca = $diseno->color_primario ?? '#1f2937';
                @endphp
                
                <a href="{{ route('public.tiendas.show', $tienda->cod_tienda) }}" class="group block">
                    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 relative h-full flex flex-col">
                        
                        {{-- Área de Banner --}}
                        <div class="h-36 bg-gray-200 overflow-hidden relative">
                            @if($diseno && $diseno->banner_path)
                                <img src="{{ asset('storage/'.$diseno->banner_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-300"></div>
                            @endif
                            {{-- Overlay sutil --}}
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                        </div>

                        {{-- Logo Flotante --}}
                        <div class="absolute top-24 left-8">
                            @if($diseno && $diseno->logo_path)
                                <img src="{{ asset('storage/'.$diseno->logo_path) }}" class="w-20 h-20 rounded-2xl border-4 border-white shadow-xl object-cover bg-white">
                            @else
                                <div class="w-20 h-20 rounded-2xl border-4 border-white bg-white shadow-xl flex items-center justify-center font-black text-gray-200 text-xs">LOGO</div>
                            @endif
                        </div>

                        {{-- Información de la Tienda --}}
                        <div class="pt-12 px-8 pb-8 flex-grow flex flex-col">
                            <h2 class="text-2xl font-black uppercase leading-tight transition-colors mb-2" style="color: {{ $colorMarca }}">
                                {{ $tienda->nom_tie }}
                            </h2>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide italic mb-6">
                                "{{ $diseno->slogan ?? 'Tradición folklórica' }}"
                            </p>
                            
                            <div class="mt-auto flex items-center justify-between pt-6 border-t border-gray-50">
                                <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                    Ver Catálogo 
                                    <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </span>
                                <div class="flex gap-1">
                                    <div class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $colorMarca }}"></div>
                                    <div class="w-1.5 h-1.5 rounded-full opacity-20" style="background-color: {{ $colorMarca }}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-black uppercase tracking-widest text-sm">No se encontraron tiendas activas</p>
                </div>
            @endforelse
        </div>
    </div>
</x-public-layout>


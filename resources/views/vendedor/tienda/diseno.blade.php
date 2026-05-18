<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase">
            Personalizar Identidad de Mi Tienda
        </h2>
    </x-slot>
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
        <p class="font-bold">¡Logrado!</p>
        <p class="text-sm">{{ session('success') }}</p>
    </div>
@endif
    {{-- Contenedor con ancho ampliado --}}
    <div class="max-w-[1450px] mx-auto py-10 px-4 sm:px-6 lg:px-8" x-data="shopPreview()">
        <form action="{{ route('vendedor.tienda.diseno.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ── PANEL IZQUIERDO ── --}}
            <div class="space-y-6">

                {{-- Colores --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-2 h-6 bg-andes-oscuro rounded-full"></div>
                        <h3 class="text-xs font-black uppercase text-gray-500 tracking-widest">Colores Base</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Color Primario" class="text-[10px] font-bold uppercase text-gray-400" />
                            <input type="color" name="color_primario" x-model="color_primario"
                                class="w-full h-12 rounded-xl cursor-pointer border-2 border-gray-50 bg-white p-1 mt-1">
                            <x-input-error :messages="$errors->get('color_primario')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Color de Fondo" class="text-[10px] font-bold uppercase text-gray-400" />
                            <input type="color" name="color_fondo" x-model="color_fondo"
                                class="w-full h-12 rounded-xl cursor-pointer border-2 border-gray-50 bg-white p-1 mt-1">
                            <x-input-error :messages="$errors->get('color_fondo')" class="mt-1" />
                        </div>
                    </div>
                    <p class="text-[9px] text-gray-400 mt-3 italic">* El color primario se usará en botones y títulos destacados.</p>
                </div>

                {{-- Identidad Visual --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-andes-amarillo">
                    <h3 class="text-xs font-black uppercase text-gray-700 mb-4 tracking-widest">Identidad Visual (Imágenes)</h3>
                    <div class="space-y-5">
                        <div>
                            <x-input-label value="Logo de la Tienda (Cuadrado)" class="font-bold text-gray-600" />
                            <input type="file" name="logo" accept="image/*" @change="previewLogo"
                                class="mt-1 block w-full text-xs text-gray-500
                                        file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                        file:text-xs file:font-black file:bg-andes-amarillo file:text-white
                                        hover:file:bg-yellow-600">
                            <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Banner de Portada" class="font-bold text-gray-600" />
                            <input type="file" name="banner" accept="image/*" @change="previewBanner"
                                class="mt-1 block w-full text-xs text-gray-500
                                        file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                        file:text-xs file:font-black file:bg-andes-oscuro file:text-white
                                        hover:file:bg-black">
                            <x-input-error :messages="$errors->get('banner')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Slogan de la Tienda" class="font-bold text-gray-600" />
                            <x-text-input name="slogan" x-model="slogan"
                                        class="w-full mt-1 text-sm"
                                        placeholder="Ej: Los mejores trajes de la 16 de Julio" />
                            <x-input-error :messages="$errors->get('slogan')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- Contacto --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-andes-verde">
                    <h3 class="text-xs font-black uppercase text-gray-700 mb-4 tracking-widest">Canales de Contacto</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label value="WhatsApp (Número sin +)" class="text-[10px] font-bold" />
                            <x-text-input name="link_whatsapp" x-model="whatsapp"
                                        placeholder="Ej: 59170000000" class="w-full mt-1 text-sm" />
                            <x-input-error :messages="$errors->get('link_whatsapp')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Enlace de Facebook" class="text-[10px] font-bold" />
                            <x-text-input name="link_facebook" x-model="facebook"
                                        placeholder="https://facebook.com/tutienda" class="w-full mt-1 text-sm" />
                            <x-input-error :messages="$errors->get('link_facebook')" class="mt-1" />
                        </div>
                    </div>
                </div>
                {{-- Horario --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-t-4 border-andes-amarillo">
                    <h3 class="text-xs font-black uppercase text-gray-700 mb-4 tracking-widest">Horario de Atención</h3>
                    <div>
                        <x-input-label value="Horario" class="text-[10px] font-bold" />
                        <x-text-input name="horario_tie" x-model="horario"
                                    placeholder="Ej: Lun-Vie 9:00-18:00 / Sab 9:00-13:00"
                                    class="w-full mt-1 text-sm" />
                        <x-input-error :messages="$errors->get('horario_tie')" class="mt-1" />
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-andes-oscuro hover:bg-black text-white font-black py-4 rounded-2xl
                            shadow-xl transition transform hover:-translate-y-1 uppercase tracking-widest text-sm">
                    Guardar Mi Marca Personal
                </button>
            </div>

                {{-- ── PANEL DERECHO (Simulador) ── --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-10">

                        <div class="flex items-center justify-between mb-4 px-2">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Simulador de Tienda en Tiempo Real
                            </p>
                            <div class="flex gap-1">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                        </div>

                        {{-- min-height ajustado para mayor altura visual --}}
                        <div class="rounded-[2.5rem] overflow-hidden shadow-2xl border-[12px] border-gray-900"
                             :style="'background-color:' + color_fondo + '; min-height:800px;'">

                            {{-- CABECERA: banner como fondo --}}
                            <div class="relative h-64 flex items-end"
                                 x-bind:style="banner_url
                                     ? `background-image:url('${banner_url}');background-size:cover;background-position:center;`
                                     : ''">

                                {{-- Overlay oscuro --}}
                                <div class="absolute inset-0 transition-all"
                                     :style="banner_url
                                         ? 'background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);'
                                         : 'background-color:#e5e7eb;'">
                                </div>

                                <div x-show="!banner_url"
                                     class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-gray-400 font-bold uppercase text-[10px] tracking-widest text-center px-4">
                                        Sin Banner de Portada (Sube un JPG)
                                    </span>
                                </div>

                                {{-- Logo + nombre sobre el banner --}}
                                <div class="relative z-10 flex items-end gap-5 px-8 pb-6 w-full">

                                    {{-- Logo --}}
                                    <div class="flex-shrink-0">
                                        <template x-if="logo_url">
                                            <img :src="logo_url"
                                                 class="w-24 h-24 rounded-2xl object-cover border-4 shadow-xl"
                                                 :style="'border-color:' + color_primario">
                                        </template>
                                        <template x-if="!logo_url">
                                            <div class="w-24 h-24 rounded-2xl bg-white border-4 shadow-xl flex items-center justify-center"
                                                 :style="'border-color:' + color_primario">
                                                <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Nombre y slogan --}}
                                    <div class="flex-1 min-w-0 pb-1">
                                        <h2 class="text-3xl font-black uppercase tracking-tighter leading-none truncate"
                                            :style="banner_url ? 'color:#ffffff; text-shadow:0 2px 8px rgba(0,0,0,0.8);' : 'color:' + color_primario"
                                            x-text="nombreTienda"></h2>
                                        <p class="text-xs mt-2 font-medium truncate"
                                           :style="banner_url ? 'color:rgba(255,255,255,0.9); text-shadow:0 1px 4px rgba(0,0,0,0.5);' : 'color:#6b7280;'"
                                           x-text="slogan || 'Tu slogan de tienda aparecerá aquí...'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- PRODUCTOS + ICONOS DE REDES --}}
                            <div class="px-8 py-8">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="h-[2px] flex-1 bg-gray-200"></div>
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Nuestra Vitrina</span>
                                    <div class="h-[2px] flex-1 bg-gray-200"></div>
                                </div>

                                <div class="flex gap-6 items-start">

                                    {{-- Tarjetas de muestra --}}
                                    <div class="grid grid-cols-2 gap-5 flex-1 opacity-60">
                                        <div class="bg-white p-5 rounded-[2.5rem] shadow-sm border border-gray-100">
                                            <div class="h-32 bg-gray-100 rounded-3xl mb-4"></div>
                                            <div class="h-3 w-3/4 bg-gray-100 rounded mb-3"></div>
                                            <div class="h-10 rounded-xl" :style="'background-color:' + color_primario"></div>
                                        </div>
                                        <div class="bg-white p-5 rounded-[2.5rem] shadow-sm border border-gray-100">
                                            <div class="h-32 bg-gray-100 rounded-3xl mb-4"></div>
                                            <div class="h-3 w-3/4 bg-gray-100 rounded mb-3"></div>
                                            <div class="h-10 rounded-xl" :style="'background-color:' + color_primario"></div>
                                        </div>
                                    </div>

                                    {{-- ── CORRECCIÓN: SOLO ICONOS ── --}}
                                    <div class="flex flex-col gap-4 pt-1 flex-shrink-0">
                                        <template x-if="whatsapp">
                                            <div class="p-3 bg-green-500 text-white rounded-2xl shadow-lg transition transform hover:scale-110">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.431 5.63 1.432h.006c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </div>
                                        </template>

                                        <template x-if="facebook">
                                            <div class="p-3 bg-blue-600 text-white rounded-2xl shadow-lg transition transform hover:scale-110">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.884v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    {{-- ── FIN ICONOS ── --}}

                                </div>
                            </div>
                            {{-- Horario en simulador --}}
                            <template x-if="horario">
                                <div class="px-8 pb-8">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="h-[2px] flex-1 bg-gray-200"></div>
                                        <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Horario de Atención</span>
                                        <div class="h-[2px] flex-1 bg-gray-200"></div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white rounded-2xl px-5 py-4 shadow-sm border border-gray-100">
                                        <div class="p-2 rounded-xl flex-shrink-0" :style="'background-color:' + color_primario">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="horario"></p>
                                    </div>
                                </div>
                            </template>

                        </div>{{-- fin simulador --}}
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
    function shopPreview() {
        return {
            color_primario: '{{ $diseno->color_primario ?? "#ce1212" }}',
            color_fondo:    '{{ $diseno->color_fondo    ?? "#f3f4f6" }}',
            slogan:         @js($diseno->slogan          ?? ''),
            whatsapp:       @js($diseno->link_whatsapp   ?? ''),
            facebook:       @js($diseno->link_facebook   ?? ''),
            nombreTienda:   @js($tienda->nom_tie),
            logo_url:       @js($diseno->logo_path   ? asset('storage/'.$diseno->logo_path)   : ''),
            banner_url:     @js($diseno->banner_path ? asset('storage/'.$diseno->banner_path) : ''),
            horario: @js($diseno->horario_tie ?? ''),

            previewLogo(event) {
                const file = event.target.files[0];
                if (file) this.logo_url = URL.createObjectURL(file);
            },
            previewBanner(event) {
                const file = event.target.files[0];
                if (file) this.banner_url = URL.createObjectURL(file);
            }
        }
    }
    </script>

    <style>
    [x-cloak] { display: none !important; }
    .transition-all { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>

</x-app-layout>
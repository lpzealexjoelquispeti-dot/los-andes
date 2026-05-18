<x-app-layout>
    <x-slot name="header">Alimentar Inteligencia - Tesauro</x-slot>

    <div class="max-w-6xl mx-auto py-16 px-8">
        
        {{-- Botón Volver --}}
        <a href="{{ route('admin.tesauro.index') }}" class="group inline-flex items-center gap-3 text-[11px] font-black uppercase text-gray-400 hover:text-andes-verde transition-all mb-10 tracking-[0.2em]">
            <div class="p-2 rounded-lg bg-white shadow-sm group-hover:shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </div>
            Volver al glosario
        </a>

        <div class="bg-white rounded-[4rem] shadow-2xl border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gray-50/40 px-16 py-12 border-b border-gray-50">
                <h3 class="text-3xl font-black uppercase text-gray-800 tracking-tighter flex items-center gap-4">
                    <span class="w-2 h-10 bg-andes-verde rounded-full"></span>
                    Nuevo Vínculo Maestro
                </h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2 ml-6">Vincula el lenguaje común con los registros oficiales de Danzas</p>
            </div>

            {{-- Formulario --}}
            <form action="{{ route('admin.tesauro.store') }}" method="POST" class="p-16 space-y-12">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    {{-- Término --}}
                    <div class="space-y-4">
                        <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">Término de Usuario (Lo que la gente busca)</label>
                        <input type="text" name="termino_usuario" value="{{ old('termino_usuario') }}" required placeholder="EJ: MATRACA"
                               class="w-full rounded-2xl border-gray-100 bg-gray-50/50 uppercase font-bold text-sm p-5 focus:ring-andes-verde focus:bg-white shadow-sm transition-all">
                        @error('termino_usuario') <p class="text-red-500 text-[10px] font-black mt-3 bg-red-50 p-4 rounded-2xl italic border border-red-100">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- Danza Relacionada --}}
                    <div class="space-y-4">
                        <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">Danza Oficial Relacionada</label>
                        <select name="cod_danza_ref" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 font-bold text-sm p-5 focus:ring-andes-verde shadow-sm transition-all">
                            @foreach($danzas as $danza)
                                <option value="{{ $danza->cod_danza }}">{{ $danza->nom_danza }} ({{ $danza->clasificacion }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nivel de Traducción --}}
                {{-- Nivel de Traducción --}}
<div class="space-y-4"
     x-data="{ tipo: '{{ old('tipo', 'ortografia') }}' }">

    <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
        Tipo de Vínculo
    </label>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Opción 1 --}}
        <label class="cursor-pointer">
            <input type="radio"
                   name="tipo"
                   value="ortografia"
                   x-model="tipo"
                   class="hidden">

            <div class="p-8 border-2 rounded-[2rem] text-center shadow-sm transition-all"
                 :class="tipo === 'ortografia'
                    ? 'border-andes-verde bg-white shadow-xl scale-[1.02]'
                    : 'border-gray-100 bg-gray-50/30'">

                <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-4 transition-all"
                     :class="tipo === 'ortografia'
                        ? 'bg-andes-verde/10'
                        : 'bg-gray-100'">

                    <svg class="w-5 h-5"
                         :class="tipo === 'ortografia' ? 'text-andes-verde' : 'text-gray-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>

                <p class="text-[11px] font-black uppercase"
                   :class="tipo === 'ortografia' ? 'text-gray-800' : 'text-gray-400'">
                    Ortografía
                </p>

                <p class="text-[10px] mt-1 font-bold"
                   :class="tipo === 'ortografia' ? 'text-gray-500' : 'text-gray-300'">
                    Variante de escritura
                </p>
            </div>
        </label>

        {{-- Opción 2 --}}
        <label class="cursor-pointer">
            <input type="radio"
                   name="tipo"
                   value="sinonimo"
                   x-model="tipo"
                   class="hidden">

            <div class="p-8 border-2 rounded-[2rem] text-center shadow-sm transition-all"
                 :class="tipo === 'sinonimo'
                    ? 'border-andes-verde bg-white shadow-xl scale-[1.02]'
                    : 'border-gray-100 bg-gray-50/30'">

                <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-4 transition-all"
                     :class="tipo === 'sinonimo'
                        ? 'bg-andes-verde/10'
                        : 'bg-gray-100'">

                    <svg class="w-5 h-5"
                         :class="tipo === 'sinonimo' ? 'text-andes-verde' : 'text-gray-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>

                <p class="text-[11px] font-black uppercase"
                   :class="tipo === 'sinonimo' ? 'text-gray-800' : 'text-gray-400'">
                    Sinónimo
                </p>

                <p class="text-[10px] mt-1 font-bold"
                   :class="tipo === 'sinonimo' ? 'text-gray-500' : 'text-gray-300'">
                    Nombre alternativo
                </p>
            </div>
        </label>

        {{-- Opción 3 --}}
        <label class="cursor-pointer">
            <input type="radio"
                   name="tipo"
                   value="referencia"
                   x-model="tipo"
                   class="hidden">

            <div class="p-8 border-2 rounded-[2rem] text-center shadow-sm transition-all"
                 :class="tipo === 'referencia'
                    ? 'border-andes-verde bg-white shadow-xl scale-[1.02]'
                    : 'border-gray-100 bg-gray-50/30'">

                <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-4 transition-all"
                     :class="tipo === 'referencia'
                        ? 'bg-andes-verde/10'
                        : 'bg-gray-100'">

                    <svg class="w-5 h-5"
                         :class="tipo === 'referencia' ? 'text-andes-verde' : 'text-gray-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>

                <p class="text-[11px] font-black uppercase"
                   :class="tipo === 'referencia' ? 'text-gray-800' : 'text-gray-400'">
                    Accesorio
                </p>

                <p class="text-[10px] mt-1 font-bold"
                   :class="tipo === 'referencia' ? 'text-gray-500' : 'text-gray-300'">
                    Elemento relacionado
                </p>
            </div>
        </label>

    </div>
</div>

                {{-- Botones --}}
                <div class="flex flex-col md:flex-row gap-5 pt-14 border-t border-gray-100">

                    <a href="{{ route('admin.tesauro.index') }}"
                       class="group flex-1 flex items-center justify-center gap-3 py-5 px-10 text-gray-400 font-black uppercase text-[11px] tracking-[0.3em] rounded-2xl border border-gray-200 hover:bg-gray-50 hover:text-gray-600 hover:border-gray-300 transition-all duration-200">
                        <svg class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>

                    <button type="submit"
                            class="group flex-[2] flex items-center justify-center gap-4 bg-andes-oscuro text-white py-5 px-14 rounded-2xl font-black uppercase text-[11px] tracking-[0.4em] hover:bg-black active:scale-[0.98] transition-all duration-200 shadow-lg shadow-black/10">
                        <svg class="w-4 h-4 opacity-80 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Vincular al Buscador
                    </button>

                </div>

            </form>
        </div>
    </div>
</x-app-layout>
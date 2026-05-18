<x-app-layout>
    <x-slot name="header">Editar Danza: {{ $danza->nom_danza }}</x-slot>

    <div class="max-w-6xl mx-auto py-16 px-8">

        <a href="{{ route('admin.danzas.index') }}"
           class="group inline-flex items-center gap-3 text-[11px] font-black uppercase text-gray-400 hover:text-andes-verde transition-all mb-10 tracking-[0.2em]">
            <div class="p-2 rounded-lg bg-white shadow-sm group-hover:shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            Volver al panel maestro
        </a>

        <div class="bg-white rounded-[4rem] shadow-[0_30px_100px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">

           <div class="bg-gray-50/40 px-16 py-12 border-b border-gray-50 flex justify-between items-center">
    <div>
        <h3 class="text-3xl font-black uppercase text-gray-800 tracking-tighter flex items-center gap-4">
            <span class="w-2 h-10 bg-andes-verde rounded-full"></span>
            Modificar Registro
        </h3>

        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2 ml-6">
            Asegúrate de que los datos culturales sean precisos
        </p>
    </div>
</div>

            <form action="{{ route('admin.danzas.update', $danza->cod_danza) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-16 space-y-12">

                @csrf
                @method('PATCH')

                {{-- FILA 1 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

                    <div class="space-y-4">
                        <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
                            Nombre Oficial
                        </label>

                        <input type="text"
                               name="nom_danza"
                               value="{{ old('nom_danza', $danza->nom_danza) }}"
                               required
                               class="w-full rounded-2xl border-gray-100 bg-gray-50/50 uppercase font-bold text-sm p-5 focus:ring-andes-verde focus:border-andes-verde">

                    </div>

                    <div class="space-y-4">
                        <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
                            Clasificación
                        </label>

                        <select name="clasificacion"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 font-bold text-sm p-5 focus:ring-andes-verde">

                            <option value="Pesada" {{ $danza->clasificacion == 'Pesada' ? 'selected' : '' }}>Pesada</option>
                            <option value="Liviana" {{ $danza->clasificacion == 'Liviana' ? 'selected' : '' }}>Liviana</option>
                            <option value="Autóctona" {{ $danza->clasificacion == 'Autóctona' ? 'selected' : '' }}>Autóctona</option>
                            <option value="Sátira" {{ $danza->clasificacion == 'Sátira' ? 'selected' : '' }}>Sátira</option>
                            <option value="Salón" {{ $danza->clasificacion == 'Salón' ? 'selected' : '' }}>Salón</option>

                        </select>
                    </div>

                </div>

                {{-- FILA 2 --}}
                <div class="space-y-4">
                    <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
                        Reseña Histórica y Cultural
                    </label>

                    <textarea name="descripcion"
                              rows="6"
                              class="w-full rounded-[2.5rem] border-gray-100 bg-gray-50/50 text-base p-8 focus:ring-andes-verde">{{ old('descripcion', $danza->descripcion) }}</textarea>
                </div>

                {{-- FILA 3 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16 pt-10 border-t border-gray-50">

                    {{-- PREVISUALIZACIÓN --}}
                    <div class="space-y-6">
    <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
        Imagen Actual / Vista Previa
    </label>

    {{-- Caja más pequeña pero mostrando imagen completa --}}
    <div class="w-full max-w-sm h-64 rounded-[2rem] overflow-hidden border border-gray-100 bg-gray-50 shadow-inner mx-auto md:mx-0 flex items-center justify-center">

        @if($danza->imagen_danza)
            <img id="preview-image"
                 src="{{ asset('storage/' . $danza->imagen_danza) }}"
                 class="w-full h-full object-contain p-2">
        @else
            <img id="preview-image"
                 src=""
                 class="hidden w-full h-full object-contain p-2">

            <div id="empty-preview"
                 class="flex flex-col items-center justify-center h-full text-gray-300">

                <svg class="w-14 h-14 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                </svg>

                <span class="text-[10px] font-black uppercase">Sin imagen</span>
            </div>
        @endif

    </div>
</div>

                    {{-- SUBIDA --}}
                    <div class="flex flex-col justify-center space-y-6">

                        <label class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] ml-2 block">
                            Reemplazar Imagen
                        </label>

                        <label class="cursor-pointer group">

                            <div
                                class="flex flex-col items-center justify-center p-10 border-2 border-dashed border-gray-200 rounded-[2rem] bg-gray-50 hover:border-andes-verde hover:bg-white transition-all">

                                <div
                                    class="w-12 h-12 bg-andes-verde/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-andes-verde group-hover:text-white transition-all">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="3" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>

                                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">
                                    Seleccionar Imagen
                                </p>

                                <p id="file-name"
                                   class="text-[10px] text-gray-400 mt-3 font-bold uppercase">
                                    Ningún archivo
                                </p>

                            </div>

                            <input type="file"
                                   name="imagen_danza"
                                   id="upload-image"
                                   class="hidden"
                                   accept="image/*">

                        </label>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="flex flex-col md:flex-row gap-6 pt-12">

                    <a href="{{ route('admin.danzas.index') }}"
                       class="flex-1 py-6 text-center text-gray-400 font-black uppercase text-[11px] tracking-[0.3em] rounded-2xl border border-gray-100 hover:bg-gray-50">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="flex-[2] bg-andes-verde text-white py-6 rounded-[2rem] font-black uppercase text-[11px] tracking-[0.4em] hover:bg-green-700 transition-all">
                        Guardar cambios
                    </button>

                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPT PREVIEW --}}
    <script>
        const input = document.getElementById('upload-image');
        const preview = document.getElementById('preview-image');
        const empty = document.getElementById('empty-preview');
        const fileName = document.getElementById('file-name');

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                fileName.textContent = file.name;

                const reader = new FileReader();

                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.classList.remove('hidden');

                    if (empty) empty.classList.add('hidden');
                }

                reader.readAsDataURL(file);
            }
        });
    </script>

</x-app-layout>
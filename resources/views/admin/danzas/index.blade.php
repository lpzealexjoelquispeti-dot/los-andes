<x-app-layout>
    <x-slot name="header">Administración de Folklore</x-slot>

    {{-- Inicializamos Alpine con un objeto para la danza actual y el estado del modal --}}
    <div class="max-w-7xl mx-auto py-10 px-6" x-data="{ openDetail: false, currentDanza: {} }">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tighter italic">Danzas Maestras</h2>
                <p class="text-andes-verde text-[10px] font-black uppercase tracking-[0.3em]">Gestión de Catálogo e Integridad</p>
            </div>
            {{-- Botón que redirige a la vista create --}}
            <a href="{{ route('admin.danzas.create') }}" 
               class="bg-andes-verde text-white px-8 py-4 rounded-2xl font-black uppercase text-xs shadow-lg hover:scale-105 transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Danza
            </a>
        </div>

        {{-- TABLA DE DATOS --}}
        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Nombre</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400">Clasificación</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($danzas as $danza)
                    <tr class="{{ $danza->trashed() ? 'bg-gray-50/50 opacity-60' : 'hover:bg-gray-50/30' }} transition-colors">
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $danza->trashed() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $danza->trashed() ? 'Inactiva' : 'Activa' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 font-bold text-gray-800 uppercase text-sm">{{ $danza->nom_danza }}</td>
                        <td class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $danza->clasificacion }}</td>
                        <td class="px-8 py-5 text-right flex justify-end gap-2">
                            
                            {{-- BOTÓN OJITO: Carga los datos en Alpine y abre el modal --}}
                             <button
                                type="button"

                                @click="currentDanza = {
                                    nom_danza: '{{ addslashes($danza->nom_danza) }}',
                                    clasificacion: '{{ addslashes($danza->clasificacion) }}',
                                    descripcion: '{{ addslashes($danza->descripcion) }}',
                                    imagen_danza: '{{ $danza->imagen_danza }}',
                                    deleted_at: '{{ $danza->deleted_at }}'
                                }; openDetail = true"

                                class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:scale-110 transition shadow-sm">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                            </button>

                            @if(!$danza->trashed())
                                {{-- Botón Editar (Redirige a vista edit) --}}
                                <a href="{{ route('admin.danzas.edit', $danza->cod_danza) }}" 
                                   class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:scale-110 transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                               <form action="{{ route('admin.danzas.destroy', $danza->cod_danza) }}" method="POST" class="delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmarBajaDanza(this)"
                                            class="p-2 bg-red-50 text-red-600 rounded-xl hover:scale-110 transition shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form> 
                            @else
                                <form action="{{ route('admin.danzas.restore', $danza->cod_danza) }}" method="POST" class="restore-form">
                                    @csrf @method('PATCH')
                                    <button type="button"
                                            onclick="confirmarRestauro(this)"
                                            class="p-2 bg-green-50 text-andes-verde rounded-xl hover:scale-110 transition shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </form>

                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MODAL DE DETALLES (Permanece en el Index) --}}
        {{-- MODAL COMPLETO ACTUALIZADO --}}
<div x-show="openDetail"
     x-cloak
     style="display:none"
     class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-6">

    <div class="bg-white w-full max-w-2xl rounded-[2rem] overflow-hidden shadow-2xl"
         @click.away="openDetail=false">

        {{-- CABECERA CON IMAGEN COMPLETA --}}
        <div class="h-72 bg-gray-100 relative flex items-center justify-center overflow-hidden">

            {{-- Imagen completa pequeña --}}
            <img x-show="currentDanza.imagen_danza"
                 :src="'/storage/' + currentDanza.imagen_danza"
                 class="max-w-full max-h-full object-contain p-4">

            {{-- Sin imagen --}}
            <div x-show="!currentDanza.imagen_danza"
                 class="w-full h-full flex items-center justify-center text-gray-300 font-bold uppercase">
                Sin Imagen
            </div>

            {{-- Cerrar --}}
            <button @click="openDetail=false"
                    class="absolute top-5 right-5 bg-black/40 text-white p-2 rounded-full hover:bg-red-500 transition">
                ✕
            </button>

        </div>

        {{-- CONTENIDO --}}
        <div class="p-10">

            <div class="flex gap-3 mb-4">

                <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-[10px] font-black uppercase"
                      x-text="currentDanza.clasificacion">
                </span>

                <template x-if="currentDanza.deleted_at">
                    <span class="bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">
                        Inactiva
                    </span>
                </template>

            </div>

            <h2 class="text-4xl font-black text-gray-800 uppercase mb-6"
                x-text="currentDanza.nom_danza">
            </h2>

            <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">
                Descripción
            </h4>

            <p class="text-gray-500 leading-relaxed italic"
               x-text="currentDanza.descripcion ? currentDanza.descripcion : 'Sin descripción registrada.'">
            </p>

            <div class="mt-8 pt-6 border-t border-gray-100 text-right">

                <button @click="openDetail=false"
                        class="text-gray-400 font-black uppercase text-[10px] tracking-widest hover:text-andes-verde">
                    Cerrar
                </button>

            </div>

        </div>

    </div>
</div>

    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarBajaDanza(btn) {
        const form = btn.closest('form');
        Swal.fire({
            title: '¿Eliminar danza?',
            text: 'La danza quedará inactiva. Podrás restaurarla después.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl font-sans',
                confirmButton: 'rounded-xl font-black uppercase text-xs',
                cancelButton: 'rounded-xl font-black uppercase text-xs',
            }
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    }

    function confirmarRestauro(btn) {
        const form = btn.closest('form');
        Swal.fire({
            title: '¿Restaurar danza?',
            text: 'La danza volverá a estar activa en el catálogo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007a3d',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl font-sans',
                confirmButton: 'rounded-xl font-black uppercase text-xs',
                cancelButton: 'rounded-xl font-black uppercase text-xs',
            }
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
        
    }
</script>
</x-app-layout>
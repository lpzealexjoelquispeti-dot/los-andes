<x-app-layout>
    <x-slot name="header">
        Resumen
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl mb-8 border-l-4 border-andes-verde">
            <div class="p-8 sm:px-10 flex flex-col md:flex-row items-center justify-between">
                <div>
                    <h3 class="text-3xl font-black text-andes-oscuro mb-2">¡Hola, {{ Auth::user()->name }}!</h3>
                    
                    @role('SuperAdmin')
                        <p class="text-gray-600 text-lg">Tienes el control total de Los Andes. Revisa las tiendas pendientes de aprobación.</p>
                    @endrole
                    
                    @role('Vendedor')
                        <p class="text-gray-600 text-lg">Gestiona tu tienda y tu catálogo de trajes. ¡Mantén tu inventario actualizado para los fraternos!</p>
                    @endrole
                    
                    @role('Cliente')
                        <p class="text-gray-600 text-lg">Explora la calle Los Andes digital. Busca el traje perfecto para tu próxima entrada folklórica.</p>
                    @endrole
                </div>
                
                <div class="mt-6 md:mt-0 hidden md:block">
                    <div class="w-24 h-24 rounded-full bg-green-50 flex items-center justify-center text-andes-verde">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @role('SuperAdmin')
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Tiendas Pendientes</h4>
                    <p class="text-4xl font-black text-andes-amarillo">0</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Usuarios Registrados</h4>
                    <p class="text-4xl font-black text-andes-verde">3</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Términos en Tesauro</h4>
                    <p class="text-4xl font-black text-andes-rojo">0</p>
                </div>
            @endrole

            @role('Vendedor')
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-center flex-col text-center">
                    <div class="w-16 h-16 bg-red-50 text-andes-rojo rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h4 class="font-bold text-gray-800">Añadir Nuevo Traje</h4>
                </div>
            @endrole
        </div>
    </div>
</x-app-layout>
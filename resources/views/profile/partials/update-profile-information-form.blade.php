<section x-data="{ photoPreview: null }">
    <header>
        <h2 class="text-xl font-semibold text-white">
            {{ __('Información del Perfil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            {{ __("Datos personales y fotografía. El correo electrónico es gestionado por el administrador.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex items-center gap-6">
            <div class="relative">
                <template x-if="photoPreview">
                    <img :src="photoPreview" class="h-48 w-48 rounded-xl object-cover border-4 border-andes-rojo shadow-2xl">
                </template>
                
                <template x-if="!photoPreview">
                    <img src="{{ $user->foto_perfil ? asset('storage/'.$user->foto_perfil) : asset('img/default-avatar.png') }}" 
                         class="h-48 w-48 rounded-xl object-cover border-4 border-gray-800 shadow-xl">
                </template>

                <label for="foto_perfil" class="absolute -bottom-3 -right-3 bg-andes-rojo p-3 rounded-full cursor-pointer hover:bg-red-700 transition-all shadow-lg group">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                    <input id="foto_perfil" name="foto_perfil" type="file" class="hidden" 
                           @change="
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL($event.target.files[0]);
                           " />
                </label>
            </div>
            <div class="text-gray-400 text-sm">
                <p class="font-bold text-white mb-1">Imagen de Identidad</p>
                <p>Formatos permitidos: JPG, PNG.</p>
                <p>Tamaño máximo: 2MB.</p>
            </div>
        </div>

        <hr class="border-gray-800">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nombres')" class="text-gray-300" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-andes-dark border-gray-700 text-black" 
                    :value="old('name', $user->name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Correo Electrónico (No editable)')" class="text-gray-900" />
                <x-text-input id="email" name="email" type="email" 
                    class="mt-1 block w-full bg-gray-900 border-gray-800 text-gray-500 cursor-not-allowed" 
                    :value="$user->email" readonly />
                <p class="text-xs text-gray-600 mt-1">Contacta a soporte para cambiar tu correo.</p>
            </div>

            <div>
                <x-input-label for="ap_pat" :value="__('Apellido Paterno')" class="text-gray-900" />
                <x-text-input id="ap_pat" name="ap_pat" type="text" class="mt-1 block w-full bg-andes-dark border-gray-700 text-black" 
                    :value="old('ap_pat', $user->ap_pat)" required />
                <x-input-error class="mt-2" :messages="$errors->get('ap_pat')" />
            </div>

            <div>
                <x-input-label for="ap_mat" :value="__('Apellido Materno')" class="text-gray-900" />
                <x-text-input id="ap_mat" name="ap_mat" type="text" class="mt-1 block w-full bg-andes-dark border-gray-700 text-black" 
                    :value="old('ap_mat', $user->ap_mat)" />
                <x-input-error class="mt-2" :messages="$errors->get('ap_mat')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-andes-rojo hover:bg-red-700 shadow-lg shadow-red-900/20 transition-all">
                {{ __('Actualizar Perfil') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-400">
                    {{ __('Cambios guardados.') }}
                </p>
            @endif
        </div>
    </form>
</section>
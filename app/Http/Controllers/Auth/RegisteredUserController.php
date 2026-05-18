<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string','max:255', 'regex:/^[A-ZÑÁÉÍÓÚ][a-zA-Z\sñÑáéíóúÁÉÍÓÚ]*$/u'],
            'ap_pat' => ['required', 'string', 'max:255', 'regex:/^[A-ZÑÁÉÍÓÚ][a-zA-Z\sñÑáéíóúÁÉÍÓÚ]*$/u'],
            'ap_mat' => ['nullable', 'string', 'max:255', 'regex:/^[A-ZÑÁÉÍÓÚ][a-zA-Z\sñÑáéíóúÁÉÍÓÚ]*$/u'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            // Validación estricta de contraseña
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ], [
            // MENSAJES PERSONALIZADOS PARA EL EMAIL
            'email.required' => 'El correo electrónico es obligatorio para registrarse.',
            'email.email' => 'Por favor, ingresa un formato de correo válido (ej. juan@correo.com).',
            'email.unique' => 'Este correo ya está registrado en el sistema Los Andes. ¿Olvidaste tu contraseña?',
            'email.max' => 'El correo es demasiado largo (máximo 255 caracteres).',
            
            // MENSAJES PARA NOMBRES Y APELLIDOS
            'name.required' => 'Debes ingresar tu nombre.',
            'name.regex' => 'El nombre solo puede contener letras y espacios, sin números ni símbolos y empezar con letra mayúsculal',
            'ap_pat.required' => 'El apellido paterno es obligatorio.',
            'ap_pat.regex' => 'El apellido paterno solo puede contener letras y empezar con mayúscula',
            'ap_mat.regex' => 'El apellido materno solo puede contener letras y empezar con mayúscula',
            
            // MENSAJES PARA LA CONTRASEÑA
            'password.required' => 'Debes crear una contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden. Vuelve a intentarlo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            // Nota: Laravel genera automáticamente los mensajes de letters, mixedCase, etc., en inglés si no tienes el paquete de idioma español. 
            // Si necesitas esos sub-mensajes específicos de Password::class en español, se manejan desde los archivos de idioma (lang).
        ]);

        $user = User::create([
            'name' => $request->name,
            'ap_pat' => $request->ap_pat,
            'ap_mat' => $request->ap_mat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

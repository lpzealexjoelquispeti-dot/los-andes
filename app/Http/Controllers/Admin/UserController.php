<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\UserCredencialesMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Mail\ClienteCredencialesMail;
use App\Mail\VendedorCredencialesMail;
use App\Mail\AdminCredencialesMail;
 // 💡 Importante: Importamos el modelo de Spatie

class UserController extends Controller
{
    public function index()
    {
        // Eager loading para evitar el problema N+1 con Spatie
        $usuarios = User::withTrashed()->with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('usuarios'));
    }

    public function create()
    {
        // 💡 Jalamos todos los roles creados en Spatie para el @foreach de tu vista
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'ap_pat' => ['required', 'string', 'max:255'],
        'ap_mat' => ['nullable', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'rol' => ['required', 'string', 'exists:roles,name'],
    ]);

    // Generar contraseña híbrida aleatoria
    $baseApellido = ucfirst(preg_replace('/[^A-Za-z0-9]/', '', trim($request->ap_pat)));
    $passwordPlana = $baseApellido . rand(100, 999) . '$' . \Illuminate\Support\Str::random(3);

    // 1. Guardar datos del usuario
    $usuario = User::create([
        'name' => $request->name,
        'ap_pat' => $request->ap_pat,
        'ap_mat' => $request->ap_mat,
        'email' => $request->email,
        'password' => Hash::make($passwordPlana),
    ]);

    // 2. ⚡ SOLUCIÓN AL BUG: syncRoles limpia cualquier rol por defecto y clava el seleccionado
    // 1. Sincronizamos el rol en Spatie (Spatie sí reconoce el nombre tal cual está en la DB)
    $usuario->syncRoles([$request->rol]);

    // 2. 🚀 Convertimos el rol a minúsculas para blindar el switch contra mayúsculas
    $rolNormalizado = strtolower(trim($request->rol));

    // 3. Despachamos el correo específico sin fallas de coincidencia
    switch ($rolNormalizado) {
        case 'vendedor':
            Mail::to($usuario->email)->send(new VendedorCredencialesMail($usuario, $passwordPlana));
            break;

        case 'admin':
            Mail::to($usuario->email)->send(new AdminCredencialesMail($usuario, $passwordPlana));
            break;

        case 'cliente':
        default:
            Mail::to($usuario->email)->send(new ClienteCredencialesMail($usuario, $passwordPlana));
            break;
    }

    return redirect()->route('admin.users.index')->with('status', 'usuario-creado');

  
}

   public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ap_pat' => ['required', 'string', 'max:255'], // Campo obligatorio de tu DB
            'ap_mat' => ['nullable', 'string', 'max:255'], // Campo opcional
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'rol' => ['required', 'string', 'exists:roles,name'],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'ap_pat.required' => 'El apellido paterno es requerido.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
        ]);

        // 🛡️ SEGURIDAD: Solo actualizamos datos demográficos, nunca contraseñas desde aquí
        $user->update([
            'name' => $request->name,
            'ap_pat' => $request->ap_pat,
            'ap_mat' => $request->ap_mat,
            'email' => $request->email,
        ]);

        // Sincronizamos el rol de Spatie
        $user->syncRoles([$request->rol]);

        return redirect()->route('admin.users.index')->with('status', 'usuario-actualizado');
    }

    /**
     * 🔐 MÉTODO ÓPTIMO: Regeneración de Clave Temporal y Auditoría Automática
     */
    public function regeneratePassword($id)
{
    $user = User::withTrashed()->findOrFail($id);

    // Generamos la clave híbrida usando su apellido paterno
    $baseApellido = ucfirst(preg_replace('/[^A-Za-z0-9]/', '', trim($user->ap_pat)));
    $passwordPlana = $baseApellido . rand(100, 999) . '$' . \Illuminate\Support\Str::random(3);

    // Guardamos la nueva contraseña (esto dispara automáticamente el Observer para auditoría)
    $user->update([
        'password' => Hash::make($passwordPlana)
    ]);

    // 🚀 ENVIAR EL NUEVO CORREO ESPECIALIZADO DE RESTAURACIÓN
    Mail::to($user->email)->send(new \App\Mail\RestaurarClaveMail($user, $passwordPlana));

    return redirect()->back()->with('status', 'clave-regenerada');
}

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore(); // Restauración

        return redirect()->route('admin.users.index')->with('status', 'usuario-restaurado');
    }
}
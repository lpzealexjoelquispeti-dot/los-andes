<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie; // <-- IMPORTANTE: Agregamos las cookies de Laravel
use Illuminate\View\View;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Autentica al usuario usando las credenciales enviadas
        $request->authenticate();

        $request->session()->regenerate();

        // --- TRUCO PARA GUARDAR EL GMAIL Y LA CONTRASEÑA ---
        // Revisamos si el usuario marcó la casilla de "Recordar"
        if ($request->has('remember')) {
            // Guardamos el email y la contraseña en cookies por 30 días (43200 minutos)
            Cookie::queue('user_email', $request->email, 43200);
            Cookie::queue('user_password', $request->password, 43200);
        } else {
            // Si no la marcó, borramos las cookies por seguridad para que queden limpias
            Cookie::queue(Cookie::forget('user_email'));
            Cookie::queue(Cookie::forget('user_password'));
        }
        // ----------------------------------------------------

        $user = Auth::user();

        // Redirecciones según el rol (Mantenemos la lógica de tu equipo intacta)
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('Vendedor')) {
            return redirect()->route('vendedor.dashboard');
        }

        return redirect()->route('inicio');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
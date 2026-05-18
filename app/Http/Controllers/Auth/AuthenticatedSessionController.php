<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        // Valida credenciales e inicia sesión procesando el checkbox 'remember'
        $request->authenticate();

        // Regenera la sesión para evitar ataques de fijación de sesión
        $request->session()->regenerate();

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Redirección dinámica basada en Roles de Spatie
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('Vendedor')) {
            return redirect()->route('vendedor.dashboard');
        }

        // Ruta por defecto si no cumple con los roles anteriores
        return redirect()->intended(route('inicio', absolute: false));
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
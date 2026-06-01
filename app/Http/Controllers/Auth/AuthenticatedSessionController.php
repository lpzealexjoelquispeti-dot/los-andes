<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if ($request->boolean('remember')) {
            Cookie::queue('user_email', $request->email, 43200);
        } else {
            Cookie::queue(Cookie::forget('user_email'));
        }

        $user = Auth::user();

        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Vendedor')) {
            return redirect()->route('vendedor.dashboard');
        }

        return redirect()->intended(route('inicio', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

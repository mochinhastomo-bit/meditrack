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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi administrator.']);
        }

        if ($user->isKurir()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun kurir hanya dapat digunakan melalui aplikasi Android MediTrack.']);
        }

        return match($user->role) {
            'superadmin' => redirect()->route('admin.dashboard'),
            'farmasi'    => redirect()->route('farmasi.dashboard'),
            default      => redirect()->route('login')->withErrors(['email' => 'Role tidak dikenali. Hubungi administrator.']),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

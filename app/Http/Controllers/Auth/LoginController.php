<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'));

        if (!$attempt) {
            throw ValidationException::withMessages([
                'username' => 'Kredensial tidak cocok dengan catatan kami.',
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('toast', ['type' => 'success', 'message' => "Selamat datang kembali, {$user->name}! 🎉"]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

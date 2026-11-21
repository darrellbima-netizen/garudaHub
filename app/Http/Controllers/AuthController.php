<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Login pengguna
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Memeriksa email dan password
        if (!Auth::attempt($credentials)) {

            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Email atau password salah.',
                ], 401)
                : back()->withErrors([
                    'email' => 'Email atau password salah.',
                ])->onlyInput('email');
        }

        // Mencegah session fixation
        $request->session()->regenerate();

        $user = $request->user();

        return $request->expectsJson()
            ? response()->json([
                'message' => 'Login berhasil.',
                'user' => $user->only([
                    'id',
                    'nama',
                    'email',
                    'role',
                ]),
            ])
            : redirect()->intended('/');
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        Auth::logout();

        // Menghapus session lama
        $request->session()->invalidate();

        // Membuat token CSRF baru
        $request->session()->regenerateToken();

        return $request->expectsJson()
            ? response()->json([
                'message' => 'Logout berhasil.',
            ])
            : redirect('/login');
    }
}

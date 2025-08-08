<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function login()
    {
        return view('admin.pages.auth.login');
    }

    /**
     * Memproses upaya autentikasi dari form login.
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi input dari form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba lakukan login
        if (Auth::attempt($credentials)) {
            // Jika berhasil, regenerate session untuk keamanan
            $request->session()->regenerate();

            // ===================================================================
            // PERUBAHAN DI SINI: Redirect berdasarkan role pengguna
            // ===================================================================
            $user = Auth::user();

            if ($user->role == 'admin' || $user->role == 'superadmin') {
                return redirect()->route('dashboard');
            } elseif ($user->role == 'user') {
                return redirect()->route('user.dashboard');
            }

            // Fallback jika role tidak terdefinisi (seharusnya tidak terjadi)
            return redirect('/login');
        }

        // 3. Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

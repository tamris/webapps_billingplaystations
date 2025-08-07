<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Wajib di-import

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

            // Redirect ke halaman yang dituju sebelumnya, atau ke dashboard jika tidak ada
            return redirect()->intended('/dashboard');
        }

        // 3. Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Kembalikan ke form dengan pesan error dan input email
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
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    //
    public function register()
    {
        return view('admin.pages.auth.register');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (tanpa role)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Buat User Baru (role di-hardcode menjadi 'admin')
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // <-- OTOMATIS MENJADI ADMIN
        ]);

        // 3. Redirect
        return redirect('/login')->with('success', 'Registrasi berhasil! Akun Anda telah dibuat sebagai Admin.');
    }
}

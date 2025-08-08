<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        // 1. Ambil data pengguna dari Google
        $googleUser = Socialite::driver('google')->user();

        // 2. Cari pengguna di database, atau buat baru jika tidak ada
        // PERBAIKAN 1: Menggunakan 'updateOrCreate' agar lebih efisien
        $user = User::updateOrCreate(
            [
                'email' => $googleUser->getEmail(),
            ],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(24)), // Password acak karena tidak dipakai
                'role' => 'user', // <-- PERBAIKAN 2: Selalu set role sebagai 'user'
            ]
        );

        // 3. Login-kan pengguna
        Auth::login($user);

        // 4. PERBAIKAN 3: Arahkan ke dashboard khusus user
        return redirect()->route('user.dashboard');
    }
}

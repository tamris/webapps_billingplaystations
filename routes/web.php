<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth'])->group(function () {

    // == GRUP UNTUK ROLE 'admin' DAN 'superadmin' ==
    Route::middleware(['role:admin,superadmin'])->group(function () {
        
        // Dashboard bisa diakses oleh admin dan superadmin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // PERUBAHAN: Rute 'barang' sekarang langsung di sini
        // URL-nya menjadi /barang, /barang/create, dst.
        Route::resource('barang', BarangController::class);

    });


    // == GRUP KHUSUS UNTUK ROLE 'superadmin' ==
    Route::middleware(['role:superadmin'])->group(function () {
        
        // Manajemen User hanya bisa diakses oleh superadmin
        Route::resource('users', UserController::class);
        
    });

});

// Beri nama 'login' pada route login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Tambahan untuk proses logout

// Beri nama 'register' pada route register (INI PERBAIKANNYA)
Route::get('/register', [RegisterController::class, 'register'])->name('register');

// Jangan lupa, route POST untuk memproses form juga perlu ada
// Kita beri nama 'register.store' agar tidak bentrok,
// tapi karena action di form-mu mengarah ke 'register' maka kita perlu route POST juga
Route::post('/register', [RegisterController::class, 'store']);
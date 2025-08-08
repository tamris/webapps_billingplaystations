<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;



// Route::get('/', function () {
//     return view('index');
// });

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

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


Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    
    // Menggunakan controller yang sudah Anda buat
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    // Tambahkan route user lainnya di sini...

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
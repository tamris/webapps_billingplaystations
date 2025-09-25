<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaystationController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Admin\ReportController;
use App\Models\PlaySession;
use Illuminate\Support\Facades\Route;


Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);


Route::middleware(['auth'])->group(function () {
    // tampilkan list ke semua role (admin & operator)
    Route::get('/ps', [PlaystationController::class, 'index'])
        ->middleware('role:admin,operator')
        ->name('playstations.index');

    // CRUD khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('playstations', PlaystationController::class)
            ->except(['index', 'show']);
        Route::resource('users', UserController::class);
    });
});

// routes/web.php

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/revenue/export', [ReportController::class, 'exportRevenue'])->name('reports.revenue.export');
});



Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ps/{ps}/start', [PlaySession::class, 'start'])->name('ps.start');
    Route::post('/ps/{ps}/stop',  [PlaySession::class, 'stop'])->name('ps.stop');
    Route::get('/sessions', [PlaySession::class, 'index'])->name('sessions.index');
});

Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/sessions', [AdminSessionController::class, 'index'])->name('sessions.index');
    Route::post('/ps/{ps}/start', [AdminSessionController::class, 'start'])->name('ps.start');
    Route::post('/ps/{ps}/stop',  [AdminSessionController::class, 'stop'])->name('ps.stop');
});


// Beri nama 'login' pada route login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Tambahan untuk proses logout

// Beri nama 'register' pada route register (INI PERBAIKANNYA)
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

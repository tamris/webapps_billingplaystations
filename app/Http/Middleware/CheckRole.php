<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // Menerima parameter role (e.g., 'superadmin', 'admin')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user tidak login atau rolenya tidak ada di dalam daftar yang diizinkan
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Tampilkan halaman error 403 (Forbidden)
            abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        $users = User::latest()->get(); // Mengambil data terbaru
        return view('admin.pages.users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        return view('admin.pages.users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'superadmin'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User baru berhasil dibuat!');
    }

    /**
     * Menampilkan form untuk mengedit user.
     * Menggunakan Route Model Binding (User $user) untuk otomatis mencari user berdasarkan ID.
     */
    public function edit(User $user)
    {
        return view('admin.pages.users.edit', compact('user'));
    }

    /**
     * Memperbarui data user di database.
     */
    public function update(Request $request, User $user)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],

            // PERUBAHAN DI SINI: Validasi role menjadi kondisional
            'role' => [
                // Field 'role' hanya 'required' JIKA user yg login TIDAK SAMA DENGAN user yg diedit
                Rule::requiredIf(Auth::id() != $user->id),
                Rule::in(['admin', 'superadmin'])
            ],

            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // ... sisa kode method update tidak perlu diubah ...
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Hanya update role jika user yang sedang login tidak mengedit dirinya sendiri
        // dan jika data role memang dikirimkan (tidak disabled)
        if (Auth::id() !== $user->id && isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Keamanan: Jangan biarkan user menghapus dirinya sendiri
        if (Auth::id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}

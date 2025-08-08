<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('admin.pages.barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('admin.pages.barang.create');
    }

    // ===================================================================
    // PERUBAHAN HANYA DI SINI
    // ===================================================================
    public function store(Request $request)
    {
        // Validasi input tetap sama
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan data baru ke database tetap sama
        Barang::create($request->all());

        // Ganti redirect dengan respon JSON agar bisa dibaca oleh JavaScript
        return response()->json(['message' => 'Barang berhasil ditambahkan.']);
    }
    // ===================================================================
    // METHOD LAINNYA TIDAK PERLU DIUBAH
    // ===================================================================

    public function edit(Barang $barang)
    {
        return view('admin.pages.barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        // Validasi input tetap sama
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);

        // Update data barang di database tetap sama
        $barang->update($request->all());

        // Ganti redirect dengan respon JSON agar bisa dibaca oleh JavaScript
        return response()->json(['message' => 'Barang berhasil diperbarui.']);
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}

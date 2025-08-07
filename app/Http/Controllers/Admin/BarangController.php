<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all(); // Assuming you have a Barang model to fetch data
        return view('admin.pages.barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('admin.pages.barang.create');
    }

     public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan data baru ke database
        Barang::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil ditambahkan.');
    }

        public function edit(Barang $barang)
    {
        // Menampilkan halaman form edit dengan data barang yang akan diedit
        return view('admin.pages.barang.edit', compact('barang'));
    }
     public function update(Request $request, Barang $barang)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);
        
        // Update data barang di database
        $barang->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil diperbarui.');
    }
      public function destroy(Barang $barang)
    {
        // Hapus data barang dari database
        $barang->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil dihapus.');
    }
}

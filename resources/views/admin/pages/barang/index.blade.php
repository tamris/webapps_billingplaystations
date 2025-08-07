@extends('admin.layouts.base')

@section('title', 'Daftar Barang')

@section('content')
    <div class="container-fluid">
        {{-- Judul Halaman --}}
        <h1 class="h3 mb-2 text-gray-800">Daftar Barang</h1>

        {{-- Card Utama untuk Tabel --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">Tambah Barang</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {{-- Tabel untuk menampilkan data --}}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                {{-- Mengatur lebar kolom header --}}
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Nama Barang</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 35%;">Deskripsi</th>
                                <th style="width: 10%;">Stok</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Perulangan untuk menampilkan setiap baris data barang --}}
                            @foreach ($barangs as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    {{-- Memotong teks deskripsi agar tidak terlalu panjang --}}
                                    <td>{{ Str::limit($barang->deskripsi, 140) }}</td>
                                    <td>{{ $barang->stok }}</td>
                                    <td>
                                        {{-- Tombol Aksi --}}
                                        <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        
                                        {{-- PERUBAHAN DI SINI: Tombol Hapus sekarang memicu SweetAlert --}}
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            {{-- Hapus onclick dan ubah type menjadi button --}}
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#dataTable').DataTable();

        // PERUBAHAN DI SINI: Script untuk konfirmasi hapus dengan SweetAlert
        $('.form-delete').on('submit', function(e) {
            e.preventDefault(); // Mencegah form untuk langsung di-submit
            var form = this;

            Swal.fire({
                title: 'Anda yakin?',
                text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Jika dikonfirmasi, submit form
                }
            })
        });
    });
</script>
@endpush

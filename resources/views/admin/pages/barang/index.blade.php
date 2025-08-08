@extends('admin.layouts.base')

@section('title', 'Daftar Barang')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Daftar Barang</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                {{-- Tombol untuk memicu modal tambah --}}
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBarangModal">
                    Tambah Barang
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Nama Barang</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 35%;">Deskripsi</th>
                                <th style="width: 10%;">Stok</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td>{{ Str::limit($barang->deskripsi, 140) }}</td>
                                    <td>{{ $barang->stok }}</td>
                                    <td>
                                        {{-- Tombol Edit sekarang memicu modal dan membawa data --}}
                                        <button class="btn btn-warning btn-sm btn-edit" 
                                                data-toggle="modal" 
                                                data-target="#editBarangModal"
                                                data-id="{{ $barang->id }}"
                                                data-nama_barang="{{ $barang->nama_barang }}"
                                                data-harga="{{ $barang->harga }}"
                                                data-stok="{{ $barang->stok }}"
                                                data-deskripsi="{{ $barang->deskripsi }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
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

    <!-- =================================================================== -->
    <!-- MODAL UNTUK TAMBAH BARANG (TETAP SAMA) -->
    <!-- =================================================================== -->
    <div class="modal fade" id="addBarangModal" tabindex="-1" role="dialog" aria-labelledby="addBarangModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBarangModalLabel">Tambah Barang Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" required>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi (Opsional)</label>
                            <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL (POP-UP) UNTUK EDIT BARANG -->
    <!-- =================================================================== -->
    <div class="modal fade" id="editBarangModal" tabindex="-1" role="dialog" aria-labelledby="editBarangModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBarangModalLabel">Edit Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- Form untuk edit, action URL akan diatur oleh JavaScript --}}
                <form id="editForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        @method('PUT') {{-- Method spoofing untuk update --}}
                        <div class="form-group">
                            <label for="edit_nama_barang">Nama Barang</label>
                            <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_harga">Harga</label>
                            <input type="number" class="form-control" id="edit_harga" name="harga" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_stok">Stok</label>
                            <input type="number" class="form-control" id="edit_stok" name="stok" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_deskripsi">Deskripsi (Opsional)</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#dataTable').DataTable();

        // Script AJAX untuk Tambah Barang (Tetap Sama)
        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('barang.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#addBarangModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan. Pastikan semua kolom terisi.');
                }
            });
        });

        // Script untuk menangani Modal Edit
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var nama_barang = $(this).data('nama_barang');
            var harga = $(this).data('harga');
            var stok = $(this).data('stok');
            // PERBAIKAN KESALAHAN KETIK DI SINI
            var deskripsi = $(this).data('deskripsi');

            $('#edit_nama_barang').val(nama_barang);
            $('#edit_harga').val(harga);
            $('#edit_stok').val(stok);
            $('#edit_deskripsi').val(deskripsi);

            // Menyesuaikan URL dengan route Anda (tanpa /admin)
            var url = "{{ url('barang') }}/" + id;
            $('#editForm').attr('action', url);
        });

        // Script AJAX untuk Update Barang
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#editBarangModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });
                        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: errorMessage });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                    }
                }
            });
        });

        // Script untuk konfirmasi hapus (Tetap Sama)
        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
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
                    form.submit();
                }
            })
        });
    });
</script>
@endpush

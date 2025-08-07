@extends('admin.layouts.users')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container-fluid">
        {{-- Judul Halaman --}}
        <h1 class="h3 mb-2 text-gray-800">Manajemen Pengguna</h1>

        {{-- Card Utama untuk Tabel --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                {{-- PERUBAHAN: Tombol dan route mengarah ke 'users.create' --}}
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah User Baru</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                {{-- PERUBAHAN: Header tabel disesuaikan untuk data user --}}
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Nama</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 15%;">Level</th>
                                <th style="width: 15%;">Tanggal Bergabung</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- PERUBAHAN: Loop data $users --}}
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{-- Memberi badge agar role terlihat lebih menarik --}}
                                        @if ($user->role == 'superadmin')
                                            <span class="badge badge-success">Super Admin</span>
                                        @else
                                            <span class="badge badge-info">Admin</span>
                                        @endif
                                    </td>
                                    {{-- Format tanggal agar lebih mudah dibaca --}}
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        {{-- PERUBAHAN: Route aksi mengarah ke 'users.edit' dan 'users.destroy' --}}
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        
                                        {{-- PENTING: Jangan izinkan user menghapus dirinya sendiri --}}
                                        @if(auth()->user()->id !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                        @endif
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
{{-- Script SweetAlert dan DataTables tidak perlu diubah sama sekali --}}
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#dataTable').DataTable();

        // Script untuk konfirmasi hapus dengan SweetAlert
        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Anda yakin?',
                text: "Data user yang sudah dihapus tidak dapat dikembalikan!",
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
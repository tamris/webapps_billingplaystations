@extends('admin.layouts.users') {{-- Sesuaikan dengan nama layout utamamu --}}

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit User: {{ $user->name }}</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                {{-- Form untuk mengedit user --}}
                {{-- PERBEDAAN KUNCI: Route dan Method --}}
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Method spoofing untuk request UPDATE --}}

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required {{ auth()->user()->id == $user->id ? 'disabled' : '' }}>
                            <option value="">Pilih Role...</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @if(auth()->user()->id == $user->id)
                            <small class="form-text text-muted">Anda tidak dapat mengubah role diri sendiri.</small>
                        @endif
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
                    
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@extends('admin.layouts.dataPS')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Daftar PlayStation</h4>
    @if(auth()->user()->role === 'admin')
      <a href="{{ route('playstations.create') }}" class="btn btn-primary">Tambah PS</a>
    @endif
  </div>

  <form class="mb-3" method="get" action="{{ route('playstations.index') }}">
    <div class="input-group" style="max-width:420px">
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari code/nama...">
      <button class="btn btn-outline-secondary">Cari</button>
    </div>
  </form>

  <div class="table-responsive">
    <table id="dataTable" class="table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">No</th>
          <th>Kode</th>
          <th>Nama</th>
          <th>Harga/Jam</th>
          <th>Status</th>
          @if(auth()->user()->role === 'admin') <th style="width:160px;">Aksi</th> @endif
        </tr>
      </thead>
      <tbody>
        @forelse($ps as $i => $item)
          <tr>
            {{-- pakai $loop->iteration untuk nomor urut --}}
            <td>{{ $loop->iteration }}</td>
            <td><span class="fw-semibold">{{ $item->code }}</span></td>
            <td>{{ $item->name ?? '-' }}</td>
            <td>Rp {{ number_format($item->price_per_hour,0,',','.') }}</td>
            <td>
              @php $badge = $item->statusBadge(); @endphp
              <span class="badge bg-{{ $badge }}">
                {{ ucfirst(str_replace('_',' ', $item->status)) }}
              </span>
            </td>
            @if(auth()->user()->role === 'admin')
              <td>
                <a href="{{ route('playstations.edit',$item) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('playstations.destroy',$item) }}" method="post" class="d-inline form-delete">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            @endif
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
  $('#dataTable').DataTable({
  pageLength: 10,
  language: {
    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json',
    info: "_START_ - _END_ / _TOTAL_"

  },
  columnDefs: [
    { targets: -1, orderable: false, searchable: false }, // kolom Aksi
    { targets: 0,  orderable: false, searchable: false }  // kolom No
  ],
  order: [[1,'asc']]
});


  // Konfirmasi hapus pakai SweetAlert
  $('.form-delete').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
      title: 'Anda yakin?',
      text: 'Data yang sudah dihapus tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((r) => {
      if (r.isConfirmed) form.submit();
    });
  });
});
</script>
@endpush

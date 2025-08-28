@extends('admin.layouts.reports')
@section('title','Rekap Pendapatan')



@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Rekap Pendapatan</h3>

  {{-- Filter tanggal --}}
  <form class="row g-2 mb-3" method="get" action="{{ route('reports.revenue') }}">
    <div class="col-auto">
      <label class="form-label">Dari</label>
      <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>
    <div class="col-auto">
      <label class="form-label">Sampai</label>
      <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>
    <div class="col-auto align-self-end">
      <button class="btn btn-primary">Filter</button>
      <a class="btn btn-outline-secondary"
         href="{{ route('reports.revenue.export', ['from'=>$from,'to'=>$to]) }}">
        Export CSV
      </a>
    </div>
  </form>

  {{-- Ringkasan --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card p-3">
        <div class="text-muted small">Total Pendapatan</div>
        <div class="h4 mb-0">Rp {{ number_format($total,0,',','.') }}</div>
      </div>
    </div>
  </div>

  {{-- Per Hari --}}
  <div class="card mb-4">
    <div class="card-header fw-bold">Per Hari</div>
    <div class="card-body p-0">
      <table id="tblPerHari" class="table table-hover mb-0">
        <thead>
          <tr><th>Tanggal</th><th>Sesi</th><th>Total Menit</th><th>Pendapatan</th></tr>
        </thead>
        <tbody>
        @forelse($perHari as $r)
          @php
            $tglSort = \Carbon\Carbon::parse($r->tgl)->format('Y-m-d');   // untuk sorting
            $tglShow = \Carbon\Carbon::parse($r->tgl)->format('d-m-Y');   // tampilan
          @endphp
          <tr>
            <td data-order="{{ $tglSort }}">{{ $tglShow }}</td>
            <td data-order="{{ (int)$r->sesi }}">{{ $r->sesi }}</td>
            <td data-order="{{ (int)$r->total_menit }}">{{ $r->total_menit }}</td>
            <td data-order="{{ (int)$r->pendapatan }}">Rp {{ number_format($r->pendapatan,0,',','.') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Per PlayStation --}}
  <div class="card">
    <div class="card-header fw-bold">Per PlayStation</div>
    <div class="card-body p-0">
      <table id="tblPerPs" class="table table-hover mb-0">
        <thead>
          <tr><th>PS</th><th>Nama</th><th>Sesi</th><th>Total Menit</th><th>Pendapatan</th></tr>
        </thead>
        <tbody>
        @forelse($perPs as $r)
          @php
            $pend = (int)$r->pendapatan;
            $sesi = (int)$r->sesi;
            $mnt  = (int)$r->total_menit;
            $code = $r->playstation->code ?? '';
          @endphp
          <tr>
            <td data-order="{{ $code }}">{{ $code ?: '-' }}</td>
            <td>{{ $r->playstation->name ?? '-' }}</td>
            <td data-order="{{ $sesi }}">{{ $sesi }}</td>
            <td data-order="{{ $mnt }}">{{ $mnt }}</td>
            <td data-order="{{ $pend }}">Rp {{ number_format($pend,0,',','.') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
  $(function () {
    // Tabel Per Hari: 3/baris, tanpa search & length change
    $('#tblPerHari').DataTable({
      pageLength: 3,
      lengthChange: false,
      searching: false,
      order: [[0,'desc']],
      language: {
        lengthMenu: "Tampilkan _MENU_ entri",
        search: "Cari:",
        zeroRecords: "Tidak ada data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
        paginate: { previous: "Sebelumnya", next: "Selanjutnya" }
      }
    });

    // Tabel Per PS: 10/baris, search aktif
    $('#tblPerPs').DataTable({
      pageLength: 10,
      lengthChange: false,
      searching: true,
      order: [[4,'desc']], // sort by Pendapatan
      language: {
        lengthMenu: "Tampilkan _MENU_ entri",
        search: "Cari:",
        zeroRecords: "Tidak ada data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
        paginate: { previous: "Sebelumnya", next: "Selanjutnya" }
      }
    });
  });
  </script>
@endpush

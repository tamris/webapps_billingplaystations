@extends('admin.layouts.dashboard')
@section('title','Rekap Pendapatan')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Rekap Pendapatan</h3>

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

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card p-3">
        <div class="text-muted small">Total Pendapatan</div>
        <div class="h4 mb-0">Rp {{ number_format($total,0,',','.') }}</div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">Per Hari</div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr><th>Tanggal</th><th>Sesi</th><th>Total Menit</th><th>Pendapatan</th></tr>
        </thead>
        <tbody>
          @forelse($perHari as $r)
            <tr>
              <td>{{ \Carbon\Carbon::parse($r->tgl)->format('d-m-Y') }}</td>
              <td>{{ $r->sesi }}</td>
              <td>{{ $r->total_menit }}</td>
              <td>Rp {{ number_format($r->pendapatan,0,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header fw-bold">Per PlayStation</div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr><th>PS</th><th>Nama</th><th>Sesi</th><th>Total Menit</th><th>Pendapatan</th></tr>
        </thead>
        <tbody>
          @forelse($perPs as $r)
            <tr>
              <td>{{ $r->playstation->code ?? '-' }}</td>
              <td>{{ $r->playstation->name ?? '-' }}</td>
              <td>{{ $r->sesi }}</td>
              <td>{{ $r->total_menit }}</td>
              <td>Rp {{ number_format($r->pendapatan,0,',','.') }}</td>
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

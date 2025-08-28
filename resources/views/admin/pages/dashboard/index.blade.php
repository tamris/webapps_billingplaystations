@extends('admin.layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-2 text-gray-800">Dashboard</h1>

  <div class="row">
    {{-- Jumlah User --}}
    <x-stat-card
      title="Jumlah User"
      :value="$jumlahUsers"
      icon="fas fa-users"
      color="success" />

    {{-- Total PlayStation --}}
    <x-stat-card
      title="Total PlayStation"
      :value="$playstations->count()"
      icon="fas fa-gamepad"
      color="primary" />

    {{-- PS Sedang Dipakai --}}
    <x-stat-card
      title="PS Sedang Dipakai"
      :value="$activeCount"
      icon="fas fa-toggle-on"
      color="warning" />

    {{-- Pendapatan Hari Ini --}}
    <x-stat-card
      title="Pendapatan Hari Ini"
      :value="'Rp '.number_format($todayIncome, 0, ',', '.')"
      icon="fas fa-money-bill-wave"
      color="info" />
  </div>
  <div class="row">
  @foreach($playstations as $ps)
    <div class="col-md-3">
      <div class="card p-3">
        <h5>{{ $ps->code }} ({{ $ps->name }})</h5>
        <p>Status: <b>{{ ucfirst($ps->status) }}</b></p>
        <p>Harga/Jam: Rp {{ number_format($ps->price_per_hour,0,',','.') }}</p>

        @if($ps->status == 'available')
          <form method="post" action="{{ route('ps.start',$ps) }}">
            @csrf
            <button class="btn btn-sm btn-primary w-100">Mulai</button>
          </form>
        @elseif($ps->status == 'in_use')
          <form method="post" action="{{ route('ps.stop',$ps) }}">
            @csrf
            <button class="btn btn-sm btn-danger w-100">Selesai</button>
          </form>
        @else
          <button class="btn btn-sm btn-secondary w-100" disabled>Maintenance</button>
        @endif
      </div>
    </div>
  @endforeach
</div>

</div>
@endsection

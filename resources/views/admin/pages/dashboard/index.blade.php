@extends('admin.layouts.dashboard')

@section('title', 'Dashboard')

@push('styles')
  {{-- (opsional) custom style kecil buat card --}}
@endpush

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
          <h5 class="mb-1">{{ $ps->code }} @if($ps->name) ({{ $ps->name }}) @endif</h5>
          <p class="mb-1">Status: <b>{{ ucfirst(str_replace('_',' ', $ps->status)) }}</b></p>
          <p class="mb-3">Harga/Jam: Rp {{ number_format($ps->price_per_hour,0,',','.') }}</p>

          @if($ps->status == 'available')
            <form method="post"
                  action="{{ route('ps.start',$ps) }}"
                  class="js-start-form"
                  data-ps-code="{{ $ps->code }}">
              @csrf
              <button type="submit" class="btn btn-sm btn-primary w-100">
                Mulai
              </button>
            </form>

          @elseif($ps->status == 'in_use')
            <form method="post"
                  action="{{ route('ps.stop',$ps) }}"
                  class="js-stop-form"
                  data-ps-code="{{ $ps->code }}">
              @csrf
              <button type="submit" class="btn btn-sm btn-danger w-100">
                Selesai
              </button>
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

@push('scripts')

  <script>
    // Konfirmasi saat klik "Mulai"
    document.querySelectorAll('.js-start-form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const psCode = form.getAttribute('data-ps-code') || 'PS';
        Swal.fire({
          title: 'Mulai sesi?',
          html: 'Kamu akan memulai sesi untuk <b>' + psCode + '</b>.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Ya, mulai',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#0d6efd',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });

    // (Opsional) Konfirmasi saat klik "Selesai"
    document.querySelectorAll('.js-stop-form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const psCode = form.getAttribute('data-ps-code') || 'PS';
        Swal.fire({
          title: 'Selesaikan sesi?',
          html: 'Sesi <b>' + psCode + '</b> akan dihitung & ditutup.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, selesai',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });

    // Toast sukses/error dari session flash
    @if(session('ok'))
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('ok')),
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true
      });
    @endif

    @if(session('err'))
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: @json(session('err')),
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
      });
    @endif
  </script>
@endpush

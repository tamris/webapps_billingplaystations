@extends('admin.layouts.playTime')
@section('title','Riwayat Sesi')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Riwayat Sesi</h3>

  <div class="table-responsive">
    <table id="sessionsTable" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">No</th>
          <th>PS</th>
          <th>Mulai</th>
          <th>Selesai</th>
          <th>Durasi (mnt)</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
      @foreach($sessions as $s)
        <tr>
          {{-- nomor baris pakai loop iteration (bukan firstItem) --}}
          <td>{{ $loop->iteration }}</td>

          <td>{{ $s->playstation->code }}</td>

          {{-- pakai data-order (timestamp detik) biar sorting tanggal akurat --}}
          <td
            @if($s->started_at)
              data-order="{{ \Carbon\Carbon::parse($s->started_at)->timezone('Asia/Jakarta')->timestamp }}"
            @endif
          >
            {{ $s->started_at ? \Carbon\Carbon::parse($s->started_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '-' }}
          </td>

          <td
            @if($s->ended_at)
              data-order="{{ \Carbon\Carbon::parse($s->ended_at)->timezone('Asia/Jakarta')->timestamp }}"
            @endif
          >
            {{ $s->ended_at ? \Carbon\Carbon::parse($s->ended_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '-' }}
          </td>

          {{-- data-order untuk angka agar sort benar --}}
          @php
            $m = $s->duration_minutes;
          @endphp
          <td data-order="{{ $m ?? 0 }}">
            @if(is_null($s->ended_at))
              <span class="text-warning">Sedang berjalan</span>
            @elseif(is_null($m))
              -
            @else
              @php
                $h = intdiv($m, 60);
                $min = $m % 60;
              @endphp
              @if($h > 0 && $min > 0)
                {{ $h }} jam {{ $min }} menit
              @elseif($h > 0)
                {{ $h }} jam
              @else
                {{ $min }} menit
              @endif
            @endif
          </td>

          <td data-order="{{ $s->total_price ?? 0 }}">
            {{ $s->total_price ? 'Rp '.number_format($s->total_price,0,',','.') : '-' }}
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{-- Jika sudah pakai DataTables (client-side), pagination Laravel tidak dipakai --}}
  {{-- {{ $sessions instanceof \Illuminate\Pagination\AbstractPaginator ? $sessions->links() : '' }} --}}
</div>
@endsection

@push('scripts')
<script>
$(function () {
  const dt = $('#sessionsTable').DataTable({
    pageLength: 10,
    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'Semua']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
    columnDefs: [
      { targets: 0, orderable: false, searchable: false }, // kolom No
    ],
    order: [[2, 'desc']] // urut berdasarkan "Mulai"
  });

  // Auto-number kolom No mengikuti filter/sort
  dt.on('order.dt search.dt draw.dt', function () {
    dt.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
      cell.innerHTML = i + 1;
    });
  }).draw();
});
</script>
@endpush

@extends('admin.layouts.playTime')
@section('title','Riwayat Sesi')

@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Riwayat Sesi</h3>

  {{-- Filter Mingguan --}}
  <div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-end">
      <div>
        <label class="form-label mb-1">Filter Minggu</label>
        <input type="week" id="weekPicker" class="form-control">
      </div>
      <div class="ms-auto d-flex gap-2">
        <button class="btn btn-outline-primary week-btn" id="btnThisWeek">Minggu Ini</button>
        <button class="btn btn-outline-primary week-btn" id="btnLastWeek">Minggu Lalu</button>
        <button class="btn btn-outline-primary week-btn" id="btnClearWeek">Semua</button>
      </div>
    </div>
  </div>

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
          {{-- nomor baris (akan dinamis oleh DataTables) --}}
          <td>{{ $loop->iteration }}</td>

          <td>{{ $s->playstation->code }}</td>

          {{-- data-order timestamp (WIB) untuk sort & filter akurat --}}
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

          @php $m = $s->duration_minutes; @endphp
          <td data-order="{{ $m ?? 0 }}">
            @if(is_null($s->ended_at))
              <span class="text-warning">Sedang berjalan</span>
            @elseif(is_null($m))
              -
            @else
              @php $h = intdiv($m, 60); $min = $m % 60; @endphp
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
</div>
@endsection

@push('scripts')
  {{-- jQuery + DataTables --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

  <script>
  $(function () {
    const table = $('#sessionsTable').DataTable({
      pageLength: 10,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'Semua']],
      language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
      columnDefs: [{ targets: 0, orderable: false, searchable: false }],
      order: [[2, 'desc']] // urut berdasarkan "Mulai"
    });

    // Auto-number kolom No mengikuti filter/sort
    table.on('order.dt search.dt draw.dt', function () {
      table.column(0, { search: 'applied', order: 'applied' })
        .nodes()
        .each(function (cell, i) { cell.innerHTML = i + 1; });
    }).draw();

    // ====== Filter Mingguan (WIB) ======
    let weekStart = null; // epoch detik start minggu WIB
    let weekEnd   = null; // epoch detik end minggu WIB

    // Helper: dapatkan start/end minggu dari value input type=week (YYYY-Www)
    function weekRangeFromInput(weekValue) {
      if (!weekValue || !/^(\d{4})-W(\d{2})$/.test(weekValue)) return null;
      const [, yearStr, weekStr] = weekValue.match(/^(\d{4})-W(\d{2})$/);
      const year = parseInt(yearStr, 10);
      const week = parseInt(weekStr, 10);

      // ISO week: Senin sebagai awal minggu
      const jan4 = new Date(Date.UTC(year, 0, 4));
      const jan4Day = jan4.getUTCDay() || 7; // 1..7 (Mon..Sun)
      const mondayOfWeek1 = new Date(jan4);
      mondayOfWeek1.setUTCDate(jan4.getUTCDate() - (jan4Day - 1));

      const monday = new Date(mondayOfWeek1);
      monday.setUTCDate(mondayOfWeek1.getUTCDate() + (week - 1) * 7);

      // WIB offset +7 jam → buat rentang Senin 00:00:00 WIB s/d Minggu 23:59:59 WIB
      const startUtcMs = monday.getTime() - (7 * 3600 * 1000);
      const endUtcMs   = startUtcMs + (7 * 24 * 3600 * 1000) - 1000;

      return {
        start: Math.floor(startUtcMs / 1000),
        end: Math.floor(endUtcMs / 1000),
      };
    }

    // DataTables custom filter callback
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      if (settings.nTable.id !== 'sessionsTable') return true;     // hanya tabel ini
      if (weekStart === null || weekEnd === null) return true;     // tanpa filter

      // Ambil timestamp Mulai dari attribute data-order kolom index 2
      const rowNode = table.row(dataIndex).node();
      const startedTsStr = $('td:eq(2)', rowNode).attr('data-order');
      if (!startedTsStr) return false;

      const startedTs = parseInt(startedTsStr, 10);
      return startedTs >= weekStart && startedTs <= weekEnd;
    });

    function applyFilter() { table.draw(); }

    // Toggle warna tombol filter (active jadi biru)
    function setActiveButton(activeId) {
      $('.week-btn').removeClass('btn-primary').addClass('btn-outline-primary');
      if (activeId) {
        $('#' + activeId).removeClass('btn-outline-primary').addClass('btn-primary');
      }
    }

    // Dapatkan ISO week untuk "Minggu Ini"
    function getIsoYearWeek(dateUtc) {
      const d = new Date(Date.UTC(dateUtc.getUTCFullYear(), dateUtc.getUTCMonth(), dateUtc.getUTCDate()));
      d.setUTCDate(d.getUTCDate() + 4 - ((d.getUTCDay() || 7)));
      const isoYear = d.getUTCFullYear();
      const yearStart = new Date(Date.UTC(isoYear, 0, 1));
      const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
      return { isoYear, weekNo };
    }

    // Button: Minggu Ini
    $('#btnThisWeek').on('click', function (e) {
      e.preventDefault();
      const now = new Date();
      const utc = new Date(Date.UTC(now.getFullYear(), now.getMonth(), now.getDate()));
      const { isoYear, weekNo } = getIsoYearWeek(utc);
      const weekVal = isoYear + '-W' + String(weekNo).padStart(2,'0');
      $('#weekPicker').val(weekVal);

      const range = weekRangeFromInput(weekVal);
      weekStart = range.start; weekEnd = range.end;
      setActiveButton('btnThisWeek');
      applyFilter();
    });

    // Button: Minggu Lalu
    $('#btnLastWeek').on('click', function (e) {
      e.preventDefault();
      let val = $('#weekPicker').val();
      if (!val) {
        $('#btnThisWeek').trigger('click');
        val = $('#weekPicker').val();
      }
      const m = val.match(/^(\d{4})-W(\d{2})$/);
      let year = parseInt(m[1],10);
      let week = parseInt(m[2],10) - 1;
      if (week <= 0) { year -= 1; week = 52; } // cukup untuk UI
      const weekVal = year + '-W' + String(week).padStart(2,'0');
      $('#weekPicker').val(weekVal);

      const range = weekRangeFromInput(weekVal);
      weekStart = range.start; weekEnd = range.end;
      setActiveButton('btnLastWeek');
      applyFilter();
    });

    // Button: Semua
    $('#btnClearWeek').on('click', function (e) {
      e.preventDefault();
      $('#weekPicker').val('');
      weekStart = null; weekEnd = null;
      setActiveButton('btnClearWeek');
      applyFilter();
    });

    // Picker manual
    $('#weekPicker').on('change', function () {
      const val = $(this).val();
      if (!val) {
        weekStart = null; weekEnd = null;
        setActiveButton(null); // semua outline
      } else {
        const range = weekRangeFromInput(val);
        if (range) { weekStart = range.start; weekEnd = range.end; }
        setActiveButton(null); // pilih manual → tidak highlight tombol preset
      }
      applyFilter();
    });
  });
  </script>
@endpush

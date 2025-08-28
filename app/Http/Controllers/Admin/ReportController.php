<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlaySession;
use App\Models\Playstation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function revenue(Request $req)
    {
        // default: hari ini
        $from = $req->query('from', Carbon::now('Asia/Jakarta')->toDateString());
        $to   = $req->query('to',   Carbon::now('Asia/Jakarta')->toDateString());

        // normalisasi range (00:00 - 23:59:59 WIB)
        $fromDt = Carbon::parse($from.' 00:00:00', 'Asia/Jakarta')->utc();
        $toDt   = Carbon::parse($to.' 23:59:59', 'Asia/Jakarta')->utc();

        // total keseluruhan
        $total = PlaySession::where('status','closed')
            ->whereBetween('ended_at', [$fromDt, $toDt])
            ->sum('total_price');

        // rekap per hari (tanggal WIB)
        $perHari = PlaySession::select([
                DB::raw("DATE(CONVERT_TZ(ended_at,'+00:00','+07:00')) as tgl"),
                DB::raw("COUNT(*) as sesi"),
                DB::raw("SUM(total_price) as pendapatan"),
                DB::raw("SUM(duration_minutes) as total_menit"),
            ])
            ->where('status','closed')
            ->whereBetween('ended_at', [$fromDt, $toDt])
            ->groupBy('tgl')
            ->orderBy('tgl','asc')
            ->get();

        // rekap per PS
        $perPs = PlaySession::select([
                'playstation_id',
                DB::raw('COUNT(*) as sesi'),
                DB::raw('SUM(total_price) as pendapatan'),
                DB::raw('SUM(duration_minutes) as total_menit'),
            ])
            ->where('status','closed')
            ->whereBetween('ended_at', [$fromDt, $toDt])
            ->groupBy('playstation_id')
            ->with('playstation:id,code,name')
            ->get();

        return view('admin.pages.reports.revenue', compact('from','to','total','perHari','perPs'));
    }

    public function exportRevenue(Request $req): StreamedResponse
    {
        $from = $req->query('from');
        $to   = $req->query('to');

        $fromDt = Carbon::parse($from.' 00:00:00', 'Asia/Jakarta')->utc();
        $toDt   = Carbon::parse($to.' 23:59:59', 'Asia/Jakarta')->utc();

        $rows = PlaySession::with('playstation:id,code')
            ->where('status','closed')
            ->whereBetween('ended_at', [$fromDt, $toDt])
            ->orderBy('ended_at','asc')
            ->get(['playstation_id','started_at','ended_at','duration_minutes','total_price']);

        $filename = "rekap_pendapatan_{$from}_sampai_{$to}.csv";

        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal (WIB)','PS','Mulai (WIB)','Selesai (WIB)','Durasi (menit)','Total (Rp)']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    Carbon::parse($r->ended_at)->timezone('Asia/Jakarta')->format('Y-m-d'),
                    optional($r->playstation)->code,
                    Carbon::parse($r->started_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    Carbon::parse($r->ended_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    $r->duration_minutes,
                    (int)$r->total_price,
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

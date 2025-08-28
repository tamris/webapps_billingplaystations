<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playstation;
use App\Models\PlaySession;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = PlaySession::with('playstation')
            ->latest()
            ->get(); // ambil semua data tanpa paginate
        return view('admin.pages.sessions.index', compact('sessions'));
    }
    public function start(Playstation $ps)
    {
        if ($ps->status !== 'available') {
            return back()->with('err', "PS {$ps->code} sedang dipakai.");
        }

        PlaySession::create([
            'playstation_id' => $ps->id,
            'started_at'     => now(),
            'status'         => 'open',
        ]);

        $ps->update(['status' => 'in_use']);

        return back()->with('ok', "Sesi {$ps->code} dimulai.");
    }

    public function stop(Playstation $ps)
    {
        $session = $ps->sessions()->where('status', 'open')->latest()->first();
        if (!$session) return back()->with('err', "Tidak ada sesi aktif untuk {$ps->code}.");

        // Pakai timestamp biar bebas TZ & parsing
        $start = \Carbon\Carbon::parse($session->started_at);
        $end   = now();

        $diffSeconds = $end->timestamp - $start->timestamp;      // bisa negatif kalau ada mismatch
        $minutes     = (int) floor(max(0, $diffSeconds) / 60);   // clamp ke >= 0

        // Penagihan per 30 menit (minimal 1 blok)
        $gran = 30; // menit per blok
        $units = max(1, (int) ceil($minutes / $gran));
        $pricePerUnit = (float) $ps->price_per_hour * ($gran / 60.0);
        $total = $units * $pricePerUnit;

        $session->update([
            'ended_at'         => $end,
            'duration_minutes' => $minutes,
            'total_price'      => $total,
            'status'           => 'closed',
        ]);

        $ps->update(['status' => 'available']);

        return back()->with('ok', "Sesi {$ps->code} selesai. Biaya Rp " . number_format($total, 0, ',', '.'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PlaySession;
use App\Models\Playstation;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahUsers = User::count();
        $playstations = Playstation::orderBy('code')->get();

        $todayIncome = PlaySession::where('status','closed')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_price');

        $activeCount = Playstation::where('status','in_use')->count();

        return view('admin.pages.dashboard.index', compact('playstations','todayIncome','activeCount', 'jumlahUsers'));
    }
}

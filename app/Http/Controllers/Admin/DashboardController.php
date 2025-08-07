<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $jumlahBarang = Barang::count();
        $jumlahUsers = User::count();

        return view('admin.pages.dashboard.index', compact('jumlahBarang', 'jumlahUsers'));
    }
}

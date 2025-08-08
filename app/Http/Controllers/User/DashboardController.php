<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan dashboard user
        // Misalnya, mengambil data yang relevan untuk user
        return view('index'); // Pastikan ada view yang sesuai
    }
}

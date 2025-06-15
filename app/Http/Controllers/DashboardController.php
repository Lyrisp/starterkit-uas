<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil statistik untuk dashboard
        $total_berita = Berita::count();
        $pending_berita = Berita::where('status', 'pending')->count();
        $approved_berita = Berita::where('status', 'approved')->count();
        $total_user = User::count();

        return view('dashboard', compact(
            'total_berita', 
            'pending_berita', 
            'approved_berita', 
            'total_user'
        ));
    }
}
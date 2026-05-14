<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $totalKas = Warga::sum('saldo');
        $totalWarga = Warga::count();
        $pemasukanHariIni = Transaksi::whereDate('created_at', $today)
                                    ->where('jenis', 'jimpitan')
                                    ->sum('nominal');
        
        $wargaLunasCount = Transaksi::whereDate('created_at', $today)
                                    ->where('jenis', 'jimpitan')
                                    ->distinct('warga_id')
                                    ->count();
                                    
        $recentTransactions = Transaksi::with(['warga', 'user'])
                                        ->latest()
                                        ->take(8)
                                        ->get();

        $topBalanceWarga = Warga::orderBy('saldo', 'desc')->take(5)->get();
        $totalWargaBelumBayar = $totalWarga - $wargaLunasCount;

        // Chart Data (7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData['labels'][] = $date->format('d M');
            $chartData['values'][] = Transaksi::whereDate('created_at', $date)
                                             ->where('jenis', 'jimpitan')
                                             ->sum('nominal');
        }

        return view('dashboard.index', compact(
            'totalKas', 'totalWarga', 'pemasukanHariIni', 
            'wargaLunasCount', 'recentTransactions', 'chartData',
            'topBalanceWarga', 'totalWargaBelumBayar'
        ));
    }
}

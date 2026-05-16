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
        
        $wargaLunasIds = Transaksi::whereDate('created_at', $today)
                                    ->where('jenis', 'jimpitan')
                                    ->pluck('warga_id')
                                    ->toArray();

        $wargaLunas = Warga::whereIn('id', $wargaLunasIds)->get();
        $wargaBelumBayar = Warga::whereNotIn('id', $wargaLunasIds)->get();
        $wargaLunasCount = count($wargaLunasIds);
                                    
        $recentActivities = Transaksi::with(['warga', 'user'])
                                        ->latest()
                                        ->take(8)
                                        ->get();

        $topBalanceWarga = Warga::orderBy('saldo', 'desc')->take(5)->get();
        $totalWargaBelumBayar = $totalWarga - $wargaLunasCount;

        // Chart Data (7 days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Transaksi::whereDate('created_at', $date)
                                     ->where('jenis', 'jimpitan')
                                     ->sum('nominal');
        }

        $totalJimpitan = Transaksi::where('jenis', 'jimpitan')->sum('nominal');
        $totalTopup = Transaksi::where('jenis', 'topup')->sum('nominal');
        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')->sum('nominal');
        $totalDonasi = Transaksi::where('jenis', 'donasi')->sum('nominal');
        
        // Total Kas RT = Saldo semua warga + (Total Jimpitan + Total Donasi - Total Pengeluaran)
        $totalKas = Warga::sum('saldo') + ($totalJimpitan + $totalDonasi - $totalPengeluaran);

        $wargas = Warga::all();

        // Stats per RT
        $statsPerRT = Warga::select('rt', 'rw')
            ->selectRaw('SUM(saldo) as total_saldo')
            ->selectRaw('COUNT(*) as total_warga')
            ->whereNotNull('rt')
            ->groupBy('rt', 'rw')
            ->orderBy('rt')
            ->get();

        return view('dashboard.index', compact(
            'totalKas', 'totalWarga', 'pemasukanHariIni', 
            'wargaLunasCount', 'recentActivities', 'chartLabels', 'chartData',
            'topBalanceWarga', 'totalWargaBelumBayar',
            'totalJimpitan', 'totalTopup', 'totalPengeluaran', 'totalDonasi', 'wargaLunas', 'wargaBelumBayar', 'wargas',
            'statsPerRT'
        ));
    }

    public function closeDay()
    {
        $today = date('Y-m-d');
        
        // Cari warga yang BELUM bayar hari ini
        $paidWargaIds = Transaksi::whereDate('created_at', $today)
                                ->where('jenis', 'jimpitan')
                                ->pluck('warga_id')
                                ->toArray();

        $unpaidWarga = Warga::whereNotIn('id', $paidWargaIds)->get();

        foreach ($unpaidWarga as $warga) {
            $warga->increment('tunggakan', 500);
        }

        return back()->with('success', count($unpaidWarga) . ' warga telah dicatat menunggak Rp 500 untuk hari ini.');
    }
}

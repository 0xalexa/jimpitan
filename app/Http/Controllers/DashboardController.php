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

        // Stats per RT (Hanya menghitung jimpitan yang terkumpul)
        $statsPerRT = Warga::select('wargas.rt', 'wargas.rw')
            ->selectRaw('COUNT(DISTINCT wargas.id) as total_warga')
            ->selectRaw('COALESCE(SUM(CASE WHEN transaksis.jenis = "jimpitan" THEN transaksis.nominal ELSE 0 END), 0) as total_saldo')
            ->leftJoin('transaksis', 'wargas.id', '=', 'transaksis.warga_id')
            ->whereNotNull('wargas.rt')
            ->groupBy('wargas.rt', 'wargas.rw')
            ->orderBy('wargas.rt')
            ->get();

        return view('dashboard.index', compact(
            'totalKas', 'totalWarga', 'pemasukanHariIni', 
            'wargaLunasCount', 'recentActivities', 'chartLabels', 'chartData',
            'topBalanceWarga', 'totalWargaBelumBayar',
            'totalJimpitan', 'totalTopup', 'totalPengeluaran', 'totalDonasi', 'wargaLunas', 'wargaBelumBayar', 'wargas',
            'statsPerRT'
        ));
    }
}

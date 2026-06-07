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
        $user = auth()->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'sekretaris') {
            return $this->sekretarisDashboard();
        } elseif ($user->role === 'petugas') {
            return $this->petugasDashboard();
        }

        abort(403);
    }

    private function adminDashboard()
    {
        $totalWarga = Warga::count();
        $totalJimpitan = Transaksi::where('jenis', 'jimpitan')->sum('nominal');
        $totalTopup = Transaksi::where('jenis', 'topup')->sum('nominal');
        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')->sum('nominal');
        $totalDonasi = Transaksi::where('jenis', 'donasi')->sum('nominal');
        
        // Total Kas RT = Saldo semua warga + (Total Jimpitan + Total Donasi - Total Pengeluaran)
        $totalKas = Warga::sum('saldo') + ($totalJimpitan + $totalDonasi - $totalPengeluaran);
        $totalTunggakan = Warga::sum('tunggakan');

        // Stats per RT
        $statsPerRT = Warga::select('wargas.rt', 'wargas.rw')
            ->selectRaw('COUNT(DISTINCT wargas.id) as total_warga')
            ->selectRaw('COALESCE(SUM(CASE WHEN transaksis.jenis = "jimpitan" THEN transaksis.nominal ELSE 0 END), 0) as total_saldo')
            ->leftJoin('transaksis', 'wargas.id', '=', 'transaksis.warga_id')
            ->whereNotNull('wargas.rt')
            ->groupBy('wargas.rt', 'wargas.rw')
            ->orderBy('wargas.rt')
            ->get();

        return view('dashboard.admin', compact(
            'totalKas', 'totalWarga', 'totalJimpitan', 'totalTopup', 'totalPengeluaran', 'totalDonasi', 'statsPerRT', 'totalTunggakan'
        ));
    }

    private function sekretarisDashboard()
    {
        $user = auth()->user();
        
        $totalWarga = Warga::where('rt', $user->rt)->count();
        
        $getSum = function($jenis) use ($user) {
            return Transaksi::where('jenis', $jenis)
                ->where(function($q) use ($user) {
                    $q->whereHas('warga', function($w) use ($user) {
                        $w->where('rt', $user->rt);
                    })->orWhere(function($w) use ($user) {
                        $w->whereNull('warga_id')->where('user_id', $user->id);
                    });
                })->sum('nominal');
        };

        $totalKas = $getSum('jimpitan') + $getSum('donasi') - $getSum('pengeluaran');
        
        $recentActivities = Transaksi::with(['warga', 'user'])
                                        ->whereHas('warga', function($q) use ($user) {
                                            $q->where('rt', $user->rt);
                                        })
                                        ->orWhere(function($q) use ($user) {
                                            $q->whereNull('warga_id')->where('user_id', $user->id);
                                        })
                                        ->latest()
                                        ->take(8)
                                        ->get();

        $wargaMenunggak = Warga::where('rt', $user->rt)->where('tunggakan', '>', 0)->get();

        return view('dashboard.sekretaris', compact('totalWarga', 'totalKas', 'recentActivities', 'wargaMenunggak'));
    }

    private function petugasDashboard()
    {
        $user = auth()->user();
        $wargaBelumScan = Warga::where('rt', $user->rt)->where('status_harian', 0)->get();
        $wargaSudahScan = Warga::where('rt', $user->rt)->where('status_harian', 1)->get();
        $wargaTunggakan = Warga::where('rt', $user->rt)->where('tunggakan', '>', 0)->get();

        return view('dashboard.petugas', compact('wargaBelumScan', 'wargaSudahScan', 'wargaTunggakan'));
    }
}

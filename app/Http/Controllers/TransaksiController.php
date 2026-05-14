<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['warga', 'user'])->latest()->paginate(15);
        return view('transaksi.index', compact('transaksis'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'nominal' => 'required|numeric|min:1',
        ]);

        $warga = Warga::findOrFail($request->warga_id);
        $warga->increment('saldo', $request->nominal);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'jenis' => 'topup',
            'keterangan' => 'Top Up Saldo',
        ]);

        return back()->with('success', 'Top up berhasil.');
    }

    public function manualPayment(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
        ]);

        $warga = Warga::findOrFail($request->warga_id);
        
        if ($warga->saldo < 500) {
            return back()->with('error', 'Saldo warga tidak cukup.');
        }

        $warga->decrement('saldo', 500);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => 500,
            'jenis' => 'jimpitan',
            'keterangan' => 'Pembayaran Manual',
        ]);

        return back()->with('success', 'Pembayaran berhasil.');
    }
}

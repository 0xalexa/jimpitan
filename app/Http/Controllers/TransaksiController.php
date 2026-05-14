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
            'metode_pembayaran' => 'required|string',
        ]);

        $warga = Warga::findOrFail($request->warga_id);
        $warga->increment('saldo', $request->nominal);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'metode_pembayaran' => $request->metode_pembayaran,
            'jenis' => 'topup',
            'keterangan' => 'Top Up Saldo via ' . $request->metode_pembayaran,
        ]);

        return back()->with('success', 'Top up berhasil.');
    }

    public function manualPayment(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
        ]);

        $warga = Warga::findOrFail($request->warga_id);
        
        $today = date('Y-m-d');
        $exists = Transaksi::where('warga_id', $warga->id)
                            ->whereDate('created_at', $today)
                            ->where('jenis', 'jimpitan')
                            ->exists();

        if ($exists) {
            return back()->with('error', 'Warga ini sudah membayar hari ini.');
        }

        $tagihan = 500 + $warga->tunggakan;

        if ($warga->saldo < $tagihan) {
            return back()->with('error', "Saldo tidak cukup. Tagihan: Rp " . number_format($tagihan, 0, ',', '.'));
        }

        $warga->decrement('saldo', $tagihan);
        $warga->update(['tunggakan' => 0]);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => $tagihan,
            'jenis' => 'jimpitan',
            'metode_pembayaran' => 'Manual (Potong Saldo)',
            'keterangan' => 'Bayar Jimpitan Manual' . ($tagihan > 500 ? ' (Termasuk tunggakan)' : ''),
        ]);

        return back()->with('success', 'Pembayaran berhasil.');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $warga = Warga::findOrFail($transaksi->warga_id);

        if ($transaksi->jenis == 'jimpitan') {
            // Refund saldo
            $warga->increment('saldo', $transaksi->nominal);
            // Jika transaksi hari ini dibatalkan, mungkin perlu tambah tunggakan? 
            // Tapi biasanya 'batal' berarti salah input, jadi biarkan saja statusnya belum bayar.
        } elseif ($transaksi->jenis == 'topup') {
            if ($warga->saldo < $transaksi->nominal) {
                return back()->with('error', 'Gagal membatalkan. Saldo warga sudah digunakan dan tidak cukup untuk dipotong kembali.');
            }
            $warga->decrement('saldo', $transaksi->nominal);
        }

        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dibatalkan dan saldo disesuaikan.');
    }

    public function storeExpenditure(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        Transaksi::create([
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'jenis' => 'pengeluaran',
            'metode_pembayaran' => 'Kas RT',
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['warga', 'user']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('warga', function($qw) use ($search) {
                      $qw->where('nama', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $transaksis = $query->latest()->paginate(15)->appends($request->all());

        return view('transaksi.index', compact('transaksis'));
    }

    public function export()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan_jimpitan_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $transaksis = Transaksi::with(['warga', 'user'])->latest()->get();

        $callback = function() use($transaksis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Waktu', 'Nama Warga / Keterangan', 'Jenis', 'Nominal', 'Metode', 'Petugas']);

            foreach ($transaksis as $tx) {
                fputcsv($file, [
                    $tx->created_at->format('d-m-Y'),
                    $tx->created_at->format('H:i'),
                    $tx->warga ? $tx->warga->nama : $tx->keterangan,
                    strtoupper($tx->jenis),
                    $tx->nominal,
                    $tx->metode_pembayaran,
                    $tx->user->name
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        // Hanya proses penyesuaian saldo jika transaksi terkait dengan warga tertentu
        if ($transaksi->warga_id) {
            $warga = Warga::findOrFail($transaksi->warga_id);

            if ($transaksi->jenis == 'jimpitan') {
                // Refund saldo warga jika jimpitan dibatalkan
                $warga->increment('saldo', $transaksi->nominal);
            } elseif ($transaksi->jenis == 'topup') {
                // Potong kembali saldo jika topup dibatalkan
                if ($warga->saldo < $transaksi->nominal) {
                    return back()->with('error', 'Gagal membatalkan. Saldo warga sudah digunakan dan tidak cukup untuk dipotong kembali.');
                }
                $warga->decrement('saldo', $transaksi->nominal);
            }
        }

        // Untuk Pengeluaran dan Donasi, penghapusan transaksi otomatis menyesuaikan Total Kas di Dashboard
        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dibatalkan.');
    }

    public function storeExpenditure(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        Transaksi::create([
            'warga_id' => null,
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'jenis' => 'pengeluaran',
            'metode_pembayaran' => 'Kas RT',
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function storeDonation(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
            'nama_donatur' => 'nullable|string',
        ]);

        Transaksi::create([
            'warga_id' => null,
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'jenis' => 'donasi',
            'metode_pembayaran' => 'Tunai/Transfer',
            'keterangan' => 'Donasi: ' . $request->keterangan . ($request->nama_donatur ? ' (Dari: ' . $request->nama_donatur . ')' : ''),
        ]);

        return back()->with('success', 'Donasi berhasil dicatat.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScanController extends Controller
{
    public function index()
    {
        $wargas = Warga::orderBy('nama')->get();
        return view('scan.index', compact('wargas'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'qr_code' => 'required',
        ]);

        $warga = Warga::where('qr_code_string', $request->qr_code)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga tidak ditemukan.'
            ], 404);
        }

        // Check if already paid today
        $alreadyPaid = Transaksi::where('warga_id', $warga->id)
                                ->whereDate('created_at', Carbon::today())
                                ->where('jenis', 'jimpitan')
                                ->exists();

        if ($alreadyPaid) {
            return response()->json([
                'success' => false,
                'message' => 'Warga ' . $warga->nama . ' sudah membayar hari ini.'
            ]);
        }

        // Cek tagihan (500 harian + tunggakan)
        $tagihan = 500 + $warga->tunggakan;

        if ($warga->saldo < $tagihan) {
            return response()->json([
                'success' => false,
                'message' => "Saldo tidak cukup. Tagihan hari ini: Rp " . number_format($tagihan, 0, ',', '.')
            ]);
        }

        // Proses pembayaran
        $warga->decrement('saldo', $tagihan);
        $warga->update([
            'tunggakan' => 0,
            'status_harian' => 1
        ]);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => $tagihan,
            'jenis' => 'jimpitan',
            'metode_pembayaran' => 'Saldo Digital',
            'keterangan' => 'Bayar Jimpitan' . ($tagihan > 500 ? ' (Termasuk tunggakan)' : '')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran ' . $warga->nama . ' berhasil.',
            'data' => [
                'nama' => $warga->nama,
                'saldo_sisa' => $warga->saldo
            ]
        ]);
    }
}

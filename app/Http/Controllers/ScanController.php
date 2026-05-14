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
        return view('scan.index');
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

        if ($warga->saldo < 500) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo warga ' . $warga->nama . ' tidak cukup (Sisa: Rp' . number_format($warga->saldo, 0, ',', '.') . ').'
            ]);
        }

        // Process deduction
        $warga->decrement('saldo', 500);

        Transaksi::create([
            'warga_id' => $warga->id,
            'user_id' => Auth::id(),
            'nominal' => 500,
            'jenis' => 'jimpitan',
            'keterangan' => 'Pembayaran via QR Scan',
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan RT</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h4 { margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { width: 40%; float: right; }
        .summary th, .summary td { border: none; padding: 4px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN JIMPITAN RT</h2>
        <h4>Dicetak pada: {{ date('d M Y H:i') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Warga / Keterangan</th>
                <th style="width: 15%">Jenis</th>
                <th style="width: 20%">Metode</th>
                <th class="text-right" style="width: 20%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $tx->warga ? $tx->warga->nama : $tx->keterangan }}</td>
                <td>{{ strtoupper($tx->jenis) }}</td>
                <td>{{ $tx->metode_pembayaran }}</td>
                <td class="text-right">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix">
        <table class="summary">
            <tr>
                <th>Total Pemasukan</th>
                <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="border-top: 1px solid #000;">Saldo Kas / Sisa</th>
                <th class="text-right" style="border-top: 1px solid #000;">Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</th>
            </tr>
        </table>
    </div>
</body>
</html>

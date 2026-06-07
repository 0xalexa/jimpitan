@extends('layouts.app')
@section('title', 'Dashboard Sekretaris RT ' . auth()->user()->rt)
@section('page_title', 'Buku Kas RT ' . auth()->user()->rt)

@section('content')
<div class="container-fluid py-4">
    {{-- Kartu Ringkasan Keuangan --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-users fa-2x mb-2" style="color: #6366f1;"></i>
                <p class="text-muted mb-1">Total Warga RT {{ auth()->user()->rt }}</p>
                <h2 class="fw-bold mb-0">{{ $totalWarga }} KK</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-wallet fa-2x mb-2" style="color: #10b981;"></i>
                <p class="text-muted mb-1">Total Saldo Kas RT</p>
                <h2 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($totalKas, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2" style="color: #f59e0b;"></i>
                <p class="text-muted mb-1">Warga Menunggak</p>
                <h2 class="fw-bold mb-0" style="color: #ef4444;">{{ $wargaMenunggak->count() }} Orang</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: Form Input Cepat --}}
        <div class="col-lg-5">
            {{-- Form Top-Up Saldo Warga --}}
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-money-bill-wave me-2" style="color: #6366f1;"></i>Top-Up Saldo Warga</h5>
                <form action="{{ route('transaksi.topup') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Warga</label>
                        <select name="warga_id" class="form-select" required>
                            <option value="">-- Pilih Warga --</option>
                            @foreach(\App\Models\Warga::where('rt', auth()->user()->rt)->orderBy('nama')->get() as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }} (Saldo: Rp {{ number_format($w->saldo, 0, ',', '.') }}{{ $w->tunggakan > 0 ? ' | Tunggakan: Rp '.number_format($w->tunggakan, 0, ',', '.') : '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" min="1" required placeholder="Masukkan jumlah top-up">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select" required>
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus-circle me-2"></i>Proses Top-Up</button>
                </form>
            </div>

            {{-- Form Pengeluaran Kas --}}
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-arrow-right-from-bracket me-2" style="color: #ef4444;"></i>Catat Pengeluaran Kas</h5>
                <form action="{{ route('transaksi.pengeluaran') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required placeholder="Contoh: Perbaikan lampu jalan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" min="1" required placeholder="Masukkan jumlah">
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-minus-circle me-2"></i>Catat Pengeluaran</button>
                </form>
            </div>

            {{-- Form Donasi --}}
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-hand-holding-heart me-2" style="color: #10b981;"></i>Catat Donasi / Pemasukan Lain</h5>
                <form action="{{ route('transaksi.donasi') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Donatur (Opsional)</label>
                        <input type="text" name="nama_donatur" class="form-control" placeholder="Nama penyumbang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required placeholder="Contoh: Sumbangan HUT RI">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" min="1" required placeholder="Masukkan jumlah">
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus-circle me-2"></i>Catat Donasi</button>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: Aktivitas Terbaru & Tunggakan --}}
        <div class="col-lg-7">
            {{-- Aktivitas Terbaru --}}
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-history me-2" style="color: #6366f1;"></i>Aktivitas Terakhir</h5>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table glass-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $tx)
                            <tr>
                                <td><small>{{ $tx->created_at->diffForHumans() }}</small></td>
                                <td>{{ $tx->warga ? $tx->warga->nama : $tx->keterangan }}</td>
                                <td>
                                    @if(in_array($tx->jenis, ['jimpitan', 'donasi', 'topup']))
                                        <span class="badge bg-success">{{ ucfirst($tx->jenis) }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($tx->jenis) }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold {{ $tx->jenis === 'pengeluaran' ? 'text-danger' : 'text-success' }}">
                                    {{ $tx->jenis === 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daftar Tunggakan --}}
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Warga Menunggak di RT {{ auth()->user()->rt }}</h5>
                @forelse($wargaMenunggak as $warga)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>{{ $warga->nama }}</strong>
                        <small class="d-block text-muted">NIK: {{ $warga->nik }}</small>
                    </div>
                    <span class="badge bg-danger rounded-pill p-2">Rp {{ number_format($warga->tunggakan, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x mb-2" style="color: #10b981;"></i>
                    <p class="text-muted mb-0">Semua warga RT {{ auth()->user()->rt }} tertib bayar!</p>
                </div>
                @endforelse
            </div>

            {{-- Pintasan Laporan --}}
            <div class="glass-card p-4 mt-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-file-export me-2" style="color: #8b5cf6;"></i>Ekspor Laporan Keuangan</h5>
                <div class="d-flex gap-3">
                    <a href="{{ route('transaksi.export') }}" class="btn btn-outline-success flex-fill">
                        <i class="fas fa-file-csv me-2"></i>Unduh Excel (CSV)
                    </a>
                    <a href="{{ route('transaksi.export.pdf') }}" class="btn btn-outline-danger flex-fill">
                        <i class="fas fa-file-pdf me-2"></i>Unduh PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

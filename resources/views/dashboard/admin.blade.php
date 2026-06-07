@extends('layouts.app')
@section('title', 'Dashboard Admin Pusat')
@section('page_title', 'Statistik Global Desa')

@section('content')
<div class="container-fluid py-4">
    {{-- Kartu Statistik Global --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-wallet fa-2x mb-2" style="color: #6366f1;"></i>
                <p class="text-muted mb-1">Total Kas Desa</p>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalKas, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-coins fa-2x mb-2" style="color: #10b981;"></i>
                <p class="text-muted mb-1">Total Jimpitan</p>
                <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($totalJimpitan, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-hand-holding-dollar fa-2x mb-2" style="color: #ef4444;"></i>
                <p class="text-muted mb-1">Total Pengeluaran</p>
                <h3 class="fw-bold mb-0" style="color: #ef4444;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-file-invoice-dollar fa-2x mb-2" style="color: #f59e0b;"></i>
                <p class="text-muted mb-1">Total Tunggakan</p>
                <h3 class="fw-bold mb-0" style="color: #f59e0b;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    {{-- Baris Kedua: Donasi & KK --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-hand-holding-heart fa-2x mb-2" style="color: #8b5cf6;"></i>
                <p class="text-muted mb-1">Total Donasi</p>
                <h3 class="fw-bold mb-0" style="color: #8b5cf6;">Rp {{ number_format($totalDonasi, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-arrow-up fa-2x mb-2" style="color: #0ea5e9;"></i>
                <p class="text-muted mb-1">Total Top-Up</p>
                <h3 class="fw-bold mb-0" style="color: #0ea5e9;">Rp {{ number_format($totalTopup, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-people-roof fa-2x mb-2" style="color: #6366f1;"></i>
                <p class="text-muted mb-1">Total Kepala Keluarga</p>
                <h3 class="fw-bold mb-0">{{ $totalWarga }} KK</h3>
            </div>
        </div>
    </div>

    {{-- Statistik Per RT --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2" style="color: #6366f1;"></i>Rekapitulasi Per RT</h5>
                <div class="table-responsive">
                    <table class="table glass-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>RT / RW</th>
                                <th class="text-center">Jumlah KK</th>
                                <th class="text-end">Saldo Terkumpul</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsPerRT as $stat)
                            <tr>
                                <td class="fw-semibold">RT {{ str_pad($stat->rt, 3, '0', STR_PAD_LEFT) }} / RW {{ str_pad($stat->rw, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center">{{ $stat->total_warga }} KK</td>
                                <td class="text-end fw-bold" style="color: #10b981;">Rp {{ number_format($stat->total_saldo, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Data wilayah belum tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pintasan Admin --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-users-viewfinder fa-2x mb-3" style="color: #6366f1;"></i>
                <h6 class="fw-bold">Kelola Data Warga</h6>
                <p class="text-muted small">Tambah, edit, atau hapus data warga seluruh RT</p>
                <a href="{{ route('warga.index') }}" class="btn btn-outline-primary w-100">Buka Data Warga</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-receipt fa-2x mb-3" style="color: #10b981;"></i>
                <h6 class="fw-bold">Riwayat Transaksi</h6>
                <p class="text-muted small">Lihat seluruh mutasi kas dari semua RT</p>
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-success w-100">Buka Riwayat Kas</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                <i class="fas fa-file-pdf fa-2x mb-3" style="color: #ef4444;"></i>
                <h6 class="fw-bold">Ekspor Laporan</h6>
                <p class="text-muted small">Unduh laporan lengkap dalam format PDF atau CSV</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('transaksi.export') }}" class="btn btn-outline-success flex-fill btn-sm">CSV</a>
                    <a href="{{ route('transaksi.export.pdf') }}" class="btn btn-outline-danger flex-fill btn-sm">PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

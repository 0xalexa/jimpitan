@extends('layouts.app')

@section('title', 'Riwayat Kas')
@section('page_title', 'Log Aktivitas Keuangan')

@section('content')
<div class="fade-in">
    <!-- Filters & Stats Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.25rem; background: var(--card-bg); border: 1px solid var(--card-border); border-left: 4px solid var(--primary) !important;">
            <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Total Transaksi</p>
            <h4 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $transaksis->total() }}</h4>
        </div>
        <div class="card" style="padding: 1.25rem; background: var(--card-bg); border: 1px solid var(--card-border); border-left: 4px solid var(--success) !important;">
            <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pemasukan</p>
            <h4 style="font-size: 1.5rem; font-weight: 800; color: var(--success);">+ {{ $transaksis->whereIn('jenis', ['jimpitan', 'topup'])->count() }}</h4>
        </div>
        <div class="card" style="padding: 1.25rem; background: var(--card-bg); border: 1px solid var(--card-border); border-left: 4px solid var(--danger) !important;">
            <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pengeluaran</p>
            <h4 style="font-size: 1.5rem; font-weight: 800; color: var(--danger);">- {{ $transaksis->where('jenis', 'pengeluaran')->count() }}</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Transaksi Lengkap</h3>
            <div style="display: flex; gap: 0.75rem;">
                <button class="btn btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak Laporan
                </button>
                <button class="btn btn-primary" onclick="toggleFilterPanel()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        <!-- Collapsible Filter Panel -->
        <div id="filterPanel" style="display: {{ request()->hasAny(['jenis', 'bulan', 'start_date', 'end_date', 'search']) ? 'block' : 'none' }}; border-bottom: 1px solid rgba(255, 255, 255, 0.05); background: rgba(30, 41, 59, 0.35); padding: 1.5rem 2rem;">
            <form action="{{ route('transaksi.index') }}" method="GET">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Cari Warga / Keterangan</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama, NIK, Keterangan..." value="{{ request('search') }}" style="border-radius: 0.75rem; padding: 0.6rem 1rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Jenis Transaksi</label>
                        <select name="jenis" class="form-control" style="border-radius: 0.75rem; padding: 0.6rem 1rem;">
                            <option value="">Semua Jenis</option>
                            <option value="jimpitan" {{ request('jenis') == 'jimpitan' ? 'selected' : '' }}>Jimpitan</option>
                            <option value="topup" {{ request('jenis') == 'topup' ? 'selected' : '' }}>Top Up</option>
                            <option value="donasi" {{ request('jenis') == 'donasi' ? 'selected' : '' }}>Donasi</option>
                            <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Pilih Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}" style="border-radius: 0.75rem; padding: 0.6rem 1rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" style="border-radius: 0.75rem; padding: 0.6rem 1rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" style="border-radius: 0.75rem; padding: 0.6rem 1rem;">
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center; padding: 0.65rem 1rem; border-radius: 0.75rem;">
                            <i class="fas fa-check"></i> Terapkan
                        </button>
                        @if(request()->hasAny(['jenis', 'bulan', 'start_date', 'end_date', 'search']))
                            <a href="{{ route('transaksi.index') }}" class="btn btn-outline" style="justify-content: center; padding: 0.65rem 1rem; border-radius: 0.75rem; color: var(--danger); border-color: rgba(239, 68, 68, 0.2);">
                                <i class="fas fa-rotate-left"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Waktu & Tanggal</th>
                        <th>Warga / Keterangan</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Petugas</th>
                        <th style="text-align: right; padding-right: 2rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $tx)
                    <tr>
                        <td style="padding-left: 2rem;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $tx->created_at->format('d M Y') }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $tx->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            @if($tx->warga)
                                <div style="font-weight: 700;">{{ $tx->warga->nama }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">NIK: {{ $tx->warga->nik }}</div>
                            @else
                                <div style="font-weight: 700; color: var(--secondary);">{{ $tx->keterangan }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Pengeluaran Kas</div>
                            @endif
                        </td>
                        <td>
                            @if($tx->jenis == 'topup')
                                <span class="badge badge-success"><i class="fas fa-arrow-up"></i> TOP UP</span>
                            @elseif($tx->jenis == 'jimpitan')
                                <span class="badge badge-primary"><i class="fas fa-coins"></i> JIMPITAN</span>
                            @elseif($tx->jenis == 'donasi')
                                <span class="badge badge-info"><i class="fas fa-hand-holding-heart"></i> DONASI</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-arrow-down"></i> PENGELUARAN</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 800; font-size: 1rem; color: {{ $tx->jenis == 'pengeluaran' ? 'var(--danger)' : 'var(--success)' }}">
                                {{ $tx->jenis == 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 24px; height: 24px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800;">
                                    {{ strtoupper(substr($tx->user->name, 0, 1)) }}
                                </div>
                                <span style="font-size: 0.8rem; font-weight: 600;">{{ explode(' ', $tx->user->name)[0] }}</span>
                            </div>
                        </td>
                        <td style="text-align: right; padding-right: 2rem;">
                            <button onclick="confirmDeleteTransaction({{ $tx->id }})" class="btn-header" style="border-color: #fee2e2; color: #ef4444; padding: 0.4rem 0.75rem;" title="Batalkan Transaksi">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 4rem;">
                            <img src="https://illustrations.popsy.co/amber/empty-box.svg" alt="Empty" style="width: 150px; margin-bottom: 1.5rem; opacity: 0.5;">
                            <p style="color: var(--text-muted); font-weight: 600;">Belum ada riwayat transaksi yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transaksis->hasPages())
        <div style="padding: 1.5rem 2rem; background: #f8fafc; border-top: 1px solid var(--border);">
            {{ $transaksis->links() }}
        </div>
        @endif
    </div>
</div>

<form id="delete-tx-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function toggleFilterPanel() {
        const panel = document.getElementById('filterPanel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
        }
    }

    function confirmDeleteTransaction(id) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Saldo warga akan dikembalikan/disesuaikan dan transaksi ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-tx-form');
                form.action = `/transaksi/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection

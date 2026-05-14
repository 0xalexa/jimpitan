@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Semua Riwayat Transaksi')

@section('content')
<div class="fade-in">
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log Aktivitas Keuangan</h3>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-outline" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Nama Warga</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $tx->warga->nama }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $tx->warga->nik }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $tx->jenis == 'topup' ? 'badge-success' : 'badge-primary' }}">
                            {{ strtoupper($tx->jenis) }}
                        </span>
                    </td>
                    <td style="font-weight: 700; color: {{ $tx->jenis == 'topup' ? 'var(--success)' : 'var(--text-main)' }}">
                        {{ $tx->jenis == 'topup' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                    </td>
                    <td>{{ $tx->keterangan }}</td>
                    <td>{{ $tx->user->name }}</td>
                    <td>
                        <button onclick="confirmDeleteTransaction({{ $tx->id }})" class="btn btn-outline" style="color: var(--danger); padding: 0.4rem; border: none;" title="Batalkan Transaksi">
                            <i class="fas fa-undo"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 1.5rem; border-top: 1px solid var(--border);">
        {{ $transaksis->links() }}
    </div>
</div>
</div>

<form id="delete-tx-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteTransaction(id) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Saldo warga akan dikembalikan/disesuaikan dan transaksi ini akan dihapus dari riwayat.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
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

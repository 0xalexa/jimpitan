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
                    <th>Nominal</th>
                    <th>Jenis</th>
                    <th>Petugas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td><span style="font-weight: 600;">{{ $tx->warga->nama }}</span></td>
                    <td>
                        <span style="font-weight: 700; color: {{ $tx->jenis == 'topup' ? 'var(--success)' : 'var(--text-main)' }}">
                            {{ $tx->jenis == 'topup' ? '+' : '' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        @if($tx->jenis == 'jimpitan')
                            <span class="badge badge-info">Jimpitan</span>
                        @elseif($tx->jenis == 'topup')
                            <span class="badge badge-success">Top Up</span>
                        @else
                            <span class="badge badge-danger">Tarik</span>
                        @endif
                    </td>
                    <td>{{ $tx->user->name }}</td>
                    <td><span style="font-size: 0.75rem; color: var(--text-muted);">{{ $tx->keterangan }}</span></td>
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
@endsection

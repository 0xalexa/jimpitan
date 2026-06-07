@extends('layouts.app')
@section('title', 'Pos Ronda RT ' . auth()->user()->rt)
@section('page_title', 'Pos Ronda RT ' . auth()->user()->rt)

@section('content')
<div class="container-fluid py-3">
    {{-- Tombol Scan Raksasa --}}
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('scan.index') }}" class="btn-scan-massive text-decoration-none">
                <i class="fas fa-camera fa-2x"></i>
                SCAN QR WARGA
            </a>
        </div>
    </div>

    {{-- Info Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="glass-card p-3 text-center">
                <p class="text-muted mb-1 font-large">Belum Bayar</p>
                <h1 class="fw-bold mb-0" style="color: #ef4444; font-size: 2.5rem;">{{ $wargaBelumScan->count() }}</h1>
            </div>
        </div>
        <div class="col-6">
            <div class="glass-card p-3 text-center">
                <p class="text-muted mb-1 font-large">Sudah Bayar</p>
                <h1 class="fw-bold mb-0" style="color: #10b981; font-size: 2.5rem;">{{ $wargaSudahScan->count() }}</h1>
            </div>
        </div>
    </div>

    {{-- Daftar Warga Belum Bayar Malam Ini --}}
    <h5 class="fw-bold mb-3"><i class="fas fa-list-check me-2 text-danger"></i>Belum Didatangi Malam Ini</h5>

    @forelse($wargaBelumScan as $warga)
    <div class="petugas-warga-item">
        <div>
            <h5 class="mb-1 fw-bold font-xlarge">{{ $warga->nama }}</h5>
            <p class="mb-0 text-muted font-large">RT {{ $warga->rt }} / RW {{ $warga->rw }}</p>
            @if($warga->tunggakan > 0)
            <span class="badge bg-warning mt-2 font-large p-2">
                <i class="fas fa-exclamation-circle"></i> Tunggakan: Rp {{ number_format($warga->tunggakan, 0, ',', '.') }}
            </span>
            @endif
        </div>
        <div class="status-indicator status-red"></div>
    </div>
    @empty
    <div class="glass-card p-4 text-center">
        <i class="fas fa-check-circle fa-3x mb-3" style="color: #10b981;"></i>
        <h4 class="fw-bold">Tugas Selesai!</h4>
        <p class="text-muted mb-0 font-large">Semua rumah di RT {{ auth()->user()->rt }} sudah didatangi malam ini.</p>
    </div>
    @endforelse

    {{-- Daftar Warga Sudah Bayar Malam Ini --}}
    @if($wargaSudahScan->count() > 0)
    <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-check-circle me-2 text-success"></i>Sudah Didatangi Malam Ini</h5>
    @foreach($wargaSudahScan as $warga)
    <div class="petugas-warga-item" style="border-left: 4px solid #10b981; opacity: 0.8;">
        <div>
            <h5 class="mb-1 fw-bold font-xlarge">{{ $warga->nama }}</h5>
            <p class="mb-0 text-muted font-large">RT {{ $warga->rt }} / RW {{ $warga->rw }}</p>
        </div>
        <div class="status-indicator" style="background-color: #10b981;"></div>
    </div>
    @endforeach
    @endif

    {{-- Warga Dengan Tunggakan --}}
    @if($wargaTunggakan->count() > 0)
    <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Warga Menunggak</h5>
    @foreach($wargaTunggakan as $warga)
    <div class="petugas-warga-item" style="border-left: 4px solid #f59e0b;">
        <div>
            <h5 class="mb-1 fw-bold">{{ $warga->nama }}</h5>
            <p class="mb-0 text-muted">RT {{ $warga->rt }} / RW {{ $warga->rw }}</p>
        </div>
        <span class="badge bg-danger rounded-pill font-large p-2">Rp {{ number_format($warga->tunggakan, 0, ',', '.') }}</span>
    </div>
    @endforeach
    @endif
</div>
@endsection

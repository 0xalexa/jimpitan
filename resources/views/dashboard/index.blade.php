@extends('layouts.app')

@yield('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="fade-in">
    <!-- Hero Section -->
    <div style="background: linear-gradient(135deg, var(--primary) 0%, #a855f7 100%); padding: 3rem 2rem; border-radius: 2rem; color: white; margin-bottom: 2rem; box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2); position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
        <div style="position: relative; z-index: 1;">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.025em;">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
            <p style="opacity: 0.9; font-size: 1.125rem; max-width: 600px;">Siap mendigitalisasi jimpitan hari ini? Semua data warga dan statistik kas sudah terupdate secara realtime.</p>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('scan.index') }}" class="btn" style="background: white; color: var(--primary); padding: 0.875rem 1.75rem; border-radius: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                    <i class="fas fa-qrcode"></i> Scan Jimpitan
                </a>
                <a href="{{ route('warga.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 0.875rem 1.75rem; border-radius: 1rem; backdrop-filter: blur(8px);">
                    <i class="fas fa-plus-circle"></i> Top Up Saldo
                </a>
                <a href="{{ route('warga.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 0.875rem 1.75rem; border-radius: 1rem;">
                    <i class="fas fa-users"></i> Kelola Warga
                </a>
            </div>
        </div>
        <!-- Abstract Shapes -->
        <div style="position: absolute; right: -5%; top: -10%; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(40px);"></div>
        <div style="position: absolute; right: 10%; bottom: -20%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(30px);"></div>
        <i class="fas fa-shield-alt" style="position: absolute; right: 2rem; bottom: 1rem; font-size: 12rem; opacity: 0.05; transform: rotate(-15deg);"></i>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-bottom: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="stat-label">Total Saldo Kas</span>
                    <span class="stat-value" style="display: block; margin-top: 0.5rem;">Rp {{ number_format($totalKas, 0, ',', '.') }}</span>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.1); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
            </div>
            <p style="font-size: 0.75rem; color: var(--success); margin-top: 1rem; font-weight: 600;"><i class="fas fa-arrow-up"></i> Terakumulasi dari semua warga</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid var(--success);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="stat-label">Pemasukan Hari Ini</span>
                    <span class="stat-value" style="display: block; margin-top: 0.5rem;">Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}</span>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: var(--success);">
                    <i class="fas fa-hand-holding-usd fa-lg"></i>
                </div>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">Update terakhir: {{ now()->format('H:i') }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid var(--warning);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="stat-label">Belum Bayar Hari Ini</span>
                    <span class="stat-value" style="display: block; margin-top: 0.5rem; color: var(--danger);">{{ $totalWargaBelumBayar }} Warga</span>
                </div>
                <div style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: var(--warning);">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                </div>
            </div>
            <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 3px; margin-top: 1rem; overflow: hidden;">
                <div style="width: {{ ($wargaLunasCount/$totalWarga)*100 }}%; height: 100%; background: var(--success);"></div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Left Side: Chart & Top Warga -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card glass-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area" style="color: var(--primary); margin-right: 0.5rem;"></i> Grafik Aktivitas Mingguan</h3>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="chart-container">
                        <canvas id="jimpitanChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card glass-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star" style="color: var(--warning); margin-right: 0.5rem;"></i> Warga dengan Saldo Tertinggi</h3>
                </div>
                <div class="table-responsive">
                    <table style="border: none;">
                        <thead>
                            <tr>
                                <th>Nama Warga</th>
                                <th>Saldo Terkini</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topBalanceWarga as $w)
                            <tr>
                                <td style="font-weight: 600;">{{ $w->nama }}</td>
                                <td style="color: var(--success); font-weight: 700;">Rp {{ number_format($w->saldo, 0, ',', '.') }}</td>
                                <td><span class="badge badge-success">Aktif</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Recent Activities -->
        <div class="card glass-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title"><i class="fas fa-bell" style="color: var(--primary); margin-right: 0.5rem;"></i> Aktivitas Terakhir</h3>
                <span class="badge badge-info" style="font-size: 0.65rem;">LIVE</span>
            </div>
            <div style="padding: 1rem;">
                @forelse($recentTransactions as $tx)
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.25rem; position: relative;">
                        <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                            <div style="width: 32px; height: 32px; background: {{ $tx->jenis == 'topup' ? 'var(--success)' : 'var(--primary)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <i class="fas {{ $tx->jenis == 'topup' ? 'fa-plus' : 'fa-check' }}"></i>
                            </div>
                            @if(!$loop->last)
                            <div style="width: 2px; height: calc(100% + 0.25rem); background: #e2e8f0; margin-top: 0.25rem;"></div>
                            @endif
                        </div>
                        <div style="flex: 1; padding-bottom: 0.75rem;">
                            <p style="font-weight: 700; font-size: 0.875rem; margin-bottom: 0.15rem;">{{ $tx->warga->nama }}</p>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">{{ $tx->jenis == 'topup' ? 'Melakukan Top Up' : 'Bayar Jimpitan' }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: {{ $tx->jenis == 'topup' ? 'var(--success)' : 'var(--text-main)' }}">
                                    {{ $tx->jenis == 'topup' ? '+' : '-' }}Rp{{ number_format($tx->nominal, 0, ',', '.') }}
                                </span>
                                <span style="font-size: 0.65rem; color: var(--text-muted);">{{ $tx->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem 0;">
                        <i class="fas fa-clipboard-list" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">Belum ada aktivitas.</p>
                    </div>
                @endforelse
            </div>
            <div style="padding: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline" style="width: 100%; justify-content: center; border-radius: 0.75rem;">Lihat Log Lengkap</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('jimpitanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: @json($chartData['values']),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#6366f1',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush

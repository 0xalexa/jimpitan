@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Overview')

@section('content')
    <!-- Welcome Hero -->
    <div class="hero-banner">
        <div class="hero-content">
            <h1 class="hero-title">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
            <p class="hero-subtitle">Pantau kesehatan kas jimpitan hari ini secara realtime. Data warga dan statistik keuangan sudah terintegrasi otomatis.</p>
            
            <div class="hero-actions">
                <a href="{{ route('scan.index') }}" class="btn-hero white">
                    <i class="fas fa-camera"></i> Scan Jimpitan
                </a>
                <button onclick="openModal('topupModal')" class="btn-hero glass">
                    <i class="fas fa-wallet"></i> Top Up Saldo
                </button>
                <button onclick="openModal('spendingModal')" class="btn-hero glass">
                    <i class="fas fa-receipt"></i> Catat Pengeluaran
                </button>
                <button onclick="openModal('donationModal')" class="btn-hero glass">
                    <i class="fas fa-hand-holding-heart"></i> Donasi Kas
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <!-- Total Kas -->
        <div class="card-stat" onclick="openModal('summaryModal')">
            <div class="stat-header">
                <div class="stat-info">
                    <p class="label">Total Saldo Kas RT</p>
                    <h3 class="value">Rp {{ number_format($totalKas, 0, ',', '.') }}</h3>
                </div>
                <div class="stat-icon" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-bank"></i>
                </div>
            </div>
            <div class="stat-footer trend-up">
                <i class="fas fa-arrow-trend-up"></i>
                <span>Lihat riwayat aliran dana</span>
            </div>
        </div>

        <!-- Lunas -->
        <div class="card-stat" onclick="openModal('lunasModal')">
            <div class="stat-header">
                <div class="stat-info">
                    <p class="label">Warga Sudah Bayar</p>
                    <h3 class="value">{{ $wargaLunasCount }} <span style="font-size: 1rem; color: var(--text-muted);">Warga</span></h3>
                </div>
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-footer" style="color: #64748b;">
                <i class="fas fa-users"></i>
                <span>Dari total {{ $totalWarga }} warga aktif</span>
            </div>
        </div>

        <!-- Belum Bayar -->
        <div class="card-stat" onclick="openModal('belumBayarModal')">
            <div class="stat-header">
                <div class="stat-info">
                    <p class="label">Tunggakan Hari Ini</p>
                    <h3 class="value" style="color: var(--danger);">{{ $totalWargaBelumBayar }} <span style="font-size: 1rem; color: var(--text-muted);">Warga</span></h3>
                </div>
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-footer trend-down">
                <i class="fas fa-clock"></i>
                <span>Perlu penagihan jimpitan</span>
            </div>
        </div>
    </div>

    <!-- Charts & Activity -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 2rem;">
        <div class="content-card">
            <div class="card-header">
                <h4 class="card-title">Statistik Jimpitan Mingguan</h4>
                <div style="display: flex; gap: 0.5rem;">
                   <span class="badge badge-success">Live</span>
                </div>
            </div>
            <div style="padding: 1.5rem; height: 350px;">
                <canvas id="jimpitanChart"></canvas>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h4 class="card-title">Aktivitas Terakhir</h4>
            </div>
            <div style="padding: 1rem;">
                <!-- Activity List Mockup -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($recentActivities as $activity)
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem; border-radius: 0.75rem; background: #f8fafc;">
                        <div style="width: 32px; height: 32px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                            <i class="fas fa-circle-check" style="color: var(--success);"></i>
                        </div>
                        <div>
                            <p style="font-size: 0.8rem; font-weight: 700;">{{ $activity->warga->nama ?? 'Sistem' }}</p>
                            <p style="font-size: 0.7rem; color: var(--text-muted);">{{ $activity->keterangan }}</p>
                            <p style="font-size: 0.65rem; color: var(--primary); margin-top: 0.25rem;">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada aktivitas hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Section -->
    <div id="summaryModal" class="modal-overlay" onclick="handleOverlayClick(event, 'summaryModal')">
        <div class="modal-pro">
            <div class="card-header">
                <h3 class="card-title">Detail Aliran Kas</h3>
                <button onclick="closeModal('summaryModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 2rem;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="padding: 1.25rem; background: #f0fdf4; border-radius: 1rem; border: 1px solid #bbf7d0; display: flex; justify-content: space-between;">
                        <span>Total Pemasukan Jimpitan</span>
                        <strong style="color: #166534;">Rp {{ number_format($totalJimpitan, 0, ',', '.') }}</strong>
                    </div>
                    <div style="padding: 1.25rem; background: #eff6ff; border-radius: 1rem; border: 1px solid #bfdbfe; display: flex; justify-content: space-between;">
                        <span>Total Pemasukan Top Up</span>
                        <strong style="color: #1e40af;">Rp {{ number_format($totalTopup, 0, ',', '.') }}</strong>
                    </div>
                    <div style="padding: 1.25rem; background: #fef2f2; border-radius: 1rem; border: 1px solid #fecaca; display: flex; justify-content: space-between;">
                        <span>Total Pengeluaran</span>
                        <strong style="color: #991b1b;">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
                    </div>
                    <div style="padding: 1.25rem; background: #fff7ed; border-radius: 1rem; border: 1px solid #ffedd5; display: flex; justify-content: space-between;">
                        <span>Total Donasi Kas</span>
                        <strong style="color: #9a3412;">+ Rp {{ number_format($totalDonasi, 0, ',', '.') }}</strong>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0.5rem 0;">
                    <div style="padding: 1.25rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 1.1rem;">
                        <strong>Saldo Akhir</strong>
                        <strong>Rp {{ number_format($totalKas, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warga Lunas Modal -->
    <div id="lunasModal" class="modal-overlay" onclick="handleOverlayClick(event, 'lunasModal')">
        <div class="modal-pro" style="max-width: 600px;">
            <div class="card-header">
                <h3 class="card-title">Warga Sudah Bayar Hari Ini</h3>
                <button onclick="closeModal('lunasModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 1.5rem; max-height: 400px; overflow-y: auto;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wargaLunas as $warga)
                            <tr>
                                <td style="font-weight: 700;">{{ $warga->nama }}</td>
                                <td style="font-size: 0.75rem;">{{ $warga->transaksis()->whereDate('created_at', now())->latest()->first()->created_at->format('H:i') }}</td>
                                <td><span class="badge badge-success">Lunas</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Belum Bayar Modal -->
    <div id="belumBayarModal" class="modal-overlay" onclick="handleOverlayClick(event, 'belumBayarModal')">
        <div class="modal-pro" style="max-width: 600px;">
            <div class="card-header">
                <h3 class="card-title">Warga Belum Bayar Hari Ini</h3>
                <button onclick="closeModal('belumBayarModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 1.5rem; max-height: 400px; overflow-y: auto;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tunggakan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wargaBelumBayar as $warga)
                            <tr>
                                <td style="font-weight: 700;">{{ $warga->nama }}</td>
                                <td style="color: var(--danger); font-weight: 700;">Rp {{ number_format($warga->tunggakan, 0, ',', '.') }}</td>
                                <td><button class="btn-header" onclick="closeModal('belumBayarModal'); location.href='{{ route('scan.index') }}'">Tagih</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Up Modal -->
    <div id="topupModal" class="modal-overlay" onclick="handleOverlayClick(event, 'topupModal')">
        <div class="modal-pro">
            <div class="card-header">
                <h3 class="card-title">Top Up Saldo Warga</h3>
                <button onclick="closeModal('topupModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 2rem;">
                <form action="{{ route('transaksi.topup') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Pilih Warga</label>
                        <select name="warga_id" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" required>
                            <option value="">-- Cari Nama Warga --</option>
                            @foreach($wargas as $w)
                            <option value="{{ $w->id }}">{{ $w->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: 10000" required>
                    </div>
                    <button type="submit" class="btn-header primary" style="width: 100%; padding: 1rem; border-radius: 1rem;">Proses Top Up</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Spending Modal -->
    <div id="spendingModal" class="modal-overlay" onclick="handleOverlayClick(event, 'spendingModal')">
        <div class="modal-pro">
            <div class="card-header">
                <h3 class="card-title">Catat Pengeluaran Kas</h3>
                <button onclick="closeModal('spendingModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 2rem;">
                <form action="{{ route('transaksi.pengeluaran') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Keterangan</label>
                        <input type="text" name="keterangan" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: Beli sapu lidi" required>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: 50000" required>
                    </div>
                    <button type="submit" class="btn-header primary" style="width: 100%; padding: 1rem; border-radius: 1rem; background: var(--danger);">Simpan Pengeluaran</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Donation Modal -->
    <div id="donationModal" class="modal-overlay" onclick="handleOverlayClick(event, 'donationModal')">
        <div class="modal-pro">
            <div class="card-header">
                <h3 class="card-title">Terima Donasi Kas</h3>
                <button onclick="closeModal('donationModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 2rem;">
                <form action="{{ route('transaksi.donasi') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nama Donatur (Opsional)</label>
                        <input type="text" name="nama_donatur" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: Hamba Allah / Bpk. Andi">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Keterangan Donasi</label>
                        <input type="text" name="keterangan" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: Untuk santunan anak yatim" required>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nominal Donasi (Rp)</label>
                        <input type="number" name="nominal" class="btn-header" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;" placeholder="Contoh: 100000" required>
                    </div>
                    <button type="submit" class="btn-header primary" style="width: 100%; padding: 1rem; border-radius: 1rem; background: var(--success);">Simpan Donasi</button>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Zoom Modal -->
    <div id="qrZoomModal" class="modal-overlay" onclick="handleOverlayClick(event, 'qrZoomModal')">
        <div class="modal-pro" style="max-width: 400px; text-align: center;">
            <div class="card-header">
                <h3 class="card-title">QR Code Warga</h3>
                <button onclick="closeModal('qrZoomModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
            </div>
            <div style="padding: 2.5rem;">
                <div id="qrZoomContainer" style="display: flex; justify-content: center; margin-bottom: 1rem;"></div>
                <p id="qrZoomName" style="font-weight: 800; font-size: 1.25rem; color: var(--text-main);"></p>
                <p id="qrZoomString" style="font-family: monospace; color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;"></p>
            </div>
        </div>
    </div>

    <!-- Summary per RT -->
    <div class="content-card" style="margin-top: 2rem;">
        <div class="card-header">
            <h4 class="card-title">Sebaran Saldo per Wilayah (RT/RW)</h4>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Wilayah</th>
                        <th>Total Warga</th>
                        <th>Total Saldo Digital</th>
                        <th style="text-align: right; padding-right: 2rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statsPerRT as $stat)
                    <tr>
                        <td style="padding-left: 2rem; font-weight: 800;">RT {{ $stat->rt }} / RW {{ $stat->rw }}</td>
                        <td>{{ $stat->total_warga }} Orang</td>
                        <td style="font-weight: 700; color: var(--primary);">Rp {{ number_format($stat->total_saldo, 0, ',', '.') }}</td>
                        <td style="text-align: right; padding-right: 2rem;">
                            <span class="badge badge-success">Aktif</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada data wilayah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // QR Zoom Logic
    function zoomQR(nama, string) {
        const container = document.getElementById('qrZoomContainer');
        container.innerHTML = '';
        document.getElementById('qrZoomName').innerText = nama;
        document.getElementById('qrZoomString').innerText = string;
        
        new QRCode(container, {
            text: string,
            width: 250,
            height: 250,
            colorDark: "#1e293b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        openModal('qrZoomModal');
    }

    // Chart Initialization
    const ctx = document.getElementById('jimpitanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: @json($chartData),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4f46e5'
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
                    ticks: { font: { family: 'Inter' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter' } }
                }
            }
        }
    });

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function handleOverlayClick(e, id) {
        if (e.target.id === id) closeModal(id);
    }
</script>
@endpush

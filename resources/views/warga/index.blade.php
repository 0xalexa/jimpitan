@extends('layouts.app')

@section('title', 'Data Warga')
@section('page_title', 'Manajemen Data Warga')

@section('content')
<div class="fade-in">
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 class="card-title">Daftar Warga RT/RW</h3>
        <div style="display: flex; gap: 1rem; align-items: center; flex: 1; justify-content: flex-end;">
            <div style="position: relative; flex: 1; max-width: 300px;">
                <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" id="wargaSearch" class="form-control" placeholder="Cari nama atau NIK..." style="padding-left: 2.5rem; border-radius: 2rem;">
            </div>
            <button onclick="exportToCSV()" class="btn btn-outline" style="border-radius: 1rem;">
                <i class="fas fa-file-export"></i> Export
            </button>
            <a href="{{ route('warga.create') }}" class="btn btn-primary" style="border-radius: 1rem;">
                <i class="fas fa-plus"></i> Tambah Warga
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">QR Code</th>
                    <th>Nama / NIK</th>
                    <th>Alamat</th>
                    <th>Saldo</th>
                    <th>Tunggakan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wargas as $warga)
                @php
                    $isPaid = $warga->transaksis()->whereDate('created_at', now())->where('jenis', 'jimpitan')->exists();
                @endphp
                <tr>
                    <td>
                        <div style="background: white; padding: 4px; border-radius: 8px; width: fit-content; border: 1px solid #e2e8f0; cursor: pointer;" onclick="showQR('{{ $warga->nama }}', '{{ $warga->qr_code_string }}')">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data={{ $warga->qr_code_string }}" alt="QR" style="display: block; width: 40px; height: 40px;">
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $warga->nama }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $warga->nik }}</div>
                    </td>
                    <td>{{ $warga->alamat }}</td>
                    <td>
                        <span style="font-weight: 700; color: {{ $warga->saldo < 500 ? 'var(--danger)' : 'var(--success)' }}">
                            Rp {{ number_format($warga->saldo, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: {{ $warga->tunggakan > 0 ? 'var(--danger)' : 'var(--text-muted)' }}">
                            Rp {{ number_format($warga->tunggakan, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        @if($isPaid)
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Belum</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-outline" style="padding: 0.5rem;" onclick="showQR('{{ $warga->nama }}', '{{ $warga->qr_code_string }}')" title="Tampilkan QR">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            <button class="btn btn-outline" style="padding: 0.5rem;" onclick="showTopup({{ $warga->id }}, '{{ $warga->nama }}')" title="Top Up">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <button class="btn btn-outline" style="padding: 0.5rem; color: var(--success);" onclick="confirmManual({{ $warga->id }}, '{{ $warga->nama }}')" title="Bayar Manual" {{ $isPaid ? 'disabled' : '' }}>
                                <i class="fas fa-hand-holding-usd"></i>
                            </button>
                            <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-outline" style="padding: 0.5rem;" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal QR -->
<div id="qrModal" class="modal-overlay" onclick="handleOverlayClick(event, 'qrModal')">
    <div class="modal-pro" style="max-width: 400px; text-align: center;">
        <div class="card-header">
            <h3 class="card-title">QR Code Warga</h3>
            <button onclick="closeModal('qrModal')" style="border:none; background:none; cursor:pointer; font-size: 1.5rem;">&times;</button>
        </div>
        <div style="padding: 2.5rem;">
            <div id="qrcode" style="display: flex; justify-content: center; margin-bottom: 1rem;"></div>
            <p id="qrWargaName" style="font-weight: 800; font-size: 1.25rem; color: var(--text-main);"></p>
            <p id="qrString" style="font-family: monospace; color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;"></p>
        </div>
    </div>
</div>


<!-- Modal Top Up -->
<div class="modal-overlay" id="topupModal">
    <div class="modal glass-card" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Top Up Saldo - <span id="topupWargaName"></span></h3>
            <button onclick="closeModal('topupModal')">&times;</button>
        </div>
        <div style="padding: 2rem;">
            <form action="{{ route('transaksi.topup') }}" method="POST">
                @csrf
                <input type="hidden" name="warga_id" id="topupWargaId">
                <div class="form-group">
                    <label>Nominal Top Up (Rp)</label>
                    <input type="number" name="nominal" class="form-control" placeholder="Contoh: 10000" required style="border-radius: 1rem; padding: 0.75rem;">
                </div>
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Metode Pembayaran</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                        <label class="payment-option">
                            <input type="radio" name="metode_pembayaran" value="Tunai" checked>
                            <div class="payment-box">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tunai</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="metode_pembayaran" value="Transfer Bank">
                            <div class="payment-box">
                                <i class="fas fa-university"></i>
                                <span>Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem; padding: 1rem; border-radius: 1.25rem; font-weight: 700;">
                    Proses Top Up
                </button>
            </form>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    let qrcodeInstance = null;

    function showQR(nama, string) {
        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = '';
        document.getElementById('qrWargaName').innerText = nama;
        document.getElementById('qrString').innerText = string;
        
        new QRCode(qrContainer, {
            text: string,
            width: 250,
            height: 250,
            colorDark: "#1e293b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        openModal('qrModal');
    }

    function showTopup(id, nama) {
        document.getElementById('topupWargaId').value = id;
        document.getElementById('topupWargaName').innerText = nama;
        openModal('topupModal');
    }

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function handleOverlayClick(e, id) {
        if (e.target.id === id) closeModal(id);
    }

    function confirmManual(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Bayar',
            text: `Apakah Anda yakin ingin memproses iuran manual Rp 500 untuk ${nama}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Bayar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('manual-id').value = id;
                document.getElementById('manual-form').submit();
            }
        });
    }

    
    // Search Functionality
    document.getElementById('wargaSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });

    // Export to CSV
    function exportToCSV() {
        let rows = document.querySelectorAll('table tr');
        let csv = [];
        rows.forEach(row => {
            let cols = row.querySelectorAll('td, th');
            let rowData = [];
            cols.forEach((col, index) => {
                // Skip action column
                if (index < 5) rowData.push('"' + col.innerText.trim() + '"');
            });
            csv.push(rowData.join(','));
        });
        
        let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "data_warga_jimpitan.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });
</script>

<form id="manual-form" action="{{ route('transaksi.manual') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="warga_id" id="manual-id">
</form>
@endpush

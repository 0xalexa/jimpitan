@extends('layouts.app')

@section('title', 'Data Warga')
@section('page_title', 'Manajemen Data Warga')

@section('content')
<div class="fade-in">
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Warga RT/RW</h3>
        <a href="{{ route('warga.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Warga
        </a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama / NIK</th>
                    <th>Alamat</th>
                    <th>Saldo Digital</th>
                    <th>Status Hari Ini</th>
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
                        @if($isPaid)
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Belum Bayar</span>
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
<div class="modal-overlay" id="qrModal">
    <div class="modal" style="text-align: center;">
        <h3 id="qrWargaName" style="margin-bottom: 1.5rem;">QR Code Warga</h3>
        <div id="qrcode" style="display: flex; justify-content: center; margin-bottom: 1.5rem;"></div>
        <p id="qrString" style="font-family: monospace; background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.875rem;"></p>
        <div style="margin-top: 2rem;">
            <button class="btn btn-primary" onclick="closeModal('qrModal')">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Top Up -->
<div class="modal-overlay" id="topupModal">
    <div class="modal">
        <h3>Top Up Saldo - <span id="topupWargaName"></span></h3>
        <form action="{{ route('transaksi.topup') }}" method="POST" style="margin-top: 1.5rem;">
            @csrf
            <input type="hidden" name="warga_id" id="topupWargaId">
            <div class="form-group">
                <label>Nominal Top Up (Rp)</label>
                <input type="number" name="nominal" class="form-control" placeholder="Contoh: 10000" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="closeModal('topupModal')">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    let qrcodeInstance = null;

    function showQR(nama, string) {
        document.getElementById('qrWargaName').innerText = 'QR Code: ' + nama;
        document.getElementById('qrString').innerText = string;
        
        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = '';
        
        new QRCode(qrContainer, {
            text: string,
            width: 200,
            height: 200,
            colorDark: "#1e293b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        document.getElementById('qrModal').classList.add('active');
    }

    function showTopup(id, nama) {
        document.getElementById('topupWargaId').value = id;
        document.getElementById('topupWargaName').innerText = nama;
        document.getElementById('topupModal').classList.add('active');
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

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
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

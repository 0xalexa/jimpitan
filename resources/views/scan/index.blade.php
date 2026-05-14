@extends('layouts.app')

@section('title', 'Scan QR Code')
@section('page_title', 'Penarikan Jimpitan Digital')

@section('content')
<div class="fade-in">
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Scanner Side -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-camera"></i> Scanner Kamera</h3>
        </div>
        <div style="padding: 1.5rem; text-align: center;">
            <div id="reader" style="width: 100%; border-radius: 1rem; overflow: hidden; background: #000;"></div>
            <div id="result" style="margin-top: 1.5rem; display: none;">
                <div class="badge badge-info" id="scanned-text">Memproses...</div>
            </div>
            <div style="margin-top: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted);">
                    Arahkan kamera ke QR Code warga untuk melakukan pemotongan saldo otomatis.
                </p>
            </div>
        </div>
    </div>

    <!-- Info/Manual Side -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Panduan Penggunaan</h3>
            </div>
            <div style="padding: 1.5rem;">
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 1rem; font-size: 0.875rem;">
                    <li style="display: flex; gap: 0.75rem;">
                        <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
                        <span>Pastikan pencahayaan cukup saat menscan QR Code.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
                        <span>Sistem akan memotong saldo sebesar <strong>Rp 500</strong> secara otomatis.</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem;">
                        <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
                        <span>Jika QR rusak, gunakan fitur <strong>Pembayaran Manual</strong> di menu Data Warga.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card" id="scan-success-card" style="display: none; border-left: 5px solid var(--success);">
            <div class="card-header">
                <h3 class="card-title">Scan Berhasil!</h3>
            </div>
            <div style="padding: 1.5rem; text-align: center;">
                <div style="font-size: 3rem; color: var(--success); margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 id="success-nama" style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">-</h2>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Saldo berhasil dipotong Rp 500</p>
                <div style="background: #f1f5f9; padding: 1rem; border-radius: 0.5rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Sisa Saldo Digital:</span>
                    <span id="success-saldo" style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        // Stop scanning after success
        html5QrCode.pause();
        
        document.getElementById('result').style.display = 'block';
        document.getElementById('scanned-text').innerText = 'Ditemukan: ' + decodedText;

        // AJAX Process
        fetch("{{ route('scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Show info card
                document.getElementById('scan-success-card').style.display = 'block';
                document.getElementById('success-nama').innerText = data.data.nama;
                document.getElementById('success-saldo').innerText = 'Rp ' + data.data.saldo_sisa.toLocaleString('id-ID');

                // Resume scanning after 3 seconds
                setTimeout(() => {
                    html5QrCode.resume();
                    document.getElementById('result').style.display = 'none';
                }, 3000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message
                }).then(() => {
                    html5QrCode.resume();
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error').then(() => {
                html5QrCode.resume();
            });
        });
    };

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
    .catch((err) => {
        console.error("Scanner Error:", err);
        // Fallback or Alert
    });
</script>
@endpush

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
        <div style="padding: 2rem; text-align: center;">
            <div class="scanner-container">
                <div class="scan-line"></div>
                <div class="scanner-overlay"></div>
                <div class="scanner-corner corner-tl"></div>
                <div class="scanner-corner corner-tr"></div>
                <div class="scanner-corner corner-bl"></div>
                <div class="scanner-corner corner-br"></div>
                <div id="reader" style="width: 100%; border: none;"></div>
            </div>
            
            <div id="result" style="margin-top: 2rem; display: none;">
                <div class="badge badge-info" id="scanned-text">Memproses...</div>
            </div>
            
            <div style="margin-top: 2rem;">
                <p style="font-size: 0.95rem; color: var(--text-muted); font-weight: 500;">
                    Arahkan kamera ke QR Code warga untuk penarikan jimpitan otomatis.
                </p>
            </div>
        </div>
    </div>

    <!-- Info/Manual Side -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card" style="border-left: 5px solid var(--primary);">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-microchip"></i> Simulasi & Input Manual</h3>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Option 1: Dropdown Selection -->
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-main);">Pilih Warga (Simulasi Scan)</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <select id="simulateWargaSelect" class="form-control" style="border-radius: 0.75rem; flex: 1;">
                                <option value="">-- Pilih Warga --</option>
                                @foreach($wargas as $w)
                                    <option value="{{ $w->qr_code_string }}">{{ $w->nama }} ({{ $w->qr_code_string }})</option>
                                @endforeach
                            </select>
                            <button onclick="simulateScan()" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 0.75rem; font-weight: 700;">
                                <i class="fas fa-play"></i> Simulasikan
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div style="display: flex; align-items: center; text-align: center; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin: 0.25rem 0;">
                        <span style="flex-grow: 1; border-top: 1px solid var(--border); margin-right: 0.5rem;"></span>
                        <span>atau</span>
                        <span style="flex-grow: 1; border-top: 1px solid var(--border); margin-left: 0.5rem;"></span>
                    </div>

                    <!-- Option 2: Manual Text Input -->
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.85rem; color: var(--text-main);">Input Kode QR Manual</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="manualQRInput" class="form-control" placeholder="Contoh: QR-BUD-1000" style="border-radius: 0.75rem; flex: 1;">
                            <button onclick="submitManualQR()" class="btn btn-outline" style="padding: 0.5rem 1rem; border-radius: 0.75rem; border-color: var(--primary); color: var(--primary); font-weight: 700;">
                                <i class="fas fa-keyboard"></i> Kirim
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <span>Jika QR rusak, gunakan fitur **Simulasi & Input Manual** di atas atau iuran manual di Data Warga.</span>
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
    let html5QrCode;
    try {
        html5QrCode = new Html5Qrcode("reader");
    } catch (e) {
        console.error("Reader container element not found yet or HTML5Qrcode error:", e);
    }

    function processQRCodeString(qrCodeString) {
        // Pause camera scanner if it is active
        if (html5QrCode) {
            try { html5QrCode.pause(); } catch (e) {}
        }
        
        document.getElementById('result').style.display = 'block';
        document.getElementById('scanned-text').innerText = 'Memproses: ' + qrCodeString;

        // AJAX Process
        fetch("{{ route('scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: qrCodeString })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const scannerContainer = document.querySelector('.scanner-container');
                if (scannerContainer) {
                    scannerContainer.classList.add('success-pulse');
                    setTimeout(() => scannerContainer.classList.remove('success-pulse'), 500);
                }

                const resultDiv = document.getElementById('result');
                resultDiv.innerHTML = `
                    <div style="text-align: center; animation: pulse 1s infinite;">
                        <div style="width: 80px; height: 80px; background: var(--success); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem; box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-main);">Berhasil!</h3>
                        <p style="font-weight: 700; color: var(--primary); font-size: 1.25rem; margin-bottom: 0.25rem;">${data.data.nama}</p>
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">Saldo Sisa: Rp ${new Intl.NumberFormat('id-ID').format(data.data.saldo_sisa)}</p>
                        <button onclick="resumeScan()" class="btn btn-primary" style="width: 100%; justify-content: center;">Scan Lagi</button>
                    </div>
                `;
                resultDiv.style.display = 'block';
                
                // Sound Effect
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
                    oscillator.connect(audioCtx.destination);
                    oscillator.start();
                    oscillator.stop(audioCtx.currentTime + 0.1);
                } catch (e) {
                    console.log("AudioContext blocked or not supported:", e);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message
                }).then(() => {
                    resumeScan();
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error').then(() => {
                resumeScan();
            });
        });
    }

    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        processQRCodeString(decodedText);
    };

    function simulateScan() {
        const qrString = document.getElementById('simulateWargaSelect').value;
        if (!qrString) {
            Swal.fire('Peringatan', 'Silakan pilih warga terlebih dahulu.', 'warning');
            return;
        }
        processQRCodeString(qrString);
    }

    function submitManualQR() {
        const qrString = document.getElementById('manualQRInput').value.trim();
        if (!qrString) {
            Swal.fire('Peringatan', 'Silakan masukkan kode QR terlebih dahulu.', 'warning');
            return;
        }
        processQRCodeString(qrString);
    }

    function onScanFailure(error) {
        // console.warn(`Code scan error = ${error}`);
    }

    function resumeScan() {
        document.getElementById('result').style.display = 'none';
        if (html5QrCode) {
            try { html5QrCode.resume(); } catch (e) {}
        }
    }

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    if (html5QrCode) {
        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
        .catch((err) => {
            console.warn("Scanner Kamera gagal/ditolak. Sistem fallback ke input manual.", err);
            const scanLine = document.querySelector('.scan-line');
            if (scanLine) scanLine.style.display = 'none';
            const reader = document.getElementById('reader');
            if (reader) {
                reader.innerHTML = `
                    <div style="padding: 2rem; color: var(--text-muted);">
                        <i class="fas fa-video-slash" style="font-size: 3rem; margin-bottom: 1rem; color: var(--secondary);"></i>
                        <p style="font-size: 0.9rem; font-weight: 600;">Kamera tidak dapat diakses.</p>
                        <p style="font-size: 0.8rem; margin-top: 0.25rem;">Gunakan panel <strong>Simulasi & Input Manual</strong> di sebelah kanan untuk memproses jimpitan.</p>
                    </div>
                `;
            }
        });
    }
</script>
@endpush

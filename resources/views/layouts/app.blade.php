<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Jimpitan Digital</title>
    
    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/glass.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts Core -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    @stack('styles')
</head>
<body>
    <div class="mouse-glow" id="mouseGlow"></div>

    <div class="app-wrapper">
        <!-- Sidebar Pro -->
        <aside class="sidebar" id="sidebar">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="brand-name">Jimpitan</span>
            </div>

            <nav class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-grid-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->role !== 'petugas')
                <li class="nav-item">
                    <a href="{{ route('warga.index') }}" class="nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                        <i class="fas fa-users-viewfinder"></i>
                        <span>Data Warga</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'petugas')
                <li class="nav-item">
                    <a href="{{ route('scan.index') }}" class="nav-link {{ request()->routeIs('scan.index') ? 'active' : '' }}">
                        <i class="fas fa-qrcode"></i>
                        <span>Scan QR Code</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->role !== 'petugas')
                <li class="nav-item">
                    <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        <span>Riwayat Kas</span>
                    </a>
                </li>
                @endif
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-meta">
                    <p class="name">{{ Auth::user()->name }}</p>
                    <p class="role">{{ Auth::user()->role }}</p>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper" id="mainWrapper">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <span class="page-title">@yield('page_title', 'Dashboard')</span>
                </div>

                <div class="header-right">
                    <div class="header-tools">
                        <button class="btn-header">
                            <i class="far fa-calendar"></i>
                            <span>{{ date('d M Y') }}</span>
                        </button>
                        @if(Auth::user()->role !== 'petugas')
                        <button class="btn-header primary" onclick="location.href='{{ route('transaksi.export') }}'">
                            <i class="fas fa-cloud-arrow-down"></i>
                            <span>Export Laporan</span>
                        </button>
                        @endif
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-header" style="border: 1px solid rgba(244, 63, 94, 0.25) !important; background: rgba(244, 63, 94, 0.1) !important; color: #f43f5e !important;">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <script>
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Berhasil', 
                            text: "{{ session('success') }}", 
                            timer: 3000, 
                            showConfirmButton: false 
                        });

                        // Auditory & Visual Premium Effects
                        (function() {
                            const successMsg = "{{ session('success') }}".toLowerCase();
                            
                            // 1. Play Premium Audio Chimes Synthetically via Web Audio API
                            try {
                                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                                
                                const playChime = (freq, time, duration) => {
                                    const osc = audioCtx.createOscillator();
                                    const gain = audioCtx.createGain();
                                    osc.type = 'sine';
                                    osc.frequency.setValueAtTime(freq, audioCtx.currentTime + time);
                                    gain.gain.setValueAtTime(0.12, audioCtx.currentTime + time);
                                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + time + duration);
                                    osc.connect(gain);
                                    gain.connect(audioCtx.destination);
                                    osc.start(audioCtx.currentTime + time);
                                    osc.stop(audioCtx.currentTime + time + duration);
                                };

                                if (successMsg.includes('donasi')) {
                                    // Ascending major chord (cheerful & warm)
                                    playChime(523.25, 0, 0.4);     // C5
                                    playChime(659.25, 0.1, 0.4);   // E5
                                    playChime(783.99, 0.2, 0.5);   // G5
                                } else if (successMsg.includes('top up')) {
                                    // High premium wallet ring
                                    playChime(587.33, 0, 0.35);    // D5
                                    playChime(880.00, 0.08, 0.45);  // A5
                                } else if (successMsg.includes('pengeluaran')) {
                                    // Warm warning chime
                                    playChime(440.00, 0, 0.3);     // A4
                                    playChime(554.37, 0.1, 0.4);   // C#5
                                } else {
                                    // Standard digital chime
                                    playChime(523.25, 0, 0.3);     // C5
                                    playChime(659.25, 0.1, 0.4);   // E5
                                }
                            } catch (e) {
                                console.log("AudioContext blocked or not supported:", e);
                            }

                            // 2. Fire Visual Confetti Showers
                            try {
                                if (successMsg.includes('donasi')) {
                                    // Golden & Emerald celebration explosion
                                    const duration = 2 * 1000;
                                    const end = Date.now() + duration;

                                    (function frame() {
                                        confetti({
                                            particleCount: 4,
                                            angle: 60,
                                            spread: 55,
                                            origin: { x: 0, y: 0.65 },
                                            colors: ['#10b981', '#34d399', '#fbbf24', '#f59e0b']
                                        });
                                        confetti({
                                            particleCount: 4,
                                            angle: 120,
                                            spread: 55,
                                            origin: { x: 1, y: 0.65 },
                                            colors: ['#10b981', '#34d399', '#fbbf24', '#f59e0b']
                                        });

                                        if (Date.now() < end) {
                                            requestAnimationFrame(frame);
                                        }
                                    }());
                                } else if (successMsg.includes('top up')) {
                                    // Money / Wallet Indigo-Blue splash
                                    confetti({
                                        particleCount: 150,
                                        spread: 80,
                                        origin: { y: 0.65 },
                                        colors: ['#4f46e5', '#818cf8', '#60a5fa', '#38bdf8', '#fbbf24']
                                    });
                                } else if (successMsg.includes('pengeluaran')) {
                                    // Soft warning confetti
                                    confetti({
                                        particleCount: 75,
                                        spread: 60,
                                        origin: { y: 0.7 },
                                        colors: ['#ef4444', '#f87171', '#fca5a5', '#64748b']
                                    });
                                } else {
                                    // Multicolored splash
                                    confetti({
                                        particleCount: 100,
                                        spread: 70,
                                        origin: { y: 0.65 },
                                        colors: ['#4f46e5', '#818cf8', '#10b981', '#f59e0b', '#0ea5e9']
                                    });
                                }
                            } catch (e) {
                                console.log("Confetti burst error:", e);
                            }
                        })();
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}", });
                    </script>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        // Mouse Glow Effect
        const glow = document.getElementById('mouseGlow');
        document.addEventListener('mousemove', e => {
            glow.style.left = e.clientX + 'px';
            glow.style.top = e.clientY + 'px';
        });

        // Sidebar Control
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('mainWrapper');
            
            if (window.innerWidth > 1024) {
                sidebar.classList.toggle('collapsed');
                wrapper.classList.toggle('full');
            } else {
                sidebar.classList.toggle('active');
            }
        }

        // Auto-close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 1024 && 
                !sidebar.contains(e.target) && 
                !e.target.closest('.menu-toggle') && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Add active class handling for header tools if needed
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.top-header');
            if (window.scrollY > 20) {
                header.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.05)';
            } else {
                header.style.boxShadow = 'none';
            }
        });

        // Light theme is always active
        document.documentElement.classList.add('light-theme');
    </script>
</body>
</html>

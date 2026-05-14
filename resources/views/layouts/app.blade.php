<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Jimpitan Digital</title>
    
    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts Core -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li class="nav-item">
                    <a href="{{ route('warga.index') }}" class="nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                        <i class="fas fa-users-viewfinder"></i>
                        <span>Data Warga</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('scan.index') }}" class="nav-link {{ request()->routeIs('scan.index') ? 'active' : '' }}">
                        <i class="fas fa-qrcode"></i>
                        <span>Scan QR Code</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        <span>Riwayat Kas</span>
                    </a>
                </li>
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
                        <button class="btn-header primary">
                            <i class="fas fa-cloud-arrow-down"></i>
                            <span>Export Laporan</span>
                        </button>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-header" style="border-color: #fee2e2; color: #ef4444;">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <script>
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
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
    </script>
</body>
</html>

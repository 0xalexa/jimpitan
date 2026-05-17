<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jimpitan Digital</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 3rem;">
                <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
                    <div class="brand-icon" style="width: 64px; height: 64px; font-size: 2rem;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <h1 style="font-size: 2rem; font-weight: 900; color: #fff; letter-spacing: -1px;">Jimpitan Digital</h1>
                <p style="color: #64748b; font-size: 1rem; margin-top: 0.5rem;">Silakan masuk ke Dashboard Admin</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@jimpitan.com" required autofocus>
                    @error('email')
                        <p style="color: #f43f5e; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-header primary" style="width: 100%; justify-content: center; padding: 1rem; border-radius: 1.25rem; font-size: 1rem;">
                    Masuk Sekarang <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="font-size: 0.875rem; color: #64748b;">
                    Belum punya akun? <a href="{{ route('register') }}" style="color: #6366f1; font-weight: 600; text-decoration: none;">Daftar di sini</a>
                </p>
            </div>
            
            <div style="margin-top: 2rem; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <p style="font-size: 0.75rem; color: #64748b;">
                    Smart Village RT/Desa &copy; 2026
                </p>
            </div>
        </div>
    </div>
</body>
</html>

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
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">
                    <i class="fas fa-coins"></i>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">Jimpitan Digital</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Silakan masuk ke akun Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@jimpitan.com" required autofocus>
                    @error('email')
                        <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem;">
                    Masuk Sekarang
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="font-size: 0.875rem; color: var(--text-muted);">
                    Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Daftar di sini</a>
                </p>
            </div>
            
            <div style="margin-top: 2rem; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    Smart Village RT/Desa &copy; 2026
                </p>
            </div>
        </div>
    </div>
</body>
</html>

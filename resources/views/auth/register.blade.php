<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Jimpitan Digital</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper" style="padding: 3rem 1.5rem;">
        <div class="auth-card" style="max-width: 450px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">Daftar Akun Baru</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Buat akun Admin atau Petugas iuran</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nama Anda" required value="{{ old('name') }}">
                    @error('name')
                        <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com" required value="{{ old('email') }}">
                    @error('email')
                        <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="role">Hak Akses</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas Ronda</option>
                            <option value="sekretaris" {{ old('role') == 'sekretaris' ? 'selected' : '' }}>Sekretaris RT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">No. HP</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="0812..." value="{{ old('phone') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="number" name="rt" id="rt" class="form-control" placeholder="Contoh: 1" required value="{{ old('rt') }}">
                        @error('rt')
                            <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="number" name="rw" id="rw" class="form-control" placeholder="Contoh: 2" required value="{{ old('rw') }}">
                        @error('rw')
                            <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem; margin-top: 1rem;">
                    Buat Akun Sekarang
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="font-size: 0.875rem; color: var(--text-muted);">
                    Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

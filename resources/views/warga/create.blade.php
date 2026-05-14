@extends('layouts.app')

@section('title', 'Tambah Warga')
@section('page_title', 'Tambah Warga Baru')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Form Data Warga</h3>
    </div>
    <div style="padding: 2rem;">
        <form action="{{ route('warga.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nik">NIK (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-control" placeholder="1234567890123456" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Budi Santoso" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Jl. Merdeka No. 123"></textarea>
            </div>
            <div class="form-group">
                <label for="no_hp">No. WhatsApp</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="081234567890">
            </div>
            <div class="form-group">
                <label for="saldo">Saldo Awal (Rp)</label>
                <input type="number" name="saldo" id="saldo" class="form-control" value="0" required>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2.5rem;">
                <a href="{{ route('warga.index') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</a>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

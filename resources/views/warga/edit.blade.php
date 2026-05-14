@extends('layouts.app')

@section('title', 'Edit Warga')
@section('page_title', 'Edit Data Warga')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Edit Warga: {{ $warga->nama }}</h3>
    </div>
    <div style="padding: 2rem;">
        <form action="{{ route('warga.update', $warga->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" name="nik" id="nik" class="form-control" value="{{ $warga->nik }}" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ $warga->nama }}" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ $warga->alamat }}</textarea>
            </div>
            <div class="form-group">
                <label for="no_hp">No. WhatsApp</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ $warga->no_hp }}">
            </div>
            <div class="form-group">
                <label for="saldo">Saldo Digital (Rp)</label>
                <input type="number" name="saldo" id="saldo" class="form-control" value="{{ $warga->saldo }}" required>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2.5rem;">
                <a href="{{ route('warga.index') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</a>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

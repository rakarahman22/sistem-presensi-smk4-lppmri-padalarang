@extends('layouts.app')

@section('title', 'Edit Wali Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.wali') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-pencil-square me-2"></i>Ubah Data Wali Siswa</h3>
</div>

<!-- Notifikasi Error Validasi -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal Memperbarui Data:</div>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white; max-width: 600px;">
    <form action="{{ route('admin.wali.update', $wali->id_wali) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap Wali</label>
            <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $wali->nama_wali) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
            <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $wali->no_telp) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Username Akun</label>
            <input type="text" name="username" class="form-control" value="{{ old('username', $wali->username) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mengganti password lama" style="border-radius: 8px;">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
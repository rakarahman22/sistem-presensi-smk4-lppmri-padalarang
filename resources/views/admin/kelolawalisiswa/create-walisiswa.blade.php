@extends('layouts.app')

@section('title', 'Tambah Wali Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.wali') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-person-plus-fill me-2"></i>Tambah Wali Siswa Baru</h3>
</div>

<!-- Notifikasi Error Validasi -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal Menyimpan Data:</div>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white; max-width: 600px;">
    <form action="{{ route('admin.wali.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap Wali</label>
            <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali') }}" required placeholder="Contoh: Budi Santoso, S.Pd." style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
            <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" required placeholder="Contoh: 081234567890" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Username Login Akun</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Buat username unik untuk wali murid" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password Akun</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" style="border-radius: 8px;">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-save me-1"></i> Simpan Data Wali
            </button>
        </div>
    </form>
</div>
@endsection
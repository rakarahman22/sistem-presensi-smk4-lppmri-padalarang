@extends('layouts.app')

@section('title', 'Tambah Guru - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.guru') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-person-plus-fill me-2"></i>Tambah Guru Baru</h3>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white; max-width: 600px;">
    <form action="{{ route('admin.guru.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-semibold">NIP / Nomor Induk Pegawai</label>
            <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required placeholder="Masukkan NIP resmi guru" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap (Beserta Gelar)</label>
            <input type="text" name="nama_guru" class="form-control" value="{{ old('nama_guru') }}" required placeholder="Contoh: Eko Prasetyo, S.Kom." style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" required placeholder="Contoh: Guru Mapel/Kaprodi" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Username Akun</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Untuk login dashboard guru" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password Default</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" style="border-radius: 8px;">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-save me-1"></i> Simpan Data Guru
            </button>
        </div>
    </form>
</div>
@endsection
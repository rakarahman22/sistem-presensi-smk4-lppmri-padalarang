@extends('layouts.app')

@section('title', 'Edit Guru - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.guru') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-pencil-square me-2"></i>Ubah Data Guru</h3>
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
    <form action="{{ route('admin.guru.update', $guru->id_guru) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label fw-semibold">NIP / Nomor Induk Pegawai</label>
            <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama_guru" class="form-control" value="{{ old('nama_guru', $guru->nama_guru) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $guru->jabatan) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Username Akun</label>
            <input type="text" name="username" class="form-control" value="{{ old('username', $guru->username) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mengganti sandi lama" style="border-radius: 8px;">
        </div>

        <div class="pt-2">
            <button type="submit" clkelasass="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
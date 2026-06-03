@extends('layouts.app')

@section('title', 'Profil Saya | SMK 4 LPPM RI')

@section('content')
    {{-- Topbar --}}
    <div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:14px 28px; display:flex; align-items:center; justify-content:space-between; margin: -2.5rem -2.5rem 2rem -2.5rem;">
        <div>
            <div style="font-size:16px; font-weight:600; color:#1e293b;">Profil Saya</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:1px;">Kelola Informasi Akun Anda</div>
        </div>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
            <i class="bi bi-person-circle me-1"></i> Akun Panel
        </span>
    </div>

    <div class="row mt-4 g-4">
        {{-- Alert Notifikasi --}}
        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Mohon periksa kembali form pengisian Anda.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        {{-- Form Ubah Data Profil --}}
        <div class="col-12 col-md-6">
            <div class="bg-white p-4 rounded-3 shadow-sm border">
                <h5 class="fw-semibold mb-3 text-dark" style="font-size: 15px;"><i class="bi bi-person-fill me-2 text-success"></i>Data Pribadi</h5>
                <hr class="opacity-25 mb-4">
                
                <form action="{{ route('wali.profil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">Nama Lengkap</label>
                        <input type="text" name="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" value="{{ old('nama_wali', $wali->nama_wali) }}" required>
                        @error('nama_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $wali->no_telp) }}">
                        @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">Username Akun</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $wali->username) }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-sm w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                    </button>
                </form>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="col-12 col-md-6">
            <div class="bg-white p-4 rounded-3 shadow-sm border">
                <h5 class="fw-semibold mb-3 text-dark" style="font-size: 15px;"><i class="bi bi-shield-lock-fill me-2 text-danger"></i>Keamanan Akun</h5>
                <hr class="opacity-25 mb-4">

                <form action="{{ route('wali.profil.ganti-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" placeholder="Masukkan password saat ini" required>
                        @error('password_lama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">Password Baru Baru</label>
                        <input type="password" name="password_baru" class="form-control @error('password_baru') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                        @error('password_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary" style="font-size: 13px;">Konfirmasi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                    </div>

                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-key-fill me-1"></i> Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
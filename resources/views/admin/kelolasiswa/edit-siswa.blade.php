@extends('layouts.app')

@section('title', 'Edit Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.siswa') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-pencil-square me-2"></i>Ubah Data Siswa</h3>
</div>

@if($errors->any())
    <div class="alert alert-danger" style="border-radius: 10px;">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white; max-width: 600px;">
    <form action="{{ route('admin.siswa.update', $siswa->id_siswa) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label fw-semibold">NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}" required style="border-radius: 8px;">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama_siswa" class="form-control" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required style="border-radius: 8px;">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Kelas & Jurusan</label>
            <select name="id_kelas" class="form-select" required style="border-radius: 8px;">
                @foreach($kelas_list as $kelas)
                    <option value="{{ $kelas->id_kelas }}" {{ old('id_kelas', $siswa->id_kelas) == $kelas->id_kelas ? 'selected' : '' }}>
                        {{ $kelas->tingkat }} - {{ $kelas->jurusan }} ({{ $kelas->nama_kelas }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username', $siswa->username) }}" required style="border-radius: 8px;">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" name="password" class="form-control" style="border-radius: 8px;">
        </div>
        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
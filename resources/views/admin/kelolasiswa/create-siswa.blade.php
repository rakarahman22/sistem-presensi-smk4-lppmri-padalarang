@extends('layouts.app')

@section('title', 'Tambah Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.siswa') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-person-plus-fill me-2"></i>Tambah Siswa Baru</h3>
</div>

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
    <form action="{{ route('admin.siswa.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-semibold">NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required placeholder="Masukkan NIS siswa" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama_siswa" class="form-control" value="{{ old('nama_siswa') }}" required placeholder="Masukkan nama lengkap siswa" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Kelas & Jurusan</label>
            <select name="id_kelas" class="form-select" required style="border-radius: 8px;">
                <option value="" disabled selected>-- Pilih Kelas --</option>
                @foreach($kelas_list as $kelas)
                    <option value="{{ $kelas->id_kelas }}" {{ old('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                        {{ $kelas->tingkat }} - {{ $kelas->jurusan }} ({{ $kelas->nama_kelas }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- FIX: DROPDOWN WALI SISWA DENGAN FITUR SEARCH -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Wali Murid / Orang Tua</label>
            <select id="select-wali" name="id_wali" required placeholder="Ketik nama wali untuk mencari...">
                <option value=""></option> <!-- Wajib kosong untuk placeholder Tom Select -->
                @foreach($wali_list as $wali)
                    <option value="{{ $wali->id_wali }}" {{ old('id_wali') == $wali->id_wali ? 'selected' : '' }}>
                        {{ $wali->nama_wali }} (No. Telp: {{ $wali->no_telp }})
                    </option>
                @endforeach
            </select>
            <div class="form-text small text-muted mt-1">
                <i class="bi bi-info-circle"></i> Nama wali tidak ketemu? 
                <a href="{{ route('admin.wali.create') }}" class="text-success fw-semibold text-decoration-none">Tambah data wali baru dahulu</a>.
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Username Login</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Buat username untuk akun siswa" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" style="border-radius: 8px;">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-save me-1"></i> Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection

<!-- SCRIPT & STYLESHEET KHUSUS UNTUK AKTIFKAN SEARCHABLE SELECT -->
@push('scripts')
<!-- Masukkan link stylesheet Tom Select di dalam stack scripts/head layout utama -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Mengubah select biasa menjadi searchable dropdown otomatis
        new TomSelect("#select-wali", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            plugins: ['dropdown_input'] // Memunculkan kotak form search di dalam dropdown list
        });
    });
</script>

<style>
    /* Menyelaraskan border Tom Select agar serasi dengan input template kamu */
    .ts-wrapper.single .ts-control {
        border-radius: 8px !important;
        padding: 0.45rem 0.75rem !important;
        border: 1px solid #dee2e6 !important;
    }
    .ts-wrapper.single.focus .ts-control {
        border-color: #15803d !important;
        box-shadow: 0 0 0 0.25rem rgba(21, 128, 61, 0.25) !important;
    }
</style>
@endpush
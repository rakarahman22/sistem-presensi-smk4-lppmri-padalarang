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

        {{-- NIS --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">NIS</label>
            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
                   value="{{ old('nis') }}" required placeholder="Masukkan NIS siswa"
                   style="border-radius: 8px;">
            @error('nis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nama Lengkap --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama_siswa" class="form-control @error('nama_siswa') is-invalid @enderror"
                   value="{{ old('nama_siswa') }}" required placeholder="Masukkan nama lengkap siswa"
                   style="border-radius: 8px;">
            @error('nama_siswa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Kelas: data sedikit (puluhan), Tom Select biasa cukup --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Kelas & Jurusan</label>
            <select id="select-kelas" name="id_kelas" required placeholder="Ketik tingkat atau jurusan...">
                <option value=""></option>
                @foreach($kelas_list as $kelas)
                    <option value="{{ $kelas->id_kelas }}"
                        {{ old('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                        {{ $kelas->tingkat }} - {{ $kelas->jurusan }} ({{ $kelas->nama_kelas }})
                    </option>
                @endforeach
            </select>
            @error('id_kelas')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="form-text small text-muted mt-1">
                <i class="bi bi-info-circle"></i> Ketik tingkat (misal: <strong>10</strong>) atau jurusan (misal: <strong>TKJ</strong>) untuk memfilter.
            </div>
        </div>

        {{-- Wali: AJAX remote search — tidak load semua data --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Wali Murid / Orang Tua</label>
            <select id="select-wali" name="id_wali" required>
                {{-- Pre-populate jika validasi gagal agar pilihan tidak hilang --}}
                @if(old('id_wali') && isset($selected_wali))
                    <option value="{{ $selected_wali->id_wali }}" selected>
                        {{ $selected_wali->nama_wali }} (No. Telp: {{ $selected_wali->no_telp }})
                    </option>
                @endif
            </select>
            @error('id_wali')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="form-text small text-muted mt-1">
                <i class="bi bi-info-circle"></i> Ketik minimal <strong>2 huruf</strong> nama wali untuk mencari. Tidak ketemu?
                <a href="{{ route('admin.wali.create') }}" class="text-success fw-semibold text-decoration-none">Tambah data wali baru dahulu</a>.
            </div>
        </div>

        {{-- Username --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Username Login</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" required placeholder="Buat username untuk akun siswa"
                   style="border-radius: 8px;">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   required placeholder="Minimal 6 karakter"
                   style="border-radius: 8px;">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success"
                    style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-save me-1"></i> Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // 1. Tom Select BIASA untuk Kelas (data puluhan, aman dimuat semua)
    new TomSelect("#select-kelas", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: ['dropdown_input']
    });

    // 2. Tom Select AJAX untuk Wali (data bisa ribuan, load on-demand)
    new TomSelect("#select-wali", {
        plugins: ['dropdown_input'],
        valueField: 'id',
        labelField: 'text',
        searchField: 'text',
        preload: false,
        minChars: 2, // cukup pakai ini saja, tidak perlu shouldLoad

        load: function (query, callback) {
            if (query.length < 2) return callback();

            fetch(`{{ route('admin.wali.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => callback(data))
            .catch(() => callback());
        },

        // HAPUS render.loading — itu yang bikin "Mencari..." stuck saat dropdown dibuka
        render: {
            no_results: function () {
                return '<div class="no-results">Wali tidak ditemukan</div>';
            }
        }
    });

});
</script>

<style>
    .ts-wrapper.single .ts-control {
        border-radius: 8px !important;
        padding: 0.45rem 0.75rem !important;
        border: 1px solid #dee2e6 !important;
        min-height: unset !important;
    }
    .ts-wrapper.single.focus .ts-control {
        border-color: #15803d !important;
        box-shadow: 0 0 0 0.25rem rgba(21, 128, 61, 0.25) !important;
    }
    .ts-dropdown .option.active,
    .ts-dropdown .option:hover {
        background-color: #dcfce7 !important;
        color: #14532d !important;
    }
    .ts-dropdown .option.selected {
        background-color: #15803d !important;
        color: #fff !important;
    }
</style>
@endpush
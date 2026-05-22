@extends('layouts.app')

@section('title', 'Tambah Kelas - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kelas') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Kelas Baru</h3>
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
    <form action="{{ route('admin.kelas.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Tingkat</label>
            <select name="tingkat" class="form-select" required style="border-radius: 8px;">
                <option value="" disabled selected>-- Pilih Tingkat Kelas --</option>
                <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Kelas / Pararel</label>
            <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas') }}" required placeholder="Contoh: RPL 1 atau TKRO 2" style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Jurusan / Program Keahlian</label>
            <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak" style="border-radius: 8px;">
        </div>

        <!-- FIX: DROPDOWN WALI KELAS DENGAN FITUR SEARCH -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Wali Kelas (Guru)</label>
            <select id="select-guru" name="id_guru" placeholder="Ketik nama guru untuk mencari...">
                <option value=""></option> <!-- Wajib kosong untuk placeholder Tom Select -->
                <option value="">-- Belum Ditentukan (Bisa Menyusul) --</option>
                @foreach($guru_list as $guru)
                    <option value="{{ $guru->id_guru }}" {{ old('id_guru') == $guru->id_guru ? 'selected' : '' }}>
                        {{ $guru->nama_guru }} (NIP: {{ $guru->nip }})
                    </option>
                @endforeach
            </select>
            <div class="form-text small text-muted mt-1">
                <i class="bi bi-info-circle"></i> Guru belum terdaftar? 
                <a href="{{ route('admin.guru.create') }}" class="text-success fw-semibold text-decoration-none">Tambah data guru baru dahulu</a>.
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-save me-1"></i> Simpan Kelas
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
        new TomSelect("#select-guru", {
            create: false,
            sortField: { field: "text", direction: "asc" },
            plugins: ['dropdown_input']
        });
    });
</script>
<style>
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
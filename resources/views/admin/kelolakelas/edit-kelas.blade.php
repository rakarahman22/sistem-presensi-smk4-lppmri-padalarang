@extends('layouts.app')

@section('title', 'Edit Kelas - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kelas') }}" class="btn btn-light btn-sm text-secondary mb-2" style="border-radius: 8px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-pencil-square me-2"></i>Ubah Data Kelas</h3>
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
    <form action="{{ route('admin.kelas.update', $kelas->id_kelas) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Tingkat</label>
            <select name="tingkat" class="form-select" required style="border-radius: 8px;">
                <option value="X" {{ old('tingkat', $kelas->tingkat) == 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                <option value="XI" {{ old('tingkat', $kelas->tingkat) == 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                <option value="XII" {{ old('tingkat', $kelas->tingkat) == 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Kelas / Pararel</label>
            <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required style="border-radius: 8px;">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Jurusan / Program Keahlian</label>
            <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $kelas->jurusan) }}" required style="border-radius: 8px;">
        </div>

        <!-- FIX: DROPDOWN EDIT WALI KELAS DENGAN FITUR SEARCH -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Wali Kelas (Guru)</label>
            <select id="select-guru-edit" name="id_guru" placeholder="Ketik nama guru untuk mencari...">
                <option value=""></option>
                <option value="">-- Belum Ditentukan / Kosongkan --</option>
                @foreach($guru_list as $guru)
                    <option value="{{ $guru->id_guru }}" {{ old('id_guru', $kelas->id_guru) == $guru->id_guru ? 'selected' : '' }}>
                        {{ $guru->nama_guru }} (NIP: {{ $guru->nip }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.5rem;">
                <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
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
        new TomSelect("#select-guru-edit", {
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
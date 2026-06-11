@extends('layouts.app')

@section('title', 'Rekap Presensi Mata Pelajaran')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clipboard-data-fill text-primary me-2"></i>Rekap Presensi Mata Pelajaran
            </h4>
            <p class="text-muted small mb-0">Pilih kelas dan mata pelajaran untuk melihat akumulasi kehadiran siswa</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{{ route('guru.dashboard') }}" class="text-decoration-none text-primary">
                        <i class="bi bi-house me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted">Rekap Presensi</li>
            </ol>
        </nav>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ===== KOLOM FORM ===== --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-funnel-fill text-primary me-2"></i>Filter Data
                </h6>
                <p class="text-muted small mb-4">Pilih kombinasi kelas dan mata pelajaran</p>

                <form action="{{ route('guru.absen-mapel.rekap.tampil') }}" method="GET">

                    {{-- Pilih Kelas --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">
                            Kelas <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-building"></i>
                            </span>
                            <select name="id_kelas" id="selectKelasRekap"
                                    class="form-select border-start-0" required>
                                <option value="">— Pilih Kelas —</option>
                                @forelse($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ request('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->tingkat }} {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})
                                    </option>
                                @empty
                                    <option value="" disabled>⚠️ Belum ada kelas yang ditugaskan</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    {{-- Pilih Mata Pelajaran --}}
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">
                            Mata Pelajaran <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted" id="iconMapel">
                                <i class="bi bi-book"></i>
                            </span>
                            <select name="nama_mapel" id="selectMapelRekap"
                                    class="form-select border-start-0" required disabled>
                                <option value="">— Pilih kelas terlebih dahulu —</option>
                            </select>
                        </div>
                        <div id="loadingMapelRekap" class="form-text text-muted d-none mt-1">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Memuat daftar mata pelajaran...
                        </div>
                    </div>

                    <button type="submit" id="btnTampilkanRekap"
                            class="btn btn-primary w-100 fw-semibold py-2 rounded-3" disabled>
                        <i class="bi bi-search me-2"></i>Tampilkan Rekapitulasi
                    </button>

                </form>
            </div>
        </div>

        {{-- ===== KOLOM PANDUAN ===== --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100"
                 style="background: linear-gradient(135deg, #f8faff 0%, #eef3ff 100%); border: 1px solid #dde7ff !important;">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>Panduan Penggunaan
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="step-num">1</div>
                        <div>
                            <div class="fw-semibold small text-dark">Pilih Kelas</div>
                            <div class="text-muted small">Pilih kelas yang ingin Anda lihat rekap presensinya dari daftar kelas yang Anda ampu.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-start">
                        <div class="step-num">2</div>
                        <div>
                            <div class="fw-semibold small text-dark">Pilih Mata Pelajaran</div>
                            <div class="text-muted small">Setelah kelas dipilih, daftar mata pelajaran yang pernah Anda ajarkan di kelas tersebut akan muncul otomatis.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-start">
                        <div class="step-num">3</div>
                        <div>
                            <div class="fw-semibold small text-dark">Lihat & Cetak Rekap</div>
                            <div class="text-muted small">Rekap akan menampilkan akumulasi kehadiran seluruh siswa beserta persentase dan status kehadiran masing-masing.</div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background:#dcfce7;">
                            <div class="fw-bold text-success small">≥ 75%</div>
                            <div style="font-size:.7rem;color:#15803d;">Memenuhi</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background:#fef3c7;">
                            <div class="fw-bold text-warning small">50–74%</div>
                            <div style="font-size:.7rem;color:#b45309;">Perhatian</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background:#ffe4e6;">
                            <div class="fw-bold text-danger small">< 50%</div>
                            <div style="font-size:.7rem;color:#9f1239;">Kritis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .step-num {
        width: 26px; height: 26px; border-radius: 50%;
        background: #3b82f6; color: #fff;
        font-size: .75rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
    }
    .input-group:focus-within .input-group-text { border-color: #86b7fe; }
    .input-group:focus-within .form-select      { border-color: #86b7fe; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectKelas  = document.getElementById('selectKelasRekap');
    const selectMapel  = document.getElementById('selectMapelRekap');
    const loadingMapel = document.getElementById('loadingMapelRekap');
    const btnTampilkan = document.getElementById('btnTampilkanRekap');

    const preselectedKelas = '{{ request('id_kelas') }}';
    const preselectedMapel = '{{ request('nama_mapel') }}';

    function loadMapel(idKelas, namaMapelPilihan = '') {
        selectMapel.disabled  = true;
        btnTampilkan.disabled = true;
        loadingMapel.classList.remove('d-none');
        selectMapel.innerHTML = '<option value="">Memuat...</option>';

        fetch(`{{ route('guru.absen-mapel.get-mapel-nama') }}?id_kelas=${idKelas}`)
            .then(r => r.json())
            .then(data => {
                loadingMapel.classList.add('d-none');
                selectMapel.innerHTML = '<option value="">— Pilih Mata Pelajaran —</option>';

                if (data.length === 0) {
                    selectMapel.innerHTML =
                        '<option value="" disabled>⚠️ Belum ada data mengajar di kelas ini</option>';
                    return;
                }

                data.forEach(nama => {
                    const opt       = document.createElement('option');
                    opt.value       = nama;
                    opt.textContent = nama;
                    if (nama === namaMapelPilihan) opt.selected = true;
                    selectMapel.appendChild(opt);
                });

                selectMapel.disabled  = false;
                btnTampilkan.disabled = !selectMapel.value;
            })
            .catch(() => {
                loadingMapel.classList.add('d-none');
                selectMapel.innerHTML = '<option value="">❌ Gagal memuat mapel</option>';
            });
    }

    selectKelas.addEventListener('change', function () {
        if (!this.value) {
            selectMapel.innerHTML = '<option value="">— Pilih kelas terlebih dahulu —</option>';
            selectMapel.disabled  = true;
            btnTampilkan.disabled = true;
            return;
        }
        loadMapel(this.value);
    });

    selectMapel.addEventListener('change', function () {
        btnTampilkan.disabled = !this.value;
    });

    if (preselectedKelas) {
        selectKelas.value = preselectedKelas;
        loadMapel(preselectedKelas, preselectedMapel);
    }
});
</script>
@endpush
@endsection
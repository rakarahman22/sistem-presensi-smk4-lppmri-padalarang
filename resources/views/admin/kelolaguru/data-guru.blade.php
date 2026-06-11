@extends('layouts.app')

@section('title', 'Data Guru - SMK 4 LPPM RI Padalarang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-person-workspace me-2"></i>Data Guru
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $gurus->count() }}</strong> guru terdaftar
        </p>
    </div>
    <a href="{{ route('admin.guru.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Guru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    {{-- ── TOOLBAR ── --}}
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label fw-semibold small mb-1 text-muted">Cari Guru</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="inputCari" class="form-control bg-light border-start-0"
                       placeholder="Nama, NIP, atau username..."
                       style="border-radius:0 10px 10px 0;">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold small mb-1 text-muted">Status Wali Kelas</label>
            <select id="filterWali" class="form-select" style="border-radius:10px;">
                <option value="">Semua</option>
                <option value="mengampu">Sudah mengampu kelas</option>
                <option value="belum">Belum mengampu kelas</option>
            </select>
        </div>
        <div class="col-3 col-md-1">
            <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
            <button id="btnReset" class="btn btn-outline-secondary w-100"
                    style="border-radius:10px;" title="Reset">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="col-3 col-md-3 d-flex align-items-end justify-content-end">
            <p class="text-muted small mb-0">
                Menampilkan <strong id="jumlahTampil">{{ $gurus->count() }}</strong> guru
            </p>
        </div>
    </div>

    {{-- ── TABEL ── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tabelGuru" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th style="width:140px;">NIP</th>
                    <th>Nama Guru</th>
                    <th>Jabatan</th>
                    <th>Username</th>
                    <th>Wali Kelas</th>
                    <th class="text-center" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $i => $guru)
                    <tr class="baris-guru"
                        data-nama="{{ strtolower($guru->nama_guru) }}"
                        data-nip="{{ $guru->nip }}"
                        data-username="{{ strtolower($guru->username) }}"
                        data-wali="{{ $guru->kelasDiampu ? 'mengampu' : 'belum' }}">

                        <td class="ps-3 text-muted fw-medium nomor-urut">{{ $i + 1 }}</td>
                        <td class="text-muted fw-medium">{{ $guru->nip }}</td>
                        <td class="fw-semibold text-dark">{{ $guru->nama_guru }}</td>
                        <td class="text-muted small">{{ $guru->jabatan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1"
                                  style="border-radius:6px;">
                                {{ $guru->username }}
                            </span>
                        </td>
                        <td>
                            @if($guru->kelasDiampu)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $guru->kelasDiampu->tingkat }} {{ $guru->kelasDiampu->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Belum mengampu</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.guru.edit', $guru->id_guru) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.guru.destroy', $guru->id_guru) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus data {{ addslashes($guru->nama_guru) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-person-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data guru terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div id="emptyFilter" class="text-center text-muted py-5 d-none">
            <i class="bi bi-search display-6 d-block mb-2 text-secondary"></i>
            <p class="mb-0">Tidak ada guru yang cocok.</p>
            <button class="btn btn-sm btn-outline-secondary mt-2" id="btnResetEmpty"
                    style="border-radius:8px;">Reset Filter</button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f8fafc; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const inputCari     = document.getElementById('inputCari');
    const filterWali    = document.getElementById('filterWali');
    const btnReset      = document.getElementById('btnReset');
    const btnResetEmpty = document.getElementById('btnResetEmpty');
    const jumlahTampil  = document.getElementById('jumlahTampil');
    const emptyFilter   = document.getElementById('emptyFilter');
    const tbody         = document.querySelector('#tabelGuru tbody');

    function filter() {
        const cari  = inputCari.value.toLowerCase().trim();
        const wali  = filterWali.value;
        const baris = tbody.querySelectorAll('tr.baris-guru');
        let tampil  = 0;

        baris.forEach(tr => {
            const cocokCari = !cari || tr.dataset.nama.includes(cari)
                                    || tr.dataset.nip.includes(cari)
                                    || tr.dataset.username.includes(cari);
            const cocokWali = !wali || tr.dataset.wali === wali;
            const tampilkan = cocokCari && cocokWali;

            tr.style.display = tampilkan ? '' : 'none';
            if (tampilkan) {
                tampil++;
                tr.querySelector('.nomor-urut').textContent = tampil;
            }
        });

        jumlahTampil.textContent = tampil;
        emptyFilter.classList.toggle('d-none', baris.length === 0 || tampil > 0);
    }

    function reset() {
        inputCari.value  = '';
        filterWali.value = '';
        filter();
    }
    inputCari.addEventListener('input',     filter);
    filterWali.addEventListener('change',   filter);
    btnReset.addEventListener('click',      reset);
    btnResetEmpty.addEventListener('click', reset);
})();
</script>
@endpush

@endsection
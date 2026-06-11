@extends('layouts.app')

@section('title', 'Data Kelas - SMK 4 LPPM RI Padalarang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-collection-fill me-2"></i>Data Kelas
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $kelases->count() }}</strong> kelas terdaftar
        </p>
    </div>
    <a href="{{ route('admin.kelas.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Kelas
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
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold small mb-1 text-muted">Filter Tingkat</label>
            <select id="filterTingkat" class="form-select" style="border-radius:10px;">
                <option value="">Semua Tingkat</option>
                @foreach($kelases->pluck('tingkat')->unique()->sort() as $t)
                    <option value="{{ strtolower($t) }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label fw-semibold small mb-1 text-muted">Filter Jurusan</label>
            <select id="filterJurusan" class="form-select" style="border-radius:10px;">
                <option value="">Semua Jurusan</option>
                @foreach($kelases->pluck('jurusan')->unique()->sort() as $j)
                    <option value="{{ strtolower($j) }}">{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4 col-md-2">
            <label class="form-label fw-semibold small mb-1 text-muted">Wali Kelas</label>
            <select id="filterWali" class="form-select" style="border-radius:10px;">
                <option value="">Semua</option>
                <option value="ada">Sudah ada</option>
                <option value="belum">Belum ada</option>
            </select>
        </div>
        <div class="col-4 col-md-1">
            <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
            <button id="btnReset" class="btn btn-outline-secondary w-100"
                    style="border-radius:10px;" title="Reset">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="col-4 col-md-2 d-flex align-items-end justify-content-end">
            <p class="text-muted small mb-0">
                <strong id="jumlahTampil">{{ $kelases->count() }}</strong> kelas
            </p>
        </div>
    </div>

    {{-- ── TABEL ── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tabelKelas" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th>Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th class="text-center" style="width:120px;">Jumlah Siswa</th>
                    <th class="text-center" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $i => $kelas)
                    @php $jumlahSiswa = $kelas->siswa->count(); @endphp
                    <tr class="baris-kelas"
                        data-tingkat="{{ strtolower($kelas->tingkat) }}"
                        data-jurusan="{{ strtolower($kelas->jurusan) }}"
                        data-wali="{{ $kelas->waliKelas ? 'ada' : 'belum' }}">

                        <td class="ps-3 text-muted fw-medium nomor-urut">{{ $i + 1 }}</td>

                        <td>
                            <span class="badge bg-success px-2 py-1"
                                  style="border-radius:6px; background:#15803d !important; font-weight:600;">
                                {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                            </span>
                        </td>

                        <td class="text-muted fw-medium">{{ $kelas->tingkat }}</td>
                        <td class="fw-semibold text-dark">{{ $kelas->jurusan }}</td>

                        <td>
                            @if($kelas->waliKelas)
                                <span class="text-dark fw-medium small">
                                    <i class="bi bi-person-badge text-success me-1"></i>
                                    {{ $kelas->waliKelas->nama_guru }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">
                                    <i class="bi bi-dash-circle me-1"></i>Belum ditentukan
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="badge {{ $jumlahSiswa > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' }}"
                                  style="border-radius:6px; font-weight:600; min-width:60px;">
                                {{ $jumlahSiswa }} siswa
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Lihat Siswa — sekarang filter by kelas --}}
                                <a href="{{ route('admin.siswa') }}?id_kelas={{ $kelas->id_kelas }}"
                                   class="btn btn-sm btn-light text-success"
                                   style="border-radius:8px;" title="Lihat Siswa Kelas Ini">
                                    <i class="bi bi-people-fill"></i>
                                </a>
                                <a href="{{ route('admin.kelas.edit', $kelas->id_kelas) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit Kelas">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $kelas->id_kelas) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus kelas {{ addslashes($kelas->tingkat . ' ' . $kelas->nama_kelas) }}?\nSemua siswa di kelas ini akan kehilangan kelasnya.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus Kelas">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-folder-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data kelas terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div id="emptyFilter" class="text-center text-muted py-5 d-none">
            <i class="bi bi-search display-6 d-block mb-2 text-secondary"></i>
            <p class="mb-0">Tidak ada kelas yang cocok dengan filter.</p>
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
    const filterTingkat = document.getElementById('filterTingkat');
    const filterJurusan = document.getElementById('filterJurusan');
    const filterWali    = document.getElementById('filterWali');
    const btnReset      = document.getElementById('btnReset');
    const btnResetEmpty = document.getElementById('btnResetEmpty');
    const jumlahTampil  = document.getElementById('jumlahTampil');
    const emptyFilter   = document.getElementById('emptyFilter');
    const tbody         = document.querySelector('#tabelKelas tbody');

    function filter() {
        const tingkat = filterTingkat.value;
        const jurusan = filterJurusan.value;
        const wali    = filterWali.value;
        const baris   = tbody.querySelectorAll('tr.baris-kelas');
        let tampil    = 0;

        baris.forEach(tr => {
            const ok = (!tingkat || tr.dataset.tingkat === tingkat)
                    && (!jurusan || tr.dataset.jurusan === jurusan)
                    && (!wali    || tr.dataset.wali    === wali);

            tr.style.display = ok ? '' : 'none';
            if (ok) {
                tampil++;
                tr.querySelector('.nomor-urut').textContent = tampil;
            }
        });

        jumlahTampil.textContent = tampil;
        emptyFilter.classList.toggle('d-none', baris.length === 0 || tampil > 0);
    }

    function reset() {
        filterTingkat.value = '';
        filterJurusan.value = '';
        filterWali.value    = '';
        filter();
    }

    filterTingkat.addEventListener('change', filter);
    filterJurusan.addEventListener('change', filter);
    filterWali.addEventListener('change',    filter);
    btnReset.addEventListener('click',       reset);
    btnResetEmpty.addEventListener('click',  reset);
})();
</script>
@endpush

@endsection
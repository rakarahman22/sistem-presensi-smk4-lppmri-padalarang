@extends('layouts.app')

@section('title', 'Data Siswa - SMK 4 LPPM RI Padalarang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-mortarboard-fill me-2"></i>Data Siswa
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $siswas->count() }}</strong> siswa terdaftar
        </p>
    </div>
    <a href="{{ route('admin.siswa.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Siswa
    </a>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    {{-- ── TOOLBAR FILTER ── --}}
    <div class="row g-2 mb-3 align-items-end">

        {{-- Search --}}
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small mb-1 text-muted">Cari Siswa</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="inputCari"
                       class="form-control bg-light border-start-0"
                       placeholder="NIS atau nama siswa..."
                       style="border-radius:0 10px 10px 0;">
            </div>
        </div>

        {{-- Filter Tingkat --}}
        <div class="col-6 col-md-2">
            <label class="form-label fw-semibold small mb-1 text-muted">Tingkat</label>
            <select id="filterTingkat" class="form-select" style="border-radius:10px;">
                <option value="">Semua</option>
                @foreach($kelas_list->pluck('tingkat')->unique()->sort() as $tingkat)
                    <option value="{{ $tingkat }}">{{ $tingkat }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Jurusan --}}
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold small mb-1 text-muted">Jurusan</label>
            <select id="filterJurusan" class="form-select" style="border-radius:10px;">
                <option value="">Semua Jurusan</option>
                @foreach($kelas_list->pluck('jurusan')->unique()->sort() as $jurusan)
                    <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Kelas --}}
        <div class="col-8 col-md-2">
            <label class="form-label fw-semibold small mb-1 text-muted">Kelas</label>
            <select id="filterKelas" class="form-select" style="border-radius:10px;">
                <option value="">Semua Kelas</option>
                @foreach($kelas_list as $k)
                    <option value="{{ $k->id_kelas }}">
                        {{ $k->tingkat }} {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Reset --}}
        <div class="col-4 col-md-1">
            <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
            <button id="btnReset" class="btn btn-outline-secondary w-100 fw-semibold"
                    style="border-radius:10px;" title="Reset filter">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

    </div>

    {{-- Info hasil filter --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong id="jumlahTampil">{{ $siswas->count() }}</strong> siswa
        </p>
    </div>

    {{-- ── TABEL ── --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tabelSiswa" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th style="width:120px;">NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Siswa</th>
                    <th class="text-center" style="width:110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $i => $siswa)
                    <tr class="baris-siswa"
                        data-nama="{{ strtolower($siswa->nama_siswa) }}"
                        data-nis="{{ $siswa->nis }}"
                        data-kelas-id="{{ $siswa->id_kelas }}"
                        data-tingkat="{{ strtolower($siswa->kelas->tingkat ?? '') }}"
                        data-jurusan="{{ strtolower($siswa->kelas->jurusan ?? '') }}">

                        <td class="ps-3 text-muted fw-medium nomor-urut">{{ $i + 1 }}</td>
                        <td class="text-muted fw-medium">{{ $siswa->nis }}</td>

                        <td class="fw-semibold text-dark">{{ $siswa->nama_siswa }}</td>

                        <td>
                            @if($siswa->kelas)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Belum set</span>
                            @endif
                        </td>

                        <td class="text-muted small">{{ $siswa->kelas->jurusan ?? '-' }}</td>

                        <td class="text-muted small">{{ $siswa->wali->nama_wali ?? '-' }}</td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $siswa->id_siswa) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus data {{ addslashes($siswa->nama_siswa) }}?')">
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
                            Belum ada data siswa terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Empty state saat filter tidak ada hasil --}}
        <div id="emptyFilter" class="text-center text-muted py-5 d-none">
            <i class="bi bi-search display-6 d-block mb-2 text-secondary"></i>
            <p class="mb-0">Tidak ada siswa yang cocok dengan filter.</p>
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
    const inputCari      = document.getElementById('inputCari');
    const filterTingkat  = document.getElementById('filterTingkat');
    const filterJurusan  = document.getElementById('filterJurusan');
    const filterKelas    = document.getElementById('filterKelas');
    const btnReset       = document.getElementById('btnReset');
    const btnResetEmpty  = document.getElementById('btnResetEmpty');
    const jumlahTampil   = document.getElementById('jumlahTampil');
    const emptyFilter    = document.getElementById('emptyFilter');
    const tbody          = document.querySelector('#tabelSiswa tbody');

    function filterTabel() {
        const cari    = inputCari.value.toLowerCase().trim();
        const tingkat = filterTingkat.value.toLowerCase();
        const jurusan = filterJurusan.value.toLowerCase();
        const kelasId = filterKelas.value;

        const baris   = tbody.querySelectorAll('tr.baris-siswa');
        let tampil    = 0;

        baris.forEach((tr, idx) => {
            const cocokCari    = !cari    || tr.dataset.nama.includes(cari) || tr.dataset.nis.includes(cari);
            const cocokTingkat = !tingkat || tr.dataset.tingkat === tingkat;
            const cocokJurusan = !jurusan || tr.dataset.jurusan === jurusan;
            const cocokKelas   = !kelasId || tr.dataset.kelasId === kelasId;

            const tampilkan = cocokCari && cocokTingkat && cocokJurusan && cocokKelas;
            tr.style.display = tampilkan ? '' : 'none';

            if (tampilkan) {
                tampil++;
                // Update nomor urut
                tr.querySelector('.nomor-urut').textContent = tampil;
            }
        });

        jumlahTampil.textContent = tampil;

        const adaData = tbody.querySelector('tr.baris-siswa') !== null;
        emptyFilter.classList.toggle('d-none', !adaData || tampil > 0);
    }

    function resetFilter() {
        inputCari.value      = '';
        filterTingkat.value  = '';
        filterJurusan.value  = '';
        filterKelas.value    = '';
        filterTabel();
    }

    // Sinkron filter tingkat → reset kelas jika tidak relevan
    filterTingkat.addEventListener('change', function () {
        // Reset kelas ketika tingkat diganti
        filterKelas.value = '';
        filterTabel();
    });

    inputCari.addEventListener('input',      filterTabel);
    filterJurusan.addEventListener('change', filterTabel);
    filterKelas.addEventListener('change',   filterTabel);
    btnReset.addEventListener('click',       resetFilter);
    btnResetEmpty.addEventListener('click',  resetFilter);

    // Auto-filter dari query string ?id_kelas= (redirect dari halaman data kelas)
    const params = new URLSearchParams(window.location.search);
    const idKelasParam = params.get('id_kelas');
    if (idKelasParam) {
        filterKelas.value = idKelasParam;
        filterTabel();
        document.getElementById('tabelSiswa').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
})();
</script>
@endpush

@endsection
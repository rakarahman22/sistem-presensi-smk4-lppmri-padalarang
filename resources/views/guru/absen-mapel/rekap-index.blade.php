@extends('layouts.app')

@section('title', 'Filter Rekap Presensi Mapel')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-calendar-check-fill text-success me-2"></i>
                    Rekap Absensi Kelas (Per Mapel)
                </h5>
                <p class="text-muted small mb-3">
                    Pilih kelas, lalu pilih mata pelajaran untuk melihat akumulasi kehadiran siswa.
                </p>
                <hr>

                @if(session('error'))
                    <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif

                <form action="{{ route('guru.absen-mapel.rekap.tampil') }}" method="GET">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Kelas</label>
                        <select name="id_kelas"
                                id="selectKelasRekap"
                                class="form-select"
                                required>
                            <option value="">-- Pilih Kelas Anda --</option>
                            @forelse($daftarKelas as $kelas)
                                <option value="{{ $kelas->id_kelas }}"
                                    {{ request('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }} {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})
                                </option>
                            @empty
                                <option value="" disabled>
                                    ⚠️ Anda belum ditugaskan di kelas mana pun
                                </option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Mata Pelajaran</label>
                        <select name="nama_mapel"
                                id="selectMapelRekap"
                                class="form-select"
                                required
                                disabled>
                            <option value="">-- Pilih kelas terlebih dahulu --</option>
                            {{-- Opsi ini diisi ulang via AJAX, namun jika ada nilai dari query string
                                 (setelah redirect balik dari rekap-tampil), render ulang via JS --}}
                        </select>
                        <div id="loadingMapelRekap"
                             class="form-text text-muted d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Memuat daftar mata pelajaran...
                        </div>
                    </div>

                    <button type="submit"
                            id="btnTampilkanRekap"
                            class="btn btn-primary w-100 fw-bold py-2"
                            style="border-radius: 8px; background-color: #1e40af; border: none;"
                            disabled>
                        <i class="bi bi-search me-1"></i> Tampilkan Rekapitulasi
                    </button>
                </form>
            </div>

            {{-- Info box panduan --}}
            <div class="card border-0 mt-3 p-3"
                 style="border-radius: 12px; background: #f0f9ff;
                        border: 1px solid #bae6fd !important;">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-info-circle-fill text-info mt-1"></i>
                    <div style="font-size: 0.85rem; color: #0369a1;">
                        Rekap dihitung berdasarkan seluruh pertemuan yang pernah Anda buka
                        untuk kombinasi kelas dan mata pelajaran yang dipilih.
                        Persentase ≥ 75% dianggap lulus kehadiran.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectKelas   = document.getElementById('selectKelasRekap');
    const selectMapel   = document.getElementById('selectMapelRekap');
    const loadingMapel  = document.getElementById('loadingMapelRekap');
    const btnTampilkan  = document.getElementById('btnTampilkanRekap');

    // Nilai dari query string (jika user kembali dari halaman rekap-tampil)
    const preselectedKelas  = '{{ request('id_kelas') }}';
    const preselectedMapel  = '{{ request('nama_mapel') }}';

    function loadMapel(idKelas, namaMapelPilihan = '') {
        selectMapel.disabled = true;
        btnTampilkan.disabled = true;
        loadingMapel.classList.remove('d-none');
        selectMapel.innerHTML = '<option value="">Memuat...</option>';

        fetch(`{{ route('guru.absen-mapel.get-mapel-nama') }}?id_kelas=${idKelas}`)
            .then(r => r.json())
            .then(data => {
                loadingMapel.classList.add('d-none');
                selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';

                if (data.length === 0) {
                    selectMapel.innerHTML =
                        '<option value="" disabled>⚠️ Belum ada data mengajar di kelas ini</option>';
                    return;
                }

                data.forEach(nama => {
                    const opt      = document.createElement('option');
                    opt.value      = nama;
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

    // Event: kelas berubah
    selectKelas.addEventListener('change', function () {
        if (!this.value) {
            selectMapel.innerHTML = '<option value="">-- Pilih kelas terlebih dahulu --</option>';
            selectMapel.disabled  = true;
            btnTampilkan.disabled = true;
            return;
        }
        loadMapel(this.value);
    });

    // Event: mapel berubah
    selectMapel.addEventListener('change', function () {
        btnTampilkan.disabled = !this.value;
    });

    // Autoload jika ada preselection dari query string
    if (preselectedKelas) {
        selectKelas.value = preselectedKelas;
        loadMapel(preselectedKelas, preselectedMapel);
    }
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Mulai Absen Sesi Mapel')

@section('content')
<div class="container py-4">
    <div class="row g-4">

        {{-- ================================================================ --}}
        {{-- KOLOM KIRI: Form Buka Sesi Mengajar --}}
        {{-- ================================================================ --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px; position: relative;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="fw-bold text-success m-0">
                        <i class="bi bi-journal-plus me-2"></i>Buka Absen Kelas
                    </h5>
                    <span id="badgePertemuan"
                          class="badge bg-primary px-2 py-1"
                          style="border-radius: 6px; font-size: 0.8rem; display: none;">
                        Pertemuan Ke-?
                    </span>
                </div>
                <hr>

                {{-- Flash messages --}}
                @if(session('error'))
                    <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif

                {{-- Warning duplikat sesi --}}
                <div id="duplikatWarning"
                     class="alert alert-warning py-2 d-none"
                     style="font-size: 0.875rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Anda <strong>sudah membuka sesi</strong> untuk mapel ini hari ini.
                    Lanjutkan hanya jika memang perlu membuka sesi tambahan.
                </div>

                <form id="formBukaSesi"
                      action="{{ route('guru.absen-mapel.buka-sesi') }}"
                      method="POST"
                      onsubmit="return handleSubmit(event)">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Kelas Bimbingan</label>
                        <select name="id_kelas" id="selectKelas" class="form-select" required>
                            <option value="">-- Pilih Kelas Anda --</option>
                            @forelse($daftarKelas as $kelas)
                                <option value="{{ $kelas->id_kelas }}">
                                    {{ $kelas->tingkat }} {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})
                                </option>
                            @empty
                                <option value="" disabled>
                                    ⚠️ Anda belum di-plotting ke kelas mana pun oleh Admin
                                </option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Mata Pelajaran Anda</label>
                        <div class="input-group">
                            <select name="id_mapel" id="selectMapel" class="form-select" required disabled>
                                <option value="">-- Pilih kelas terlebih dahulu --</option>
                            </select>
                            <button class="btn btn-outline-success"
                                    type="button"
                                    id="btnBukaModalManual"
                                    title="Tambah Mapel Baru">
                                <i class="bi bi-plus-circle-fill"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            id="btnMulaiAbsen"
                            class="btn btn-success w-100 fw-bold py-2"
                            style="background-color: #15803d; border-radius: 8px;"
                            disabled>
                        <i class="bi bi-play-fill me-1"></i> Mulai Absen Kelas
                    </button>
                </form>

                {{-- ── Overlay Konfirmasi ── --}}
                <div id="confirmOverlay"
                     style="display:none; position:absolute; inset:0; z-index:10;
                            background:rgba(0,0,0,0.45); border-radius:15px;
                            align-items:center; justify-content:center;">
                    <div class="bg-white p-4 mx-3"
                         style="border-radius: 12px; max-width: 320px; width: 100%;
                                border: 0.5px solid #dee2e6;">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-journal-check text-success me-1"></i>
                            Konfirmasi Buka Sesi
                        </h6>
                        <p id="confirmText"
                           class="text-muted mb-3"
                           style="font-size: 0.875rem; line-height: 1.6;"></p>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-light flex-fill"
                                    onclick="tutupKonfirmasi()">
                                Batal
                            </button>
                            <button type="button"
                                    class="btn btn-success flex-fill fw-bold"
                                    style="background-color: #15803d;"
                                    onclick="submitForm()">
                                <i class="bi bi-check-lg me-1"></i> Ya, Mulai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- KOLOM KANAN: Riwayat Sesi (Tab Hari Ini & Pertemuan Lalu) --}}
        {{-- ================================================================ --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-secondary mb-3">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Sesi Mengajar
                </h5>
                <hr>

                {{-- Tab navigasi --}}
                <ul class="nav nav-pills mb-3" id="tabRiwayat" role="tablist"
                    style="gap: 6px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-1"
                                id="tab-hari-ini"
                                data-bs-toggle="pill"
                                data-bs-target="#panel-hari-ini"
                                type="button"
                                style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="bi bi-calendar-day me-1"></i>Hari Ini
                            @if($riwayatHariIni->count() > 0)
                                <span class="badge bg-success ms-1"
                                      style="font-size: 0.7rem;">
                                    {{ $riwayatHariIni->count() }}
                                </span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-1"
                                id="tab-lalu"
                                data-bs-toggle="pill"
                                data-bs-target="#panel-lalu"
                                type="button"
                                style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="bi bi-calendar3 me-1"></i>Pertemuan Lalu
                            @if($riwayatLalu->count() > 0)
                                <span class="badge bg-secondary ms-1"
                                      style="font-size: 0.7rem;">
                                    {{ $riwayatLalu->count() }}
                                </span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="tabRiwayatContent">

                    {{-- ── Panel: Hari Ini ── --}}
                    <div class="tab-pane fade show active" id="panel-hari-ini" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="font-size: 0.8rem;">Jam</th>
                                        <th style="font-size: 0.8rem;">Kelas</th>
                                        <th style="font-size: 0.8rem;">Mata Pelajaran</th>
                                        <th style="font-size: 0.8rem;" class="text-center">Ke-</th>
                                        <th style="font-size: 0.8rem;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatHariIni as $riwayat)
                                        <tr>
                                            <td class="fw-bold text-primary" style="font-size: 0.85rem;">
                                                {{ \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') }} WIB
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success fw-semibold">
                                                    {{ $riwayat->kelas->tingkat }} {{ $riwayat->kelas->nama_kelas }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-dark" style="font-size: 0.875rem;">
                                                {{ $riwayat->nama_mapel }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"
                                                      style="border-radius: 6px; font-weight: 600;">
                                                    {{ $riwayat->pertemuan_ke }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('guru.absen-mapel.isi', $riwayat->id_mengajar) }}"
                                                   class="btn btn-sm btn-outline-primary px-3"
                                                   style="border-radius: 6px; font-size: 0.8rem;">
                                                    <i class="bi bi-pencil-square me-1"></i>Ubah Absen
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-calendar-x d-block fs-2 mb-2 text-secondary"></i>
                                                Belum ada sesi mengajar yang dibuka hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Panel: Pertemuan Lalu ── --}}
                    <div class="tab-pane fade" id="panel-lalu" role="tabpanel">

                        {{-- Filter cepat kelas & mapel --}}
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <select id="filterKelasLalu" class="form-select form-select-sm"
                                        onchange="filterRiwayatLalu()">
                                    <option value="">Semua Kelas</option>
                                    @foreach($riwayatLalu->pluck('kelas')->unique('id_kelas')->filter() as $k)
                                        <option value="{{ $k->id_kelas }}">
                                            {{ $k->tingkat }} {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filterMapelLalu" class="form-select form-select-sm"
                                        onchange="filterRiwayatLalu()">
                                    <option value="">Semua Mapel</option>
                                    @foreach($riwayatLalu->pluck('nama_mapel')->unique() as $nm)
                                        <option value="{{ $nm }}">{{ $nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tabelRiwayatLalu">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="font-size: 0.8rem;">Tanggal</th>
                                        <th style="font-size: 0.8rem;">Kelas</th>
                                        <th style="font-size: 0.8rem;">Mata Pelajaran</th>
                                        <th style="font-size: 0.8rem;" class="text-center">Ke-</th>
                                        <th style="font-size: 0.8rem;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyRiwayatLalu">
                                    @forelse($riwayatLalu as $riwayat)
                                        <tr data-kelas="{{ $riwayat->id_kelas }}"
                                            data-mapel="{{ $riwayat->nama_mapel }}">
                                            <td style="font-size: 0.8rem; color: #6c757d;">
                                                {{ \Carbon\Carbon::parse($riwayat->tgl_mengajar)->translatedFormat('d M Y') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                                    {{ $riwayat->kelas->tingkat }} {{ $riwayat->kelas->nama_kelas }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-dark" style="font-size: 0.875rem;">
                                                {{ $riwayat->nama_mapel }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"
                                                      style="border-radius: 6px; font-weight: 600;">
                                                    {{ $riwayat->pertemuan_ke }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('guru.absen-mapel.isi', $riwayat->id_mengajar) }}"
                                                   class="btn btn-sm btn-outline-secondary px-3"
                                                   style="border-radius: 6px; font-size: 0.8rem;">
                                                    <i class="bi bi-eye me-1"></i>Lihat Absen
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyLalu">
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                                                Belum ada riwayat pertemuan sebelumnya.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>
        </div>

    </div>
</div>

{{-- ================================================================ --}}
{{-- Modal Tambah Mata Pelajaran --}}
{{-- ================================================================ --}}
<div id="modalTambahMapel"
     style="display:none; position:fixed; inset:0; z-index:1055;
            background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background: #fff; border-radius: 12px; border: none;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                width: 100%; max-width: 420px; margin: 1rem;">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-success m-0">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Master Mata Pelajaran
                </h6>
                <button type="button"
                        class="btn-close"
                        onclick="tutupModal()"></button>
            </div>

            <div id="errorAlertAjax"
                 class="alert alert-danger d-none py-2"
                 style="font-size: 0.875rem;"></div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 0.875rem;">
                    Nama Mata Pelajaran
                </label>
                <input type="text"
                       id="inputNamaMapelBaru"
                       class="form-control"
                       placeholder="Contoh: Pemrograman Web"
                       autocomplete="off">
            </div>

            <button type="button"
                    id="btnSimpanMapelAjax"
                    class="btn btn-success w-100 fw-bold py-2"
                    style="background-color: #15803d; border-radius: 8px;"
                    onclick="simpanMapelBaru()">
                <span id="spinnerSimpan"
                      class="spinner-border spinner-border-sm d-none me-1"
                      role="status"
                      aria-hidden="true"></span>
                Simpan &amp; Daftarkan Pilihan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Elemen utama
    const selectKelas    = document.getElementById('selectKelas');
    const selectMapel    = document.getElementById('selectMapel');
    const badgePertemuan = document.getElementById('badgePertemuan');
    const btnMulai       = document.getElementById('btnMulaiAbsen');
    const duplikatWarn   = document.getElementById('duplikatWarning');
    const confirmOverlay = document.getElementById('confirmOverlay');

    // ── 1. Saat kelas berubah → load mapel via AJAX
    selectKelas.addEventListener('change', function () {
        const idKelas = this.value;

        // Reset state
        selectMapel.innerHTML = '<option value="">⏳ Memuat mata pelajaran...</option>';
        selectMapel.disabled  = true;
        badgePertemuan.style.display = 'none';
        btnMulai.disabled     = true;
        duplikatWarn.classList.add('d-none');

        if (!idKelas) {
            selectMapel.innerHTML = '<option value="">-- Pilih kelas terlebih dahulu --</option>';
            return;
        }

        fetch(`{{ route('guru.absen-mapel.get-mapel') }}?id_kelas=${idKelas}`)
            .then(r => r.json())
            .then(data => {
                selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';

                if (data.length === 0) {
                    selectMapel.innerHTML =
                        '<option value="" disabled>⚠️ Tidak ada mapel yang di-plot untuk kelas ini</option>';
                    return;
                }

                data.forEach(mapel => {
                    const opt  = document.createElement('option');
                    opt.value  = mapel.id_mapel;
                    opt.text   = `${mapel.nama_mapel} [${mapel.jurusan}]`;
                    selectMapel.add(opt);
                });

                selectMapel.disabled = false;
            })
            .catch(() => {
                selectMapel.innerHTML = '<option value="">❌ Gagal memuat mata pelajaran</option>';
            });
    });

    // ── 2. Saat mapel berubah → cek pertemuan & duplikat
    selectMapel.addEventListener('change', function () {
        const idKelas = selectKelas.value;
        const idMapel = this.value;

        badgePertemuan.style.display = 'none';
        btnMulai.disabled = true;
        duplikatWarn.classList.add('d-none');

        if (!idKelas || !idMapel) return;

        // Ambil nama mapel bersih (tanpa " [jurusan]")
        const namaMapelBersih = this.options[this.selectedIndex].text.split(' [')[0];

        // Cek pertemuan ke-berapa
        fetch(`{{ route('guru.absen-mapel.cek-pertemuan') }}?id_kelas=${idKelas}&nama_mapel=${encodeURIComponent(namaMapelBersih)}`)
            .then(r => r.json())
            .then(data => {
                badgePertemuan.textContent   = `Pertemuan Ke-${data.pertemuan_ke}`;
                badgePertemuan.style.display = '';
            })
            .catch(() => {});

        // Cek duplikat sesi hari ini
        fetch(`{{ route('guru.absen-mapel.cek-duplikat') }}?id_kelas=${idKelas}&id_mapel=${idMapel}`)
            .then(r => r.json())
            .then(data => {
                if (data.duplikat) {
                    duplikatWarn.classList.remove('d-none');
                }
            })
            .catch(() => {});

        btnMulai.disabled = false;
    });

    // ── 3. Submit form dengan konfirmasi
    window.handleSubmit = function (e) {
        e.preventDefault();

        const namaKelas = selectKelas.options[selectKelas.selectedIndex]?.text || '-';
        const namaMapel = selectMapel.options[selectMapel.selectedIndex]?.text.split(' [')[0] || '-';
        const pertemuan = badgePertemuan.textContent || '';

        document.getElementById('confirmText').innerHTML =
            `Anda akan membuka sesi <strong>${namaMapel}</strong> untuk kelas ` +
            `<strong>${namaKelas}</strong>. Ini akan menjadi <strong>${pertemuan.toLowerCase()}</strong>.`;

        confirmOverlay.style.display = 'flex';
        return false;
    };

    window.tutupKonfirmasi = function () {
        confirmOverlay.style.display = 'none';
    };

    window.submitForm = function () {
        confirmOverlay.style.display = 'none';
        document.getElementById('formBukaSesi').submit();
    };

    // ── 4. Modal tambah mapel
    document.getElementById('btnBukaModalManual').addEventListener('click', function () {
        const modal = document.getElementById('modalTambahMapel');
        modal.style.display = 'flex';
        document.getElementById('inputNamaMapelBaru').focus();
    });

    window.tutupModal = function () {
        document.getElementById('modalTambahMapel').style.display = 'none';
        document.getElementById('errorAlertAjax').classList.add('d-none');
        document.getElementById('inputNamaMapelBaru').value = '';
    };

    // Tutup modal jika klik di luar box
    document.getElementById('modalTambahMapel').addEventListener('click', function (e) {
        if (e.target === this) tutupModal();
    });

    // ── 5. Simpan mapel baru via AJAX
    window.simpanMapelBaru = function () {
        const input         = document.getElementById('inputNamaMapelBaru');
        const errorAlert    = document.getElementById('errorAlertAjax');
        const spinner       = document.getElementById('spinnerSimpan');
        const btnSimpan     = document.getElementById('btnSimpanMapelAjax');
        const namaMapelVal  = input.value.trim();

        if (!namaMapelVal) {
            errorAlert.textContent = 'Nama mata pelajaran tidak boleh kosong!';
            errorAlert.classList.remove('d-none');
            return;
        }

        btnSimpan.disabled = true;
        spinner.classList.remove('d-none');
        errorAlert.classList.add('d-none');

        fetch(`{{ route('guru.absen-mapel.tambah-mapel-ajax') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama_mapel: namaMapelVal })
        })
        .then(r => r.json())
        .then(data => {
            btnSimpan.disabled = false;
            spinner.classList.add('d-none');

            if (!data.success) {
                errorAlert.textContent = data.message || 'Gagal menyimpan mata pelajaran.';
                errorAlert.classList.remove('d-none');
                return;
            }

            // Tambahkan ke dropdown & pilih otomatis
            const opt      = document.createElement('option');
            opt.value      = data.id_mapel;
            opt.text       = data.nama_mapel;
            opt.selected   = true;
            selectMapel.add(opt);
            selectMapel.disabled = false;

            tutupModal();

            // Trigger change agar badge pertemuan & duplikat dihitung
            selectMapel.dispatchEvent(new Event('change'));
        })
        .catch(() => {
            btnSimpan.disabled = false;
            spinner.classList.add('d-none');
            errorAlert.textContent = 'Terjadi kesalahan sistem atau mata pelajaran sudah ada.';
            errorAlert.classList.remove('d-none');
        });
    };

    // ── 6. Enter di input mapel baru → simpan
    document.getElementById('inputNamaMapelBaru').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') simpanMapelBaru();
    });

});

// ── 7. Filter riwayat pertemuan lalu (client-side)
function filterRiwayatLalu() {
    const filterKelas  = document.getElementById('filterKelasLalu').value;
    const filterMapel  = document.getElementById('filterMapelLalu').value;
    const rows         = document.querySelectorAll('#bodyRiwayatLalu tr[data-kelas]');
    let   tampil       = 0;

    rows.forEach(function (row) {
        const cocokKelas = !filterKelas || row.dataset.kelas === filterKelas;
        const cocokMapel = !filterMapel || row.dataset.mapel === filterMapel;
        const visible    = cocokKelas && cocokMapel;
        row.style.display = visible ? '' : 'none';
        if (visible) tampil++;
    });

    // Tampilkan pesan kosong jika semua tersembunyi
    const emptyRow = document.getElementById('rowEmptyFilterLalu');
    if (emptyRow) emptyRow.style.display = tampil === 0 ? '' : 'none';
}
</script>
@endpush
@endsection
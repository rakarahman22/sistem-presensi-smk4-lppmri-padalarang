@extends('layouts.app')

@section('title', 'Kelola Presensi Siswa - Admin Panel')

@section('content')
<div class="container-fluid px-0 px-md-2">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" style="color: #14532d;">
            <i class="bi bi-calendar-check-fill me-2"></i>Kelola Presensi Siswa
        </h3>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">
                <i class="bi bi-clock me-1"></i> Terakhir diperbarui: <span id="lastUpdated"></span>
            </span>
        </div>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert border-0 shadow-sm mb-4 py-3 small"
             style="border-radius: 10px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-1"></i>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    {{-- FILTER TANGGAL --}}
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white" style="border-radius: 14px;">
        <form method="GET" action="{{ route('admin.presensi') }}"
              class="d-flex align-items-center gap-3 flex-wrap">
            <label class="fw-semibold small text-muted mb-0">
                <i class="bi bi-calendar3 me-1"></i> Tampilkan Presensi Tanggal:
            </label>
            <input type="date"
                   name="tanggal"
                   value="{{ $tanggal }}"
                   class="form-control form-control-sm"
                   style="max-width: 180px; border-radius: 8px;"
                   onchange="this.form.submit()">
            <span class="badge bg-success px-3 py-2"
                  style="font-size: 0.85rem; border-radius: 8px;">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
            </span>
            @if($tanggal !== \Carbon\Carbon::today()->toDateString())
                <a href="{{ route('admin.presensi') }}"
                   class="btn btn-sm btn-outline-secondary"
                   style="border-radius: 8px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Kembali ke Hari Ini
                </a>
            @endif
        </form>
    </div>

    {{-- CARD STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-people-fill me-1 text-primary"></i>Total Presensi
                </small>
                <h5 class="fw-bold text-primary m-0">
                    {{ $presensiHariIni->count() }}
                    <small class="fs-6 fw-normal text-muted">/ {{ $totalSiswa }}</small>
                </h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i>Hadir
                </small>
                <h5 class="fw-bold text-success m-0">{{ $totalHadir }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-exclamation-circle-fill me-1 text-warning"></i>Terlambat
                </small>
                <h5 class="fw-bold text-warning m-0">{{ $totalTerlambat }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-x-circle-fill me-1 text-danger"></i>Alpa
                </small>
                <h5 class="fw-bold text-danger m-0">{{ $totalAlpa }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-info-circle-fill me-1 text-info"></i>Izin / Sakit
                </small>
                <h5 class="fw-bold text-info m-0">{{ $totalIzinSakit }}</h5>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 0.78rem;">
                    <i class="bi bi-dash-circle-fill me-1 text-secondary"></i>Belum Absen
                </small>
                <h5 class="fw-bold text-secondary m-0">{{ $totalBelumAbsen }}</h5>
            </div>
        </div>
    </div>

    {{-- KARTU UTAMA TABEL --}}
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">

            {{-- Search --}}
            <div class="input-group" style="max-width: 280px;">
                <span class="input-group-text bg-light border-end-0 text-muted"
                      style="border-radius: 10px 0 0 10px;">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchPresence"
                       class="form-control bg-light border-start-0"
                       placeholder="Cari nama atau NIS..."
                       style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Filter Kelas --}}
                <select id="filterKelas" class="form-select form-select-sm"
                        style="max-width: 170px; border-radius: 10px;">
                    <option value="">Semua Kelas</option>
                    @foreach($presensiHariIni->pluck('siswa.kelas')->unique('id_kelas')->filter() as $kelas)
                        <option value="{{ strtolower($kelas->tingkat . ' ' . $kelas->nama_kelas) }}">
                            {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select id="filterStatus" class="form-select form-select-sm"
                        style="max-width: 150px; border-radius: 10px;">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                </select>

                {{-- Tombol Cetak --}}
                <button onclick="window.print()"
                        class="btn btn-sm btn-outline-secondary"
                        style="border-radius: 8px;">
                    <i class="bi bi-printer me-1"></i>Cetak
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-muted fw-semibold">
                    <tr>
                        <th class="ps-3" style="width: 45px;">No</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Koordinat</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="presenceTableBody">
                    @forelse($presensiHariIni as $index => $data)
                        @php
                            $badgeClass = match($data->status) {
                                'Hadir'     => 'bg-success',
                                'Terlambat' => 'bg-warning text-dark',
                                'Izin'      => 'bg-info text-dark',
                                'Sakit'     => 'bg-secondary',
                                default     => 'bg-danger',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 fw-medium text-secondary">{{ $index + 1 }}</td>
                            <td class="text-secondary small">{{ $data->siswa->nis ?? '-' }}</td>
                            <td class="fw-semibold text-dark">
                                {{ $data->siswa->nama_siswa ?? 'Data Terhapus' }}
                            </td>
                            <td>
                                @if($data->siswa && $data->siswa->kelas)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                          style="border-radius: 6px; font-weight: 600; font-size: 0.78rem;">
                                        {{ $data->siswa->kelas->tingkat }} {{ $data->siswa->kelas->nama_kelas }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="fw-bold text-primary small">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') }} WIB
                            </td>
                            <td>
                                @if($data->lat_siswa && $data->long_siswa)
                                    <a href="https://www.google.com/maps?q={{ $data->lat_siswa }},{{ $data->long_siswa }}"
                                       target="_blank"
                                       class="btn btn-sm btn-light text-danger fw-medium"
                                       style="border-radius: 6px; font-size: 0.8rem;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        {{ round($data->lat_siswa, 4) }}, {{ round($data->long_siswa, 4) }}
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Manual</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 fw-bold"
                                      style="font-size: 0.78rem;">
                                    {{ $data->status }}
                                </span>
                            </td>
                            <td class="small">
                                @if($data->keterangan)
                                    <span class="text-muted">
                                        <i class="bi bi-chat-left-text me-1 text-info"></i>
                                        {{ $data->keterangan }}
                                    </span>
                                    @if($data->edited_at)
                                        <br>
                                        <span class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-pencil me-1"></i>
                                            Diedit {{ \Carbon\Carbon::parse($data->edited_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-outline-warning fw-semibold"
                                    style="border-radius: 6px; font-size: 0.8rem;"
                                    onclick="bukaModalKoreksi(
                                        {{ $data->id_presensi }},
                                        '{{ addslashes($data->siswa->nama_siswa ?? 'Siswa') }}',
                                        '{{ $data->tgl_presensi }}',
                                        '{{ $data->status }}',
                                        '{{ addslashes($data->keterangan ?? '') }}'
                                    )">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x display-6 d-block mb-2 text-secondary"></i>
                                Belum ada data presensi pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- MODAL KOREKSI PRESENSI --}}
<div class="modal fade" id="modalKoreksiPresensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0" style="border-radius: 16px;">

            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Koreksi Presensi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formKoreksi" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body pt-2">

                    {{-- Info siswa --}}
                    <div class="alert alert-light border py-2 px-3 mb-3 small"
                         style="border-radius: 8px;">
                        <i class="bi bi-person-fill me-1 text-success"></i>
                        <span id="infoNamaSiswa" class="fw-semibold">—</span>
                        <span class="text-muted ms-1" id="infoTanggalSiswa"></span>
                    </div>

                    {{-- Pilih Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">
                            Ubah Status Menjadi
                        </label>
                        <select name="status" id="inputStatus"
                                class="form-select form-select-sm" required>
                            <option value="Hadir">✅ Hadir</option>
                            <option value="Terlambat">⏰ Terlambat</option>
                            <option value="Izin">📋 Izin</option>
                            <option value="Sakit">🤒 Sakit</option>
                            <option value="Alpa">❌ Alpa</option>
                        </select>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark">
                            Keterangan
                            <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <input type="text"
                               name="keterangan"
                               id="inputKeterangan"
                               class="form-control form-control-sm"
                               maxlength="255"
                               placeholder="cth: Surat sakit ditunjukkan langsung, 3 Juni 2026">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-whatsapp me-1 text-success"></i>
                            Bukti dapat diserahkan via WhatsApp atau langsung ke admin.
                        </small>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                            class="btn btn-sm btn-light px-3"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-sm btn-warning fw-bold text-dark px-4">
                        <i class="bi bi-check-lg me-1"></i>Simpan Koreksi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // Waktu terakhir diperbarui
    document.getElementById('lastUpdated').textContent =
        new Date().toLocaleTimeString('id-ID');

    // Auto-refresh 60 detik hanya saat melihat hari ini
    @if($tanggal === \Carbon\Carbon::today()->toDateString())
        setTimeout(() => location.reload(), 60000);
    @endif

    // Route koreksi dengan placeholder :id
    const routeKoreksi = '{{ route("admin.presensi.koreksi", ["id" => ":id"]) }}';

    // Buka modal dan isi data siswa yang akan dikoreksi
    function bukaModalKoreksi(idPresensi, namaSiswa, tanggal, statusSaat, keteranganSaat) {
        document.getElementById('infoNamaSiswa').textContent    = namaSiswa;
        document.getElementById('infoTanggalSiswa').textContent = '— ' + tanggal;
        document.getElementById('inputStatus').value            = statusSaat;
        document.getElementById('inputKeterangan').value        = keteranganSaat;

        // Ganti placeholder :id dengan id presensi yang sebenarnya
        document.getElementById('formKoreksi').action =
            routeKoreksi.replace(':id', idPresensi);

        new bootstrap.Modal(
            document.getElementById('modalKoreksiPresensi')
        ).show();
    }

    // Filter gabungan: search + kelas + status
    function applyFilter() {
        const search = document.getElementById('searchPresence').value.toLowerCase();
        const kelas  = document.getElementById('filterKelas').value.toLowerCase();
        const status = document.getElementById('filterStatus').value.toLowerCase();

        document.querySelectorAll('#presenceTableBody tr').forEach(function(row) {
            if (row.cells.length <= 1) return;

            const text        = row.textContent.toLowerCase();
            const matchSearch = !search || text.includes(search);
            const matchKelas  = !kelas  || text.includes(kelas);
            const matchStatus = !status || text.includes(status);

            row.style.display = (matchSearch && matchKelas && matchStatus) ? '' : 'none';
        });
    }

    document.getElementById('searchPresence').addEventListener('keyup', applyFilter);
    document.getElementById('filterKelas').addEventListener('change', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
</script>
@endsection
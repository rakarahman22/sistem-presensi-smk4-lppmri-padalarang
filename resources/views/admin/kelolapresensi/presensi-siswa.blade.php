@extends('layouts.app')

@section('title', 'Monitoring Presensi Siswa - Admin Panel')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" style="color: #14532d;">
            <i class="bi bi-calendar-check-fill me-2"></i>Monitoring Presensi Siswa
        </h3>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted fw-medium small">
                <i class="bi bi-calendar3 me-1"></i> Hari Ini:
            </span>
            <span class="badge bg-success" style="background-color: #15803d !important;">
                {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
            </span>
            <span class="text-muted small ms-2">
                <i class="bi bi-clock me-1"></i> Terakhir diperbarui: <span id="lastUpdated"></span>
            </span>
        </div>
    </div>

    <!-- CARD STATISTIK -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">
                    <i class="bi bi-people-fill me-1 text-primary"></i>Total Absen Hari Ini
                </small>
                <h4 class="fw-bold text-primary m-0">{{ $presensiHariIni->count() }} <small class="fs-6 fw-normal text-muted">/ {{ $totalSiswa }} Siswa</small></h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i>Hadir Tepat Waktu
                </small>
                <h4 class="fw-bold text-success m-0">{{ $totalHadir }} Siswa</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">
                    <i class="bi bi-exclamation-circle-fill me-1 text-warning"></i>Terlambat
                </small>
                <h4 class="fw-bold text-warning m-0">{{ $totalTerlambat }} Siswa</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">
                    <i class="bi bi-x-circle-fill me-1 text-danger"></i>Belum Absen
                </small>
                <h4 class="fw-bold text-danger m-0">{{ $totalBelumAbsen }} Siswa</h4>
            </div>
        </div>
    </div>

    <!-- KARTU UTAMA TABEL MONITORING -->
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            {{-- Search --}}
            <div class="input-group" style="max-width: 280px;">
                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchPresence" class="form-control bg-light border-start-0"
                    placeholder="Cari nama atau NIS..."
                    style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Filter Kelas --}}
                <select id="filterKelas" class="form-select form-select-sm" style="max-width: 170px; border-radius: 10px;">
                    <option value="">Semua Kelas</option>
                    @foreach($presensiHariIni->pluck('siswa.kelas')->unique('id_kelas')->filter() as $kelas)
                        <option value="{{ strtolower($kelas->tingkat . ' ' . $kelas->nama_kelas) }}">
                            {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select id="filterStatus" class="form-select form-select-sm" style="max-width: 140px; border-radius: 10px;">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                </select>

                {{-- Tombol Cetak --}}
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                    <i class="bi bi-printer me-1"></i>Cetak
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light text-muted fw-semibold">
                    <tr>
                        <th class="ps-3" style="width: 55px;">No</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Koordinat Posisi</th>
                        <th class="text-center" style="width: 120px;">Status</th>
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
                            <td class="text-secondary">{{ $data->siswa->nis ?? '-' }}</td>
                            <td class="fw-semibold text-dark">{{ $data->siswa->nama_siswa ?? 'Data Terhapus' }}</td>
                            <td>
                                @if($data->siswa && $data->siswa->kelas)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 6px; font-weight: 600;">
                                        {{ $data->siswa->kelas->tingkat }} {{ $data->siswa->kelas->nama_kelas }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="fw-bold text-primary">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') }} WIB
                            </td>
                            <td>
                                @if($data->lat_siswa && $data->long_siswa)
                                    <a href="https://www.google.com/maps?q={{ $data->lat_siswa }},{{ $data->long_siswa }}"
                                        target="_blank"
                                        class="btn btn-sm btn-light text-danger fw-medium"
                                        style="border-radius: 6px; font-size: 0.85rem;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        {{ round($data->lat_siswa, 4) }}, {{ round($data->long_siswa, 4) }}
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Input Manual Admin</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 fw-bold" style="font-size: 0.82rem;">
                                    {{ $data->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x display-6 d-block mb-2 text-secondary"></i>
                                Belum ada siswa yang melakukan presensi masuk pada hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Tampilkan waktu terakhir diperbarui
    document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString('id-ID');

    // Auto-refresh setiap 60 detik
    setTimeout(() => location.reload(), 60000);

    // Fungsi filter gabungan (search + kelas + status)
    function applyFilter() {
        const search = document.getElementById('searchPresence').value.toLowerCase();
        const kelas  = document.getElementById('filterKelas').value.toLowerCase();
        const status = document.getElementById('filterStatus').value.toLowerCase();

        document.querySelectorAll('#presenceTableBody tr').forEach(function(row) {
            if (row.cells.length <= 1) return; // skip baris kosong

            const text       = row.textContent.toLowerCase();
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
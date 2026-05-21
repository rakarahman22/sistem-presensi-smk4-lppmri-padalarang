@extends('layouts.app')

@section('title', 'Monitoring Presensi Siswa - Admin Panel')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success m-0" style="color: #14532d !important;">
            <i class="bi bi-calendar-check-fill me-2"></i>Monitoring Presensi Siswa
        </h3>
        <div class="text-muted fw-medium">
            <i class="bi bi-calendar3 me-1"></i> Hari Ini: <span class="badge bg-success" style="background-color: #15803d !important;">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- CARD STATISTIK SINGKAT -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">Total Hadir</small>
                <h4 class="fw-bold text-primary m-0">{{ $presensiHariIni->count() }} Siswa</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <small class="text-muted d-block mb-1 fw-semibold">Absen Mandiri (GPS)</small>
                <h4 class="fw-bold text-success m-0">{{ $presensiHariIni->whereNotNull('lat_siswa')->count() }}</h4>
            </div>
        </div>
    </div>

    <!-- KARTU UTAMA TABEL MONITORING -->
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
        
        <div class="d-flex justify-content-between align-items-center mb-3 group-search">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-search"></i></span>
                <input type="text" id="searchPresence" class="form-control bg-light border-start-0" placeholder="Cari nama atau NIS..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
            </div>
            <div class="text-muted small">
                *Data diperbarui otomatis berdasarkan ketukan absen siswa.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light text-muted fw-semibold">
                    <tr>
                        <th class="ps-3" style="border-radius: 10px 0 0 10px; width: 70px;">No</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Koordinat Posisi</th>
                        <th class="text-end pe-3" style="border-radius: 0 10px 10px 0; width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody id="presenceTableBody">
                    @forelse($presensiHariIni as $index => $data)
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
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') }} WIB
                            </td>
                            <td>
                                @if($data->lat_siswa && $data->long_siswa)
                                    <a href="https://www.google.com/maps?q={{ $data->lat_siswa }},{{ $data->long_siswa }}" target="_blank" class="btn btn-sm btn-light text-danger fw-medium" style="border-radius: 6px; font-size: 0.85rem;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ round($data->lat_siswa, 4) }}, {{ round($data->long_siswa, 4) }}
                                    </a>
                                @else
                                    <span class="text-muted small italic">Input Manual Admin</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
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
    
    <footer class="text-center text-muted small mt-5">© 2026 Panel Administrator - SMK 4 LPPM RI Padalarang</footer>
</div>

<script>
    // Fitur pencarian data realtime di sisi client browser
    document.getElementById('searchPresence').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#presenceTableBody tr');
        
        rows.forEach(function(row) {
            if(row.cells.length > 1) { // Lewati jika tr adalah baris data kosong
                let text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? '' : 'none';
            }
        });
    });
</script>
@endsection
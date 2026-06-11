@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid px-0 px-md-2">

    {{-- ══════════════════════════════════════════
         HEADER SELAMAT DATANG
    ══════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #1a3a5c 0%, #1e40af 100%);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="text-white-50 small mb-1 fw-semibold text-uppercase letter-spacing-1">Selamat Datang</p>
                <h4 class="fw-bold text-white mb-1">{{ $guru->nama_guru }} 👋</h4>
                <p class="text-white-50 mb-0 small">
                    NIP: {{ $guru->nip }} &nbsp;|&nbsp;
                    <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-2 py-1" style="border-radius:6px; font-weight:600;">
                        <i class="bi bi-person-badge-fill me-1"></i>Tenaga Pengajar
                    </span>
                </p>
            </div>
            <div class="text-end">
                <p class="text-white-50 small mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                <p class="text-white fw-bold mb-0" id="jam-sekarang" style="font-size: 1.4rem;"></p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STAT CARDS
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius:14px; border-left: 4px solid #15803d !important;">
                <p class="text-muted small mb-1 fw-semibold">Sesi Hari Ini</p>
                <h3 class="fw-bold text-dark mb-0">{{ $sesiHariIni }}</h3>
                <p class="text-muted mb-0" style="font-size:0.78rem;">sesi dibuka</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius:14px; border-left: 4px solid #1e40af !important;">
                <p class="text-muted small mb-1 fw-semibold">Sesi Bulan Ini</p>
                <h3 class="fw-bold text-dark mb-0">{{ $sesiBulanIni }}</h3>
                <p class="text-muted mb-0" style="font-size:0.78rem;">total pertemuan</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius:14px; border-left: 4px solid #0891b2 !important;">
                <p class="text-muted small mb-1 fw-semibold">Kelas Diajar</p>
                <h3 class="fw-bold text-dark mb-0">{{ $jumlahKelas }}</h3>
                <p class="text-muted mb-0" style="font-size:0.78rem;">kelas aktif bulan ini</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius:14px; border-left: 4px solid #7c3aed !important;">
                <p class="text-muted small mb-1 fw-semibold">Mata Pelajaran</p>
                <h3 class="fw-bold text-dark mb-0">{{ $jumlahMapel }}</h3>
                <p class="text-muted mb-0" style="font-size:0.78rem;">mapel diajar bulan ini</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         REKAP ABSEN PER MAPEL (BULAN INI)
    ══════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bar-chart-fill text-primary me-2"></i>Rekap Kehadiran Per Mapel
                    </h5>
                    <p class="text-muted small mb-0">Akumulasi bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                </div>
                <a href="{{ route('guru.absen-mapel.rekap') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius:8px;">
                    <i class="bi bi-table me-1"></i> Rekap Detail
                </a>
            </div>

            @if($rekapPerMapel->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x display-5 d-block mb-2 text-secondary"></i>
                    Belum ada sesi mengajar yang tercatat bulan ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8fafc;">
                            <tr class="text-secondary" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                <th class="fw-semibold py-3 ps-3">Mata Pelajaran</th>
                                <th class="fw-semibold py-3">Kelas</th>
                                <th class="fw-semibold py-3 text-center">Pertemuan</th>
                                <th class="fw-semibold py-3 text-center text-success">H</th>
                                <th class="fw-semibold py-3 text-center text-warning">S</th>
                                <th class="fw-semibold py-3 text-center text-info">I</th>
                                <th class="fw-semibold py-3 text-center text-danger">A</th>
                                <th class="fw-semibold py-3 text-center">Kehadiran</th>
                                <th class="fw-semibold py-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapPerMapel as $row)
                                @php
                                    $persen = $row->persen_hadir;
                                    if ($persen >= 80)      { $barColor = '#15803d'; $badgeBg = 'bg-success'; }
                                    elseif ($persen >= 75)  { $barColor = '#ca8a04'; $badgeBg = 'bg-warning text-dark'; }
                                    else                    { $barColor = '#dc2626'; $badgeBg = 'bg-danger'; }
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $row->nama_mapel }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="border-radius:6px; font-weight:600;">
                                            {{ $row->kelas->tingkat ?? '' }} {{ $row->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary border" style="border-radius:6px;">
                                            {{ $row->total_pertemuan }}x
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-success">{{ $row->total_hadir }}</td>
                                    <td class="text-center fw-bold text-warning">{{ $row->total_sakit }}</td>
                                    <td class="text-center fw-bold text-info">{{ $row->total_izin }}</td>
                                    <td class="text-center fw-bold text-danger">{{ $row->total_alpa }}</td>
                                    <td class="text-center" style="min-width: 120px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1" style="height: 6px; background:#e5e7eb; border-radius:99px; overflow:hidden;">
                                                <div style="width:{{ $persen }}%; height:100%; background:{{ $barColor }}; border-radius:99px; transition: width .4s;"></div>
                                            </div>
                                            <span class="badge {{ $badgeBg }} px-2" style="border-radius:6px; font-size:0.78rem; min-width:46px;">
                                                {{ $persen }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('guru.absen-mapel.rekap.tampil', ['id_kelas' => $row->id_kelas, 'nama_mapel' => $row->nama_mapel]) }}"
                                           class="btn btn-sm btn-outline-secondary px-2 py-1"
                                           style="border-radius:6px; font-size:0.8rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         SESI MENGAJAR HARI INI + MENU CEPAT
    ══════════════════════════════════════════ --}}
    <div class="row g-3">

        {{-- Sesi Hari Ini --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-clock-history text-success me-2"></i>Sesi Mengajar Hari Ini
                    </h6>

                    @if($sesiAktifHariIni->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada sesi dibuka hari ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted" style="font-size: 0.82rem;">
                                    <tr>
                                        <th>Jam</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th class="text-center">Pertemuan</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesiAktifHariIni as $s)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }}</td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success" style="font-weight:600;">
                                                    {{ $s->kelas->tingkat }} {{ $s->kelas->nama_kelas }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-dark">{{ $s->nama_mapel }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2" style="border-radius:6px; font-weight:600;">
                                                    Ke-{{ $s->pertemuan_ke }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('guru.absen-mapel.isi', $s->id_mengajar) }}"
                                                   class="btn btn-sm btn-outline-primary px-3" style="border-radius:6px;">
                                                    <i class="bi bi-pencil-square me-1"></i>Ubah
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Menu Cepat --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Menu Cepat
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('guru.absen-mapel.index') }}"
                           class="btn fw-semibold text-start py-3 px-3"
                           style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; border-radius:10px;">
                            <i class="bi bi-play-fill me-2"></i>Mulai Absen Kelas
                        </a>
                        <a href="{{ route('guru.absen-mapel.rekap') }}"
                           class="btn fw-semibold text-start py-3 px-3"
                           style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:10px;">
                            <i class="bi bi-table me-2"></i>Rekap Absensi Mapel
                        </a>
                        <a href="#"
                           class="btn fw-semibold text-start py-3 px-3"
                           style="background:#faf5ff; color:#7c3aed; border:1px solid #ddd6fe; border-radius:10px;">
                            <i class="bi bi-check2-square me-2"></i>Validasi Kehadiran
                        </a>
                        <a href="#"
                           class="btn fw-semibold text-start py-3 px-3"
                           style="background:#f0f9ff; color:#0891b2; border:1px solid #bae6fd; border-radius:10px;">
                            <i class="bi bi-eye-fill me-2"></i>Monitoring Geofence
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .letter-spacing-1 { letter-spacing: 0.06em; }
    .table > :not(caption) > * > * { padding: 0.75rem 0.6rem; }
</style>
@endpush

@push('scripts')
<script>
    // Jam real-time
    function updateJam() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('jam-sekarang');
        if (el) el.textContent = `${h}:${m}:${s} WIB`;
    }
    updateJam();
    setInterval(updateJam, 1000);
</script>
@endpush
@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid px-0 px-md-2">

    {{-- ══════════════════════════════════════════
         HEADER SELAMAT DATANG
    ══════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4 dash-header">
        <div class="dash-header-pattern"></div>
        <div class="card-body p-4 position-relative">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="dash-avatar">
                        {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-white-50 small mb-1 fw-semibold text-uppercase letter-spacing-1">Selamat Datang</p>
                        <h4 class="fw-bold text-white mb-1">{{ $guru->nama_guru }} 👋</h4>
                        <p class="text-white-75 mb-0 small d-flex align-items-center gap-2">
                            <span>NIP: {{ $guru->nip }}</span>
                            <span class="dash-divider"></span>
                            <span><i class="bi bi-person-badge-fill me-1"></i>Tenaga Pengajar</span>
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <p class="text-white-50 small mb-1 d-flex align-items-center justify-content-end gap-1">
                        <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="text-white fw-bold mb-0 dash-clock" id="jam-sekarang"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STAT CARDS
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="--accent:#15803d;">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <i class="bi bi-clock-fill stat-icon"></i>
                    <p class="text-muted small mb-1 fw-semibold">Sesi Hari Ini</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $sesiHariIni }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.78rem;">sesi dibuka</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="--accent:#1e40af;">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <i class="bi bi-calendar2-week-fill stat-icon"></i>
                    <p class="text-muted small mb-1 fw-semibold">Sesi Bulan Ini</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $sesiBulanIni }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.78rem;">total pertemuan</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="--accent:#0891b2;">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <i class="bi bi-door-open-fill stat-icon"></i>
                    <p class="text-muted small mb-1 fw-semibold">Kelas Diajar</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $jumlahKelas }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.78rem;">kelas aktif bulan ini</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="--accent:#7c3aed;">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <i class="bi bi-journal-bookmark-fill stat-icon"></i>
                    <p class="text-muted small mb-1 fw-semibold">Mata Pelajaran</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $jumlahMapel }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.78rem;">mapel diajar bulan ini</p>
                </div>
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
                    <table class="table table-hover align-middle mb-0 table-zebra">
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
                            <table class="table table-hover align-middle mb-0 table-zebra">
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
                        <a href="{{ route('guru.absen-mapel.index') }}" class="quick-menu-item" style="--qm-color:#15803d; --qm-bg:#f0fdf4;">
                            <span class="quick-menu-icon"><i class="bi bi-play-fill"></i></span>
                            <span class="quick-menu-text">
                                <span class="quick-menu-title">Mulai Absen Kelas</span>
                                <span class="quick-menu-sub">Catat kehadiran sesi mengajar</span>
                            </span>
                            <i class="bi bi-chevron-right quick-menu-arrow"></i>
                        </a>
                        <a href="{{ route('guru.absen-mapel.rekap') }}" class="quick-menu-item" style="--qm-color:#1e40af; --qm-bg:#eff6ff;">
                            <span class="quick-menu-icon"><i class="bi bi-table"></i></span>
                            <span class="quick-menu-text">
                                <span class="quick-menu-title">Rekap Absensi Mapel</span>
                                <span class="quick-menu-sub">Lihat ringkasan kehadiran</span>
                            </span>
                            <i class="bi bi-chevron-right quick-menu-arrow"></i>
                        </a>
                        <a href="#" class="quick-menu-item" style="--qm-color:#7c3aed; --qm-bg:#faf5ff;">
                            <span class="quick-menu-icon"><i class="bi bi-check2-square"></i></span>
                            <span class="quick-menu-text">
                                <span class="quick-menu-title">Validasi Kehadiran</span>
                                <span class="quick-menu-sub">Periksa data absen siswa</span>
                            </span>
                            <i class="bi bi-chevron-right quick-menu-arrow"></i>
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
    :root {
        --gd-green-dark: #145f15;
        --gd-green: #15803d;
        --gd-green-light: #22b508;
        --gd-blue: #1e40af;
        --gd-cyan: #0891b2;
        --gd-purple: #7c3aed;
    }

    .letter-spacing-1 { letter-spacing: 0.06em; }
    .table > :not(caption) > * > * { padding: 0.85rem 0.6rem; }
    .text-white-75 { color: rgba(255,255,255,.75); }

    /* ── Header ────────────────────────────────────── */
    .dash-header {
        border-radius: 15px;
        background: linear-gradient(135deg, var(--gd-green-dark) 0%, var(--gd-green-light) 100%);
        position: relative;
        overflow: hidden;
    }
    .dash-header-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.10) 1.5px, transparent 1.5px);
        background-size: 22px 22px;
        opacity: .6;
        pointer-events: none;
    }
    .dash-avatar {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.3rem;
        color: #fff;
        flex-shrink: 0;
    }
    .dash-divider {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(255,255,255,.4);
        display: inline-block;
    }
    .dash-clock {
        font-size: 1.4rem;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.05em;
    }

    /* ── Stat cards ───────────────────────────────────── */
    .stat-card {
        border-radius: 14px;
        border-left: 4px solid var(--accent) !important;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -8px rgba(0,0,0,0.18) !important;
    }
    .stat-icon {
        position: absolute;
        top: 0.6rem;
        right: 0.7rem;
        font-size: 2.4rem;
        color: var(--accent);
        opacity: 0.12;
        line-height: 1;
    }

    /* ── Table zebra ──────────────────────────────────── */
    .table-zebra tbody tr:nth-child(even) { background-color: #fafbfc; }
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f0fdf4 !important; }

    /* ── Menu cepat ───────────────────────────────────── */
    .quick-menu-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.75rem 0.9rem;
        border-radius: 10px;
        text-decoration: none;
        border: 1px solid color-mix(in srgb, var(--qm-color) 18%, transparent);
        background: var(--qm-bg);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .quick-menu-item:hover {
        transform: translateX(2px);
        box-shadow: 0 6px 16px -8px color-mix(in srgb, var(--qm-color) 35%, transparent);
    }
    .quick-menu-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--qm-color);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .quick-menu-text {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        min-width: 0;
    }
    .quick-menu-title {
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--qm-color);
    }
    .quick-menu-sub {
        font-size: 0.74rem;
        color: #6b7280;
    }
    .quick-menu-arrow {
        color: var(--qm-color);
        opacity: .5;
        flex-shrink: 0;
    }
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
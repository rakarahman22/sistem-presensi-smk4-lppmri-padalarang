@extends('layouts.app')

@section('title', 'Absensi Per Mata Pelajaran')

@section('content')
@php $siswa = Auth::guard('siswa')->user(); @endphp

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-journal-check text-success me-2"></i>Absensi Per Mata Pelajaran
            </h4>
            <p class="text-muted small mb-0">
                {{ $siswa->nama_siswa }} &nbsp;·&nbsp;
                NIS: <span class="fw-semibold">{{ $siswa->nis }}</span> &nbsp;·&nbsp;
                Kelas: <span class="fw-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
            </p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{{ route('siswa.dashboard') }}" class="text-decoration-none text-primary">
                        <i class="bi bi-house me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted">Absensi Per Mapel</li>
            </ol>
        </nav>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <div class="row g-2 align-items-end">

                {{-- Filter Bulan --}}
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                    <select id="filterBulan" class="form-select form-select-sm rounded-3" style="min-width:130px">
                        @foreach(range(1,12) as $bln)
                            <option value="{{ $bln }}" {{ now()->month == $bln ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                    <select id="filterTahun" class="form-select form-select-sm rounded-3" style="min-width:100px">
                        @foreach(range(now()->year, now()->year - 3) as $thn)
                            <option value="{{ $thn }}" {{ now()->year == $thn ? 'selected' : '' }}>
                                {{ $thn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mata Pelajaran --}}
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold text-muted mb-1">Mata Pelajaran</label>
                    <select id="filterMapel" class="form-select form-select-sm rounded-3" style="min-width:200px">
                        <option value="">— Semua Mata Pelajaran —</option>
                        @foreach($daftarMapel as $mapel)
                            <option value="{{ $mapel }}">{{ $mapel }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Reset --}}
                <div class="col-12 col-sm-auto">
                    <button id="btnReset" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </button>
                </div>

                {{-- Label periode aktif --}}
                <div class="col-12 col-sm-auto ms-sm-auto d-flex align-items-end">
                    <span id="labelPeriode"
                          class="badge bg-success-subtle text-success border border-success-subtle
                                 rounded-pill px-3 py-2 small">
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('F Y') }}
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-hadir rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap hadir-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-number" id="statHadir">—</div>
                    <div class="stat-label">Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-sakit rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap sakit-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                <div>
                    <div class="stat-number" id="statSakit">—</div>
                    <div class="stat-label">Sakit</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-izin rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap izin-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <div class="stat-number" id="statIzin">—</div>
                    <div class="stat-label">Izin</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-alpa rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap alpa-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-number" id="statAlpa">—</div>
                    <div class="stat-label">Alpa</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RINGKASAN PERSENTASE PER MAPEL ===== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bar-chart-fill text-success me-2"></i>Ringkasan Per Mata Pelajaran
                </h5>
                <p class="text-muted small mb-0 mt-1">Persentase kehadiran dihitung dari total pertemuan yang tercatat</p>
            </div>
            <div id="spinnerRingkasan"
                 class="spinner-border spinner-border-sm text-success"
                 role="status" style="display:none">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="card-body px-4 pb-4" id="ringkasanWrap">
            {{-- Diisi JavaScript --}}
        </div>
    </div>

    {{-- ===== TABEL DETAIL ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3
                    d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Detail Kehadiran</h5>
                <p class="text-muted small mb-0 mt-1" id="subTabel">Memuat data...</p>
            </div>
            <div id="spinnerTabel"
                 class="spinner-border spinner-border-sm text-success"
                 role="status" style="display:none">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="card-body p-0" id="tabelWrap">
            {{-- Diisi JavaScript --}}
        </div>
    </div>

</div>{{-- /container --}}

{{-- ===== STYLES ===== --}}
<style>
/* ── Stat cards ── */
.stat-card { border:1px solid transparent; transition:transform .15s,box-shadow .15s; }
.stat-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08)!important; }
.stat-icon-wrap { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0; }
.stat-number { font-size:1.6rem;font-weight:700;line-height:1; }
.stat-label  { font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:2px; }

.stat-hadir { background:#f0fdf4;border-color:#bbf7d0; }
.stat-hadir .stat-number { color:#166534; } .stat-hadir .stat-label { color:#15803d; }
.hadir-icon { background:#dcfce7;color:#16a34a; }

.stat-sakit { background:#fffbeb;border-color:#fde68a; }
.stat-sakit .stat-number { color:#92400e; } .stat-sakit .stat-label { color:#b45309; }
.sakit-icon { background:#fef3c7;color:#d97706; }

.stat-izin { background:#eff6ff;border-color:#bfdbfe; }
.stat-izin .stat-number { color:#1e3a8a; } .stat-izin .stat-label { color:#1d4ed8; }
.izin-icon { background:#dbeafe;color:#2563eb; }

.stat-alpa { background:#fff1f2;border-color:#fecdd3; }
.stat-alpa .stat-number { color:#9f1239; } .stat-alpa .stat-label { color:#be123c; }
.alpa-icon { background:#ffe4e6;color:#e11d48; }

/* ── Ringkasan per mapel ── */
.mapel-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    transition: box-shadow .15s;
}
.mapel-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.07); }
.mapel-card .mapel-name { font-weight: 600; font-size: .9rem; color: #1e293b; }
.mapel-card .mapel-meta { font-size: .75rem; color: #64748b; margin-top: 2px; }
.progress-mapel { height: 8px; border-radius: 4px; background: #f1f5f9; overflow: hidden; margin: 8px 0 4px; }
.progress-mapel .bar { height: 100%; border-radius: 4px; transition: width .4s ease; }
.pct-label { font-size: .8rem; font-weight: 700; }
.pct-lulus    { color: #15803d; }
.pct-tidak    { color: #dc2626; }
.badge-lulus  { background:#dcfce7; color:#166534; font-size:.7rem; padding:2px 8px; border-radius:20px; font-weight:600; }
.badge-tidak  { background:#ffe4e6; color:#9f1239; font-size:.7rem; padding:2px 8px; border-radius:20px; font-weight:600; }
.stat-mini    { font-size:.72rem; color:#64748b; }
.stat-mini span { font-weight:600; }

/* ── Tabel detail ── */
.table-header-row { background:#f8fafc; border-bottom:2px solid #e9ecef; }
.table > :not(caption) > * > * { border-bottom-color:#f1f5f9; }
.table-hover > tbody > tr:hover > * { background:#f8fafc; }

.badge-status { display:inline-flex;align-items:center;padding:.28rem .65rem;border-radius:20px;font-size:.75rem;font-weight:600;white-space:nowrap; }
.badge-hadir  { background:#dcfce7;color:#166534; }
.badge-sakit  { background:#fef3c7;color:#92400e; }
.badge-izin   { background:#dbeafe;color:#1e3a8a; }
.badge-alpa   { background:#ffe4e6;color:#9f1239; }

.fade-in { animation:fadeIn .25s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }
</style>

{{-- ===== JAVASCRIPT ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterBulan = document.getElementById('filterBulan');
    const filterTahun = document.getElementById('filterTahun');
    const filterMapel = document.getElementById('filterMapel');
    const btnReset    = document.getElementById('btnReset');
    const baseUrl     = '{{ route("siswa.riwayat-absen-mapel.data") }}';

    const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];

    // ── Fetch utama ────────────────────────────────────────────────
    function fetchData() {
        const bulan = filterBulan.value;
        const tahun = filterTahun.value;
        const mapel = filterMapel.value;

        document.getElementById('spinnerRingkasan').style.display = 'inline-block';
        document.getElementById('spinnerTabel').style.display     = 'inline-block';

        fetch(`${baseUrl}?bulan=${bulan}&tahun=${tahun}&mapel=${encodeURIComponent(mapel)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            updateStats(data.stats);
            updateLabelPeriode(bulan, tahun, mapel);
            updateRingkasan(data.ringkasan_per_mapel);
            updateTabel(data.rows, bulan, tahun);

            document.getElementById('spinnerRingkasan').style.display = 'none';
            document.getElementById('spinnerTabel').style.display     = 'none';
        })
        .catch(() => {
            document.getElementById('spinnerRingkasan').style.display = 'none';
            document.getElementById('spinnerTabel').style.display     = 'none';
        });
    }

    // ── Stat cards ─────────────────────────────────────────────────
    function updateStats(stats) {
        document.getElementById('statHadir').textContent = stats.hadir;
        document.getElementById('statSakit').textContent = stats.sakit;
        document.getElementById('statIzin').textContent  = stats.izin;
        document.getElementById('statAlpa').textContent  = stats.alpa;
    }

    // ── Label periode ───────────────────────────────────────────────
    function updateLabelPeriode(bulan, tahun, mapel) {
        const teks = mapel
            ? `${mapel} · ${namaBulan[bulan-1]} ${tahun}`
            : `${namaBulan[bulan-1]} ${tahun}`;
        document.getElementById('labelPeriode').innerHTML =
            `<i class="bi bi-calendar3 me-1"></i>${teks}`;

        const total = ['statHadir','statSakit','statIzin','statAlpa']
            .reduce((s,id) => s + (parseInt(document.getElementById(id).textContent)||0), 0);
        document.getElementById('subTabel').textContent =
            `${namaBulan[bulan-1]} ${tahun}` +
            (mapel ? ` · ${mapel}` : '') +
            ` · ${total} pertemuan tercatat`;
    }

    // ── Ringkasan per mapel ─────────────────────────────────────────
    function updateRingkasan(ringkasan) {
        const wrap = document.getElementById('ringkasanWrap');

        if (!ringkasan || ringkasan.length === 0) {
            wrap.innerHTML = `
                <div class="text-center py-4 text-muted fade-in">
                    <i class="bi bi-inbox d-block mb-2" style="font-size:2rem"></i>
                    <span class="small">Tidak ada data untuk periode ini.</span>
                </div>`;
            return;
        }

        // Susun grid kartu per mapel
        let html = '<div class="row g-3 fade-in">';
        ringkasan.forEach(m => {
            const barColor = m.lulus ? '#16a34a' : '#dc2626';
            const pctClass = m.lulus ? 'pct-lulus' : 'pct-tidak';
            const badgeKet = m.lulus
                ? `<span class="badge-lulus">Aman</span>`
                : `<span class="badge-tidak">Perlu Perhatian</span>`;

            html += `
            <div class="col-12 col-md-6 col-lg-4">
                <div class="mapel-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="mapel-name">${m.nama_mapel}</div>
                            <div class="mapel-meta">${m.total_pertemuan} pertemuan tercatat</div>
                        </div>
                        ${badgeKet}
                    </div>

                    <div class="progress-mapel">
                        <div class="bar" style="width:${m.persen_hadir}%; background:${barColor};"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="pct-label ${pctClass}">${m.persen_hadir}% Hadir</span>
                        <div class="d-flex gap-3">
                            <span class="stat-mini"><span class="text-success">${m.hadir}</span> H</span>
                            <span class="stat-mini"><span class="text-warning">${m.sakit}</span> S</span>
                            <span class="stat-mini"><span class="text-primary">${m.izin}</span> I</span>
                            <span class="stat-mini"><span class="text-danger">${m.alpa}</span> A</span>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';
        wrap.innerHTML = html;
    }

    // ── Badge status ────────────────────────────────────────────────
    function badgeStatus(status) {
        const map = {
            'Hadir': `<span class="badge-status badge-hadir"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>`,
            'Sakit': `<span class="badge-status badge-sakit"><i class="bi bi-heart-pulse-fill me-1"></i>Sakit</span>`,
            'Izin':  `<span class="badge-status badge-izin"><i class="bi bi-file-earmark-text-fill me-1"></i>Izin</span>`,
            'Alpa':  `<span class="badge-status badge-alpa"><i class="bi bi-x-circle-fill me-1"></i>Alpa</span>`,
        };
        return map[status] || `<span class="badge-status" style="background:#f1f5f9;color:#64748b;">—</span>`;
    }

    // ── Tabel detail ────────────────────────────────────────────────
    function updateTabel(rows, bulan, tahun) {
        const wrap = document.getElementById('tabelWrap');

        if (!rows || rows.length === 0) {
            wrap.innerHTML = `
                <div class="text-center py-5 px-4 fade-in">
                    <i class="bi bi-calendar-x text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h6 class="fw-semibold text-dark mb-1">Tidak Ada Data</h6>
                    <p class="text-muted small mb-0">
                        Tidak ada catatan absensi mapel pada periode ini.
                    </p>
                </div>`;
            return;
        }

        let rowsHtml = '';
        rows.forEach((item, i) => {
            rowsHtml += `
            <tr>
                <td class="ps-4 text-muted small">${i + 1}</td>
                <td class="py-3">
                    <span class="fw-semibold text-dark">${item.tgl_formatted}</span><br>
                    <span class="text-muted small">${item.hari_formatted}</span>
                </td>
                <td class="py-3 fw-semibold text-dark" style="font-size:.875rem;">
                    ${item.nama_mapel}
                </td>
                <td class="py-3 text-muted small">${item.jam_mulai} WIB</td>
                <td class="py-3">${badgeStatus(item.status)}</td>
            </tr>`;
        });

        wrap.innerHTML = `
            <div class="table-responsive fade-in">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-header-row">
                            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:5%">#</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:20%">
                                <i class="bi bi-calendar-date me-1"></i>Tanggal
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">
                                <i class="bi bi-book me-1"></i>Mata Pelajaran
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:15%">
                                <i class="bi bi-clock me-1"></i>Jam Mulai
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:15%">
                                <i class="bi bi-tag me-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            </div>`;
    }

    // ── Event listeners ─────────────────────────────────────────────
    filterBulan.addEventListener('change', fetchData);
    filterTahun.addEventListener('change', fetchData);
    filterMapel.addEventListener('change', fetchData);

    btnReset.addEventListener('click', function () {
        filterBulan.value = '{{ now()->month }}';
        filterTahun.value = '{{ now()->year }}';
        filterMapel.value = '';
        fetchData();
    });

    // Load awal
    fetchData();
});
</script>
@endsection
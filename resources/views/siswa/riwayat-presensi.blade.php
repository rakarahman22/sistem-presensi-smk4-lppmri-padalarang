@extends('layouts.app')

@section('title', 'Riwayat Presensi - SMK 4 LPPM RI Padalarang')

@section('content')
@php $siswa = Auth::guard('siswa')->user(); @endphp

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Presensi
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
                <li class="breadcrumb-item active text-muted">Riwayat Presensi</li>
            </ol>
        </nav>
    </div>

    {{-- ===== FILTER REALTIME ===== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <div class="row g-2 align-items-end">
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
                <div class="col-12 col-sm-auto">
                    <button id="btnReset" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </button>
                </div>
                <div class="col-12 col-sm-auto ms-sm-auto d-flex align-items-end">
                    <span id="labelPeriode" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== STAT CARDS (diupdate via JS) ===== --}}
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
            <div class="stat-card stat-izin rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap izin-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <div class="stat-number" id="statIzin">—</div>
                    <div class="stat-label">Izin</div>
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
            <div class="stat-card stat-alpa rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap alpa-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-number" id="statAlpa">—</div>
                    <div class="stat-label">Alpa</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PROGRESS BAR (diupdate via JS) ===== --}}
    <div id="progressWrap" class="card border-0 shadow-sm rounded-4 mb-4 px-4 py-3" style="display:none!important">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-semibold text-dark">Persentase Kehadiran</span>
            <span class="fw-bold text-success" id="pctHadirLabel">0% Hadir</span>
        </div>
        <div class="progress rounded-pill" style="height:10px;">
            <div id="barHadir" class="progress-bar bg-success" style="width:0%"></div>
            <div id="barIzin"  class="progress-bar bg-primary" style="width:0%"></div>
            <div id="barSakit" class="progress-bar bg-warning" style="width:0%"></div>
            <div id="barAlpa"  class="progress-bar bg-danger"  style="width:0%"></div>
        </div>
        <div class="d-flex gap-3 mt-2 flex-wrap">
            <span class="small text-muted"><span class="legend-dot bg-success"></span> Hadir <span id="legHadir">0</span>%</span>
            <span class="small text-muted"><span class="legend-dot bg-primary"></span> Izin <span id="legIzin">0</span>%</span>
            <span class="small text-muted"><span class="legend-dot bg-warning"></span> Sakit <span id="legSakit">0</span>%</span>
            <span class="small text-muted"><span class="legend-dot bg-danger"></span> Alpa <span id="legAlpa">0</span>%</span>
        </div>
    </div>

    {{-- ===== TABEL (diupdate via JS) ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Detail Presensi</h5>
                <p class="text-muted small mb-0 mt-1" id="subTabel">Memuat data...</p>
            </div>
            {{-- Spinner loading --}}
            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary" role="status" style="display:none">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="card-body p-0" id="tabelWrap">
            {{-- Diisi oleh JavaScript --}}
        </div>
    </div>

</div>

{{-- ===== STYLES ===== --}}
<style>
    .stat-card { border:1px solid transparent; transition:transform .15s ease,box-shadow .15s ease; }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08)!important; }
    .stat-icon-wrap { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0; }
    .stat-number { font-size:1.6rem;font-weight:700;line-height:1; }
    .stat-label  { font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:2px; }

    .stat-hadir { background:#f0fdf4;border-color:#bbf7d0; }
    .stat-hadir .stat-number { color:#166534; }
    .stat-hadir .stat-label  { color:#15803d; }
    .hadir-icon { background:#dcfce7;color:#16a34a; }

    .stat-izin { background:#eff6ff;border-color:#bfdbfe; }
    .stat-izin .stat-number { color:#1e3a8a; }
    .stat-izin .stat-label  { color:#1d4ed8; }
    .izin-icon { background:#dbeafe;color:#2563eb; }

    .stat-sakit { background:#fffbeb;border-color:#fde68a; }
    .stat-sakit .stat-number { color:#92400e; }
    .stat-sakit .stat-label  { color:#b45309; }
    .sakit-icon { background:#fef3c7;color:#d97706; }

    .stat-alpa { background:#fff1f2;border-color:#fecdd3; }
    .stat-alpa .stat-number { color:#9f1239; }
    .stat-alpa .stat-label  { color:#be123c; }
    .alpa-icon { background:#ffe4e6;color:#e11d48; }

    .legend-dot { display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;vertical-align:middle; }

    .table-header-row { background-color:#f8fafc;border-bottom:2px solid #e9ecef; }
    .table > :not(caption) > * > * { border-bottom-color:#f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color:#f8fafc; }

    .badge-status { display:inline-flex;align-items:center;padding:.28rem .65rem;border-radius:20px;font-size:.75rem;font-weight:600;white-space:nowrap; }
    .badge-hadir     { background:#dcfce7;color:#166534; }
    .badge-izin      { background:#dbeafe;color:#1e3a8a; }
    .badge-sakit     { background:#fef3c7;color:#92400e; }
    .badge-alpa      { background:#ffe4e6;color:#9f1239; }
    .badge-terlambat { background:#fff7ed;color:#9a3412; }
    .badge-unknown   { background:#f1f5f9;color:#64748b; }

    /* Fade animasi saat tabel diupdate */
    .fade-in { animation: fadeIn .25s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }

    /* Pagination Bootstrap override agar compact */
    .pagination { margin-bottom:0; }
    .page-link { font-size:.8rem; padding:.3rem .6rem; }
</style>

{{-- ===== JAVASCRIPT REALTIME ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterBulan = document.getElementById('filterBulan');
    const filterTahun = document.getElementById('filterTahun');
    const btnReset    = document.getElementById('btnReset');
    const baseUrl     = '{{ route("siswa.riwayat-presensi.data") }}';

    // Nama bulan dalam bahasa Indonesia
    const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];

    // ── Fetch data dari server ──────────────────────────────────────
    function fetchData(page = 1) {
        const bulan  = filterBulan.value;
        const tahun  = filterTahun.value;
        const spinner = document.getElementById('loadingSpinner');

        spinner.style.display = 'inline-block';

        fetch(`${baseUrl}?bulan=${bulan}&tahun=${tahun}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            updateStats(data.stats);
            updateProgress(data.stats);
            updateLabel(bulan, tahun);
            updateTabel(data);
            spinner.style.display = 'none';
        })
        .catch(() => {
            spinner.style.display = 'none';
        });
    }

    // ── Update stat cards ───────────────────────────────────────────
    function updateStats(stats) {
        document.getElementById('statHadir').textContent = stats.hadir;
        document.getElementById('statIzin').textContent  = stats.izin;
        document.getElementById('statSakit').textContent = stats.sakit;
        document.getElementById('statAlpa').textContent  = stats.alpa;
    }

    // ── Update progress bar ─────────────────────────────────────────
    function updateProgress(stats) {
        const wrap  = document.getElementById('progressWrap');
        const total = stats.hadir + stats.izin + stats.sakit + stats.alpa;

        if (total === 0) { wrap.style.setProperty('display','none','important'); return; }

        wrap.style.removeProperty('display');

        const pct = (v) => Math.round((v / total) * 100);
        const pH = pct(stats.hadir), pI = pct(stats.izin),
              pS = pct(stats.sakit), pA = pct(stats.alpa);

        document.getElementById('barHadir').style.width = pH + '%';
        document.getElementById('barIzin').style.width  = pI + '%';
        document.getElementById('barSakit').style.width = pS + '%';
        document.getElementById('barAlpa').style.width  = pA + '%';
        document.getElementById('pctHadirLabel').textContent = pH + '% Hadir';
        document.getElementById('legHadir').textContent = pH;
        document.getElementById('legIzin').textContent  = pI;
        document.getElementById('legSakit').textContent = pS;
        document.getElementById('legAlpa').textContent  = pA;
    }

    // ── Update label periode ────────────────────────────────────────
    function updateLabel(bulan, tahun) {
        document.getElementById('labelPeriode').innerHTML =
            `<i class="bi bi-calendar3 me-1"></i>${namaBulan[bulan-1]} ${tahun}`;
        const total = parseInt(document.getElementById('statHadir').textContent||0)
                    + parseInt(document.getElementById('statIzin').textContent||0)
                    + parseInt(document.getElementById('statSakit').textContent||0)
                    + parseInt(document.getElementById('statAlpa').textContent||0);
        document.getElementById('subTabel').textContent =
            `${namaBulan[bulan-1]} ${tahun} · ${total} hari tercatat`;
    }

    // ── Render badge status ─────────────────────────────────────────
    function badgeStatus(status) {
        const map = {
            hadir:     `<span class="badge-status badge-hadir"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>`,
            terlambat: `<span class="badge-status badge-terlambat"><i class="bi bi-clock-history me-1"></i>Terlambat</span>`,
            izin:      `<span class="badge-status badge-izin"><i class="bi bi-file-earmark-text-fill me-1"></i>Izin</span>`,
            sakit:     `<span class="badge-status badge-sakit"><i class="bi bi-heart-pulse-fill me-1"></i>Sakit</span>`,
            alpa:      `<span class="badge-status badge-alpa"><i class="bi bi-x-circle-fill me-1"></i>Alpa</span>`,
        };
        return map[status?.toLowerCase()] || `<span class="badge-status badge-unknown">—</span>`;
    }

    // ── Render kolom status awal ────────────────────────────────────
    function statusAwal(item) {
        if (item.status_awal && item.status_awal !== item.status) {
            return `<span class="badge-status badge-unknown text-decoration-line-through opacity-75" style="font-size:.7rem">${cap(item.status_awal)}</span>
                    <br><span class="text-muted" style="font-size:.7rem"><i class="bi bi-pencil-fill me-1 text-warning"></i>Dikoreksi</span>`;
        }
        return `<span class="text-muted small">—</span>`;
    }

    function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    // ── Update tabel & pagination ───────────────────────────────────
    function updateTabel(data) {
        const wrap = document.getElementById('tabelWrap');

        if (!data.rows || data.rows.length === 0) {
            wrap.innerHTML = `
                <div class="text-center py-5 px-4 fade-in">
                    <i class="bi bi-calendar-x text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h6 class="fw-semibold text-dark mb-1">Tidak Ada Data</h6>
                    <p class="text-muted small mb-0">Tidak ada catatan presensi pada periode ini.</p>
                </div>`;
            return;
        }

        let rows = '';
        data.rows.forEach((item, i) => {
            const no      = data.from + i;
            const tgl     = item.tgl_formatted;
            const hari    = item.hari_formatted;
            const jam     = item.jam_masuk
                ? `<span class="d-inline-flex align-items-center gap-1 fw-semibold text-dark">
                       <i class="bi bi-arrow-right-circle-fill text-success" style="font-size:.85rem"></i>${item.jam_masuk}
                   </span>`
                : `<span class="text-muted">—</span>`;
            const ket     = item.keterangan || '—';

            rows += `
            <tr>
                <td class="ps-4 text-muted small">${no}</td>
                <td class="py-3">
                    <span class="fw-semibold text-dark">${tgl}</span><br>
                    <span class="text-muted small">${hari}</span>
                </td>
                <td class="py-3">${jam}</td>
                <td class="py-3">${badgeStatus(item.status)}</td>
                <td class="py-3">${statusAwal(item)}</td>
                <td class="pe-4 py-3 text-muted small">${ket}</td>
            </tr>`;
        });

        let paginasi = '';
        if (data.last_page > 1) {
            paginasi = `<div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">Menampilkan ${data.from}–${data.to} dari ${data.total} data</span>
                <ul class="pagination pagination-sm mb-0">${buildPagination(data)}</ul>
            </div>`;
        }

        wrap.innerHTML = `
            <div class="table-responsive fade-in">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-header-row">
                            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:5%">#</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:24%">
                                <i class="bi bi-calendar-date me-1"></i>Tanggal
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:18%">
                                <i class="bi bi-clock me-1"></i>Jam Masuk
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:18%">
                                <i class="bi bi-tag me-1"></i>Status
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:18%">
                                <i class="bi bi-pencil-square me-1"></i>Status Awal
                            </th>
                            <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase">
                                <i class="bi bi-chat-left-text me-1"></i>Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>${paginasi}`;

        // Event listener untuk tombol pagination yang baru dirender
        wrap.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                fetchData(this.dataset.page);
            });
        });
    }

    // ── Build pagination HTML ───────────────────────────────────────
    function buildPagination(data) {
        let html = '';
        const cur  = data.current_page;
        const last = data.last_page;

        // Prev
        html += `<li class="page-item ${cur === 1 ? 'disabled' : ''}">
            <button class="page-link page-btn" data-page="${cur - 1}">
                <i class="bi bi-chevron-left"></i>
            </button></li>`;

        // Halaman
        for (let p = 1; p <= last; p++) {
            if (p === 1 || p === last || (p >= cur - 1 && p <= cur + 1)) {
                html += `<li class="page-item ${p === cur ? 'active' : ''}">
                    <button class="page-link page-btn" data-page="${p}">${p}</button></li>`;
            } else if (p === cur - 2 || p === cur + 2) {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${cur === last ? 'disabled' : ''}">
            <button class="page-link page-btn" data-page="${cur + 1}">
                <i class="bi bi-chevron-right"></i>
            </button></li>`;

        return html;
    }

    // ── Event listeners filter ──────────────────────────────────────
    filterBulan.addEventListener('change', () => fetchData());
    filterTahun.addEventListener('change', () => fetchData());

    btnReset.addEventListener('click', function () {
        filterBulan.value = '{{ now()->month }}';
        filterTahun.value = '{{ now()->year }}';
        fetchData();
    });

    // Load data pertama kali
    fetchData();
});
</script>
@endsection
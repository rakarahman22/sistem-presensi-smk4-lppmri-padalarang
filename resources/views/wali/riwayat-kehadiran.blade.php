@extends('layouts.app')

@section('title', 'Riwayat Kehadiran Anak | SMK 4 LPPM RI')

@section('content')
    <style>
        /* ===== STATUS BADGE ===== */
        .status-hadir     { background: #dcfce7; color: #15803d; }
        .status-terlambat { background: #fef3c7; color: #92400e; }
        .status-izin      { background: #e0f2fe; color: #0369a1; }
        .status-sakit     { background: #ede9fe; color: #6d28d9; }
        .status-alpha,
        .status-alpa      { background: #fee2e2; color: #b91c1c; }

        .badge-status {
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        /* ===== KOREKSI BADGE ===== */
        .koreksi-wrap {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .koreksi-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
        }
        .koreksi-old {
            text-decoration: line-through;
            opacity: 0.6;
            font-weight: 400;
        }
        .koreksi-by {
            font-size: 10px;
            color: #94a3b8;
        }

        /* ===== PROGRESS BAR ===== */
        .pct-bar-wrap {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .pct-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .pct-label   { font-size: 12px; color: #64748b; }
        .pct-number  { font-size: 20px; font-weight: 700; color: #15803d; }
        .pct-track   { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
        .pct-fill    { height: 100%; border-radius: 4px; background: #22c55e; transition: width 0.6s ease; }
        .pct-legend  { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 8px; }
        .pct-legend span { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #64748b; }
        .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

        /* ===== REKAP CARDS ===== */
        .rekap-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }
        .rekap-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .rekap-num  { font-size: 22px; font-weight: 700; }
        .rekap-lbl  { font-size: 10px; color: #64748b; margin-top: 2px; }

        /* ===== TABEL ===== */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        table td {
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        table tr:last-child td { border-bottom: none; }
        table tr:hover { background-color: #f9fafb; }

        .td-hari   { font-size: 12px; color: #64748b; }
        .verify-ok { color: #22c55e; font-weight: 600; font-size: 11px; }
        .security-note { font-size: 11px; color: #94a3b8; font-style: italic; }

        /* ===== FILTER TOGGLE ===== */
        #filter-tanggal-wrap { display: none; }
        #filter-tanggal-wrap.show { display: flex; }

        /* ===== LOADING INDICATOR ===== */
        .filter-form { position: relative; }
        .filter-form.loading { opacity: 0.6; pointer-events: none; }

        #filter-status-bar {
            display: none;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #64748b;
            margin-top: 8px;
        }
        #filter-status-bar.show { display: flex; }

        .spinner-sm {
            width: 13px; height: 13px;
            border: 2px solid #d1fae5;
            border-top-color: #15803d;
            border-radius: 50%;
            animation: spin-sm 0.55s linear infinite;
            flex-shrink: 0;
        }
        @keyframes spin-sm { to { transform: rotate(360deg); } }
    </style>

    {{-- Topbar --}}
    <div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:14px 28px; display:flex; align-items:center; justify-content:space-between; margin:-2.5rem -2.5rem 2rem -2.5rem;">
        <div>
            <div style="font-size:16px; font-weight:600; color:#1e293b;">Riwayat Kehadiran Anak</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:1px;">{{ now()->translatedFormat('l, d F Y') }}</div>
        </div>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
            <i class="bi bi-person-hearts me-1"></i> Wali Siswa
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== FORM FILTER ===== --}}
    <form method="GET" action="{{ route('wali.riwayat-kehadiran') }}"
          class="bg-white rounded-3 shadow-sm p-3 mb-4 border filter-form"
          id="filterForm">
        <div class="row g-3 align-items-end">

            {{-- Nama Anak --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold" style="font-size:12px;">Nama Anak</label>
                <select name="id_siswa" class="form-select form-select-sm auto-submit">
                    <option value="">-- Semua Anak --</option>
                    @foreach ($siswaList as $s)
                        <option value="{{ $s->id_siswa }}" {{ $idSiswaFilter == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama_siswa }} — {{ $s->kelas->nama_kelas ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Bulan --}}
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">Bulan</label>
                <select name="bulan" class="form-select form-select-sm auto-submit">
                    @foreach (range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">Tahun</label>
                <select name="tahun" class="form-select form-select-sm auto-submit">
                    @foreach (range(now()->year, now()->year - 3) as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Toggle filter tanggal spesifik --}}
            <div class="col-12 col-md-auto d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-tanggal">
                    <i class="bi bi-calendar3 me-1"></i> Tanggal Spesifik
                </button>
            </div>
        </div>

        {{-- Status bar loading --}}
        <div id="filter-status-bar">
            <span class="spinner-sm"></span>
            <span>Memuat data...</span>
        </div>

        {{-- Filter tanggal spesifik (tersembunyi by default) --}}
        <div class="row g-3 align-items-end mt-1 {{ ($tglDari || $tglSampai) ? 'show' : '' }}" id="filter-tanggal-wrap">
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" name="tgl_dari" id="input-tgl-dari"
                       class="form-control form-control-sm date-auto-submit"
                       value="{{ $tglDari ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">Sampai Tanggal</label>
                <input type="date" name="tgl_sampai" id="input-tgl-sampai"
                       class="form-control form-control-sm date-auto-submit"
                       value="{{ $tglSampai ?? '' }}">
            </div>
            <div class="col-12 col-md-3">
                <small class="text-muted" style="font-size:11px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Filter tanggal akan mengabaikan filter bulan/tahun.
                </small>
            </div>
        </div>
    </form>

    {{-- ===== INFO SISWA ===== --}}
    @if ($idSiswaFilter)
        @php $siswaAktif = $siswaList->firstWhere('id_siswa', $idSiswaFilter); @endphp
        @if ($siswaAktif)
            <div class="d-flex align-items-center gap-3 mb-3 bg-white p-3 rounded-3 border shadow-sm">
                <div style="width:44px;height:44px;background:#15803d;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;">
                    {{ strtoupper(substr($siswaAktif->nama_siswa, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:15px;">{{ $siswaAktif->nama_siswa }}</div>
                    <div class="text-muted" style="font-size:12px;">
                        {{ $siswaAktif->kelas->nama_kelas ?? '-' }} &nbsp;·&nbsp; NIS: {{ $siswaAktif->nis ?? '-' }}
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ===== PROGRESS BAR + REKAP ===== --}}
    @if ($riwayat->count() > 0)
        @php
            $cHadir     = $riwayat->where('status', 'Hadir')->count();
            $cTerlambat = $riwayat->where('status', 'Terlambat')->count();
            $cSakit     = $riwayat->where('status', 'Sakit')->count();
            $cIzin      = $riwayat->where('status', 'Izin')->count();
            $cAlpha     = $riwayat->whereIn('status', ['Alpa', 'Alpha'])->count();

            $totalHari  = $riwayat->count();
            $persentase = $totalHari > 0 ? round((($cHadir + $cTerlambat) / $totalHari) * 100) : 0;
        @endphp

        {{-- Progress bar --}}
        <div class="pct-bar-wrap">
            <div class="pct-top">
                <span class="pct-label">
                    Persentase kehadiran
                    @if ($tglDari && $tglSampai)
                        — {{ \Carbon\Carbon::parse($tglDari)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($tglSampai)->translatedFormat('d M Y') }}
                    @else
                        — {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    @endif
                </span>
                <span class="pct-number">{{ $persentase }}%</span>
            </div>
            <div class="pct-track">
                <div class="pct-fill" style="width: {{ $persentase }}%;"></div>
            </div>
            <div class="pct-legend">
                <span><span class="dot" style="background:#22c55e;"></span> {{ $cHadir }} hadir</span>
                <span><span class="dot" style="background:#f59e0b;"></span> {{ $cTerlambat }} terlambat</span>
                <span><span class="dot" style="background:#6d28d9;"></span> {{ $cSakit }} sakit</span>
                <span><span class="dot" style="background:#0369a1;"></span> {{ $cIzin }} izin</span>
                <span><span class="dot" style="background:#ef4444;"></span> {{ $cAlpha }} alpha</span>
                <span style="margin-left:auto; font-weight:600; color:#1e293b;">Total: {{ $totalHari }} hari</span>
            </div>
        </div>

        {{-- Rekap cards --}}
        <div class="rekap-grid mb-3">
            <div class="rekap-card">
                <div class="rekap-num" style="color:#15803d;">{{ $cHadir }}</div>
                <div class="rekap-lbl">Hadir</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-num" style="color:#92400e;">{{ $cTerlambat }}</div>
                <div class="rekap-lbl">Terlambat</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-num" style="color:#6d28d9;">{{ $cSakit }}</div>
                <div class="rekap-lbl">Sakit</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-num" style="color:#0369a1;">{{ $cIzin }}</div>
                <div class="rekap-lbl">Izin</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-num" style="color:#b91c1c;">{{ $cAlpha }}</div>
                <div class="rekap-lbl">Alpha</div>
            </div>
        </div>
    @endif

    {{-- ===== TABEL RIWAYAT ===== --}}
    <div class="table-container mb-3 border">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:110px;">Tanggal</th>
                    <th class="text-center" style="width:80px;">Hari</th>
                    @if (!$idSiswaFilter)
                        <th class="text-center">Nama Anak</th>
                    @endif
                    <th class="text-center" style="width:100px;">Jam Masuk</th>
                    <th class="text-center" style="width:100px;">Status</th>
                    <th class="text-center" style="width:130px;">Verifikasi Lokasi</th>
                    <th class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $p)
                <tr>
                    {{-- Tanggal --}}
                    <td class="text-center fw-semibold" style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($p->tgl_presensi)->translatedFormat('d F Y') }}
                    </td>

                    {{-- Hari --}}
                    <td class="text-center td-hari">
                        {{ \Carbon\Carbon::parse($p->tgl_presensi)->translatedFormat('l') }}
                    </td>

                    @if (!$idSiswaFilter)
                        <td class="text-center fw-semibold">{{ $p->nama_siswa }}</td>
                    @endif

                    {{-- Jam masuk --}}
                    <td class="text-center">
                        @if ($p->jam_masuk)
                            <strong>{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }} WIB</strong>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        <span class="badge-status status-{{ strtolower($p->status) }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>

                    {{-- Verifikasi lokasi --}}
                    <td class="text-center">
                        @if (in_array(strtolower($p->status), ['hadir', 'terlambat']))
                            <span class="verify-ok">
                                <i class="bi bi-geo-alt-fill me-1"></i> Area Sekolah
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Keterangan — tampilkan badge koreksi jika ada --}}
                    <td class="text-center">
                        @if (!empty($p->status_awal) && $p->status_awal !== $p->status)
                            <div class="koreksi-wrap">
                                <span class="koreksi-badge">
                                    <i class="bi bi-pencil-fill" style="font-size:9px;"></i>
                                    <span class="koreksi-old">{{ ucfirst($p->status_awal) }}</span>
                                    → {{ ucfirst($p->status) }}
                                </span>
                                @if (!empty($p->dikoreksi_oleh))
                                    <span class="koreksi-by">oleh {{ $p->dikoreksi_oleh }}</span>
                                @endif
                            </div>
                        @elseif (!empty($p->keterangan))
                            <span class="text-muted" style="font-size:12px;">{{ $p->keterangan }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $idSiswaFilter ? 6 : 7 }}" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x" style="font-size:36px; opacity:0.3; display:block; margin-bottom:8px;"></i>
                        Tidak ada data presensi untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="security-note">
        * Waktu presensi diambil secara otomatis berdasarkan sensor perangkat siswa saat berada di radius sekolah SMK 4 LPPM RI Padalarang.
    </p>

    <script>
        const form          = document.getElementById('filterForm');
        const statusBar     = document.getElementById('filter-status-bar');
        const btnToggle     = document.getElementById('btn-toggle-tanggal');
        const filterWrap    = document.getElementById('filter-tanggal-wrap');
        const inputTglDari  = document.getElementById('input-tgl-dari');
        const inputTglSampai= document.getElementById('input-tgl-sampai');

        // ── Helper: tampilkan loading lalu submit ─────────────────
        function submitForm() {
            statusBar.classList.add('show');
            form.classList.add('loading');
            form.submit();
        }

        // ── Select (nama anak, bulan, tahun): langsung submit ─────
        document.querySelectorAll('.auto-submit').forEach(function (el) {
            el.addEventListener('change', submitForm);
        });

        // ── Input tanggal: submit setelah keduanya terisi, dengan debounce 600ms
        // Ini mencegah submit di tengah-tengah user belum selesai pilih tanggal akhir.
        let debounceTimer = null;

        function handleDateChange() {
            const dari    = inputTglDari.value;
            const sampai  = inputTglSampai.value;

            // Kalau salah satu dikosongkan, submit langsung (reset ke filter bulan/tahun)
            if (!dari && !sampai) {
                clearTimeout(debounceTimer);
                submitForm();
                return;
            }

            // Kalau keduanya sudah diisi, tunggu 600ms baru submit
            if (dari && sampai) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(submitForm, 600);
                return;
            }

            // Kalau baru satu yang diisi, belum perlu submit
        }

        document.querySelectorAll('.date-auto-submit').forEach(function (el) {
            el.addEventListener('change', handleDateChange);
        });

        // ── Toggle filter tanggal spesifik ────────────────────────
        if (btnToggle && filterWrap) {
            btnToggle.addEventListener('click', function () {
                filterWrap.classList.toggle('show');
                btnToggle.classList.toggle('btn-outline-secondary');
                btnToggle.classList.toggle('btn-secondary');
            });

            @if ($tglDari || $tglSampai)
                filterWrap.classList.add('show');
                btnToggle.classList.remove('btn-outline-secondary');
                btnToggle.classList.add('btn-secondary');
            @endif
        }
    </script>
@endsection
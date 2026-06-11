@extends('layouts.app')

@section('title', $readOnly ? 'Detail Absensi Pertemuan Lalu' : 'Lembar Absensi Kelas')

@section('content')
@php
    $jmlHadir = $presensiSiswa->where('status', 'Hadir')->count();
    $jmlSakit = $presensiSiswa->where('status', 'Sakit')->count();
    $jmlIzin  = $presensiSiswa->where('status', 'Izin')->count();
    $jmlAlpa  = $presensiSiswa->where('status', 'Alpa')->count();
    $total    = $presensiSiswa->count();
@endphp

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('guru.absen-mapel.index') }}"
                   class="btn btn-sm btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                @if($readOnly)
                    <span class="badge rounded-pill px-3 py-1"
                          style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;font-size:.72rem;">
                        <i class="bi bi-eye me-1"></i>Mode Lihat
                    </span>
                @else
                    <span class="badge rounded-pill px-3 py-1"
                          style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;font-size:.72rem;">
                        <i class="bi bi-record-circle-fill me-1"></i>Sesi Aktif
                    </span>
                @endif
            </div>
            <h4 class="fw-bold mb-0 text-dark">{{ $sesi->nama_mapel }}</h4>
            <p class="text-muted small mb-0 mt-1">
                Kelas <strong>{{ $sesi->kelas->tingkat }} {{ $sesi->kelas->nama_kelas }}</strong>
                &nbsp;·&nbsp; Pertemuan ke-<strong>{{ $pertemuanKe }}</strong>
                &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($sesi->tgl_mengajar)->translatedFormat('l, d F Y') }}
                &nbsp;·&nbsp; Mulai <strong>{{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}</strong> WIB
            </p>
        </div>
    </div>

    {{-- ===== BANNER READONLY ===== --}}
    @if($readOnly)
    <div class="alert border-0 rounded-3 d-flex align-items-center gap-2 mb-4 py-2"
         style="background:#f8fafc;border:1px solid #e2e8f0!important;font-size:.85rem;">
        <i class="bi bi-lock-fill text-muted"></i>
        <span class="text-muted">
            Data absensi pertemuan lalu <strong class="text-dark">tidak dapat diubah</strong>.
            Anda hanya dapat melihat catatan kehadiran pada sesi ini.
        </span>
    </div>
    @endif

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4 py-2">
        <i class="bi bi-check-circle-fill text-success"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="absen-stat-card rounded-4 p-2 p-md-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-2">
                    <div class="absen-icon" style="background:#dcfce7;color:#16a34a;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="absen-num" style="color:#166534;">{{ $jmlHadir }}</div>
                        <div class="absen-lbl" style="color:#15803d;">Hadir</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="absen-stat-card rounded-4 p-2 p-md-3" style="background:#fffbeb;border:1px solid #fde68a;">
                <div class="d-flex align-items-center gap-2">
                    <div class="absen-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="absen-num" style="color:#92400e;">{{ $jmlSakit }}</div>
                        <div class="absen-lbl" style="color:#b45309;">Sakit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="absen-stat-card rounded-4 p-2 p-md-3" style="background:#eff6ff;border:1px solid #bfdbfe;">
                <div class="d-flex align-items-center gap-2">
                    <div class="absen-icon" style="background:#dbeafe;color:#2563eb;">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="absen-num" style="color:#1e3a8a;">{{ $jmlIzin }}</div>
                        <div class="absen-lbl" style="color:#1d4ed8;">Izin</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="absen-stat-card rounded-4 p-2 p-md-3" style="background:#fff1f2;border:1px solid #fecdd3;">
                <div class="d-flex align-items-center gap-2">
                    <div class="absen-icon" style="background:#ffe4e6;color:#e11d48;">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="absen-num" style="color:#9f1239;">{{ $jmlAlpa }}</div>
                        <div class="absen-lbl" style="color:#be123c;">Alpa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL ABSENSI ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        {{-- Header card --}}
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Daftar Kehadiran Siswa
                </h6>
                <p class="text-muted small mb-0 mt-1">
                    {{ $total }} siswa terdaftar
                    @if(!$readOnly)
                        &nbsp;·&nbsp; <span class="text-primary fw-semibold" id="sudahDiisiLabel">0 sudah diisi</span>
                    @endif
                </p>
            </div>
            @if(!$readOnly)
            <button type="button" onclick="setSemuaHadir()"
                    class="btn btn-sm btn-outline-success rounded-3 px-3">
                <i class="bi bi-check2-all me-1"></i>Set Semua Hadir
            </button>
            @endif
        </div>

        {{-- Form + tabel --}}
        <form action="{{ route('guru.absen-mapel.simpan', $sesi->id_mengajar) }}"
              method="POST" id="formAbsensi">
            @csrf

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelAbsensi">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e9ecef;">
                            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:5%">#</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:14%">NIS</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">Nama Siswa</th>
                            <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase text-center"
                                style="width:{{ $readOnly ? '14%' : '36%' }}">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensiSiswa as $index => $row)
                        <tr class="absen-row" data-id="{{ $row->id_presensi_mapel }}">
                            <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                            <td class="text-muted small">{{ $row->siswa->nis ?? '-' }}</td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="mini-avatar">
                                        {{ strtoupper(substr($row->siswa->nama_siswa ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-dark small">{{ $row->siswa->nama_siswa }}</span>
                                </div>
                            </td>
                            <td class="pe-4 py-3 text-center">
                                @if($readOnly)
                                    {{-- ── Readonly: badge status saja ── --}}
                                    @php
                                        $badgeStyle = [
                                            'Hadir' => 'background:#dcfce7;color:#166534;',
                                            'Sakit' => 'background:#fef3c7;color:#92400e;',
                                            'Izin'  => 'background:#dbeafe;color:#1e3a8a;',
                                            'Alpa'  => 'background:#ffe4e6;color:#9f1239;',
                                        ];
                                        $badgeIcon = [
                                            'Hadir' => 'bi-check-circle-fill',
                                            'Sakit' => 'bi-heart-pulse-fill',
                                            'Izin'  => 'bi-file-earmark-text-fill',
                                            'Alpa'  => 'bi-x-circle-fill',
                                        ];
                                        $st = $row->status;
                                    @endphp
                                    <span class="status-badge-ro"
                                          style="{{ $badgeStyle[$st] ?? 'background:#f1f5f9;color:#64748b;' }}">
                                        <i class="bi {{ $badgeIcon[$st] ?? 'bi-dash' }} me-1"></i>{{ $st ?? '—' }}
                                    </span>

                                @else
                                    {{-- ── Edit: radio toggle H / S / I / A ── --}}
                                    <div class="d-flex justify-content-center gap-1 gap-md-2">
                                        @foreach([
                                            'Hadir' => ['#dcfce7','#166534','#bbf7d0','H'],
                                            'Sakit' => ['#fef3c7','#92400e','#fde68a','S'],
                                            'Izin'  => ['#dbeafe','#1e3a8a','#bfdbfe','I'],
                                            'Alpa'  => ['#ffe4e6','#9f1239','#fecdd3','A'],
                                        ] as $val => [$bg, $clr, $border, $lbl])
                                        <label class="radio-toggle {{ $row->status === $val ? 'active' : '' }}"
                                               style="--bg:{{ $bg }};--clr:{{ $clr }};--border:{{ $border }};"
                                               title="{{ $val }}">
                                            <input type="radio"
                                                   name="status[{{ $row->id_presensi_mapel }}]"
                                                   value="{{ $val }}"
                                                   {{ $row->status === $val ? 'checked' : '' }}
                                                   class="radio-input">
                                            {{ $lbl }}
                                        </label>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer aksi --}}
            @if(!$readOnly)
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:#fafbfc;">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Pastikan semua siswa sudah terisi sebelum menyimpan.
                </span>
                <button type="submit"
                        class="btn btn-primary fw-semibold rounded-3 px-4 py-2 shadow-sm">
                    <i class="bi bi-cloud-check-fill me-2"></i>Simpan Hasil Absensi
                </button>
            </div>
            @endif

        </form>
    </div>

</div>

{{-- ===== STYLES ===== --}}
<style>
    /* Stat cards */
    .absen-stat-card { border: 1px solid transparent; transition: transform .15s ease, box-shadow .15s ease; }
    .absen-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08) !important; }
    .absen-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .absen-num  { font-size:1.25rem;font-weight:700;line-height:1; }
    .absen-lbl  { font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:2px; }
    @media (min-width:768px) {
        .absen-icon { width:42px;height:42px;border-radius:12px;font-size:1.1rem; }
        .absen-num  { font-size:1.5rem; }
        .absen-lbl  { font-size:.7rem; }
    }
    .min-w-0 { min-width:0;overflow:hidden; }

    /* Table */
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f8fafc; }

    /* Mini avatar */
    .mini-avatar {
        width:30px;height:30px;border-radius:50%;
        background:linear-gradient(135deg,#6366f1,#3b82f6);
        color:#fff;font-size:.75rem;font-weight:700;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
    }

    /* Readonly badge */
    .status-badge-ro {
        display:inline-flex;align-items:center;
        padding:.3rem .75rem;border-radius:20px;
        font-size:.78rem;font-weight:600;white-space:nowrap;
    }

    /* Radio toggle button */
    .radio-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px; height: 34px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #94a3b8;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
        user-select: none;
    }
    .radio-toggle:hover {
        border-color: var(--border);
        background: var(--bg);
        color: var(--clr);
    }
    .radio-toggle.active {
        border-color: var(--border);
        background: var(--bg);
        color: var(--clr);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--bg) 60%, transparent);
    }
    .radio-toggle input { display: none; }

    @media (min-width: 768px) {
        .radio-toggle { width: 44px; height: 38px; font-size: .85rem; border-radius: 10px; }
    }
</style>

{{-- ===== SCRIPTS ===== --}}
@if(!$readOnly)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = document.querySelectorAll('.radio-toggle');
    const sudahDiisiLabel = document.getElementById('sudahDiisiLabel');

    // Toggle active class saat radio diklik
    labels.forEach(label => {
        label.addEventListener('click', function () {
            const input = this.querySelector('input');
            const name  = input.name;

            // Nonaktifkan semua label di baris yang sama
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                r.closest('.radio-toggle').classList.remove('active');
            });

            this.classList.add('active');
            updateCounter();
        });
    });

    // Counter siswa sudah diisi
    function updateCounter() {
        const rows   = document.querySelectorAll('.absen-row');
        let sudah    = 0;
        rows.forEach(row => {
            const checked = row.querySelector('input[type="radio"]:checked');
            if (checked) sudah++;
        });
        sudahDiisiLabel.textContent = `${sudah} sudah diisi`;
        sudahDiisiLabel.style.color = sudah === rows.length ? '#16a34a' : '#2563eb';
    }

    updateCounter(); // hitung saat pertama load

    // Set semua hadir
    window.setSemuaHadir = function () {
        document.querySelectorAll('input[type="radio"][value="Hadir"]').forEach(radio => {
            radio.checked = true;
            const label   = radio.closest('.radio-toggle');
            const name    = radio.name;

            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                r.closest('.radio-toggle').classList.remove('active');
            });
            label.classList.add('active');
        });
        updateCounter();
    };
});
</script>
@endpush
@endif
@endsection
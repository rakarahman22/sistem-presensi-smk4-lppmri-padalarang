@extends('layouts.app')

@section('title', 'Hasil Rekap Presensi - {{ $namaMapel }}')

@section('content')
@php
    $totalSiswa     = $rekapSiswa->count();
    $memenuhi       = $rekapSiswa->filter(fn($s) =>
                        $totalPertemuan > 0 && round(($s->total_hadir / $totalPertemuan) * 100, 1) >= 75
                      )->count();
    $perhatian      = $rekapSiswa->filter(fn($s) =>
                        $totalPertemuan > 0 &&
                        round(($s->total_hadir / $totalPertemuan) * 100, 1) >= 50 &&
                        round(($s->total_hadir / $totalPertemuan) * 100, 1) < 75
                      )->count();
    $kritis         = $rekapSiswa->filter(fn($s) =>
                        $totalPertemuan > 0 && round(($s->total_hadir / $totalPertemuan) * 100, 1) < 50
                      )->count();
    $rataHadir      = $totalSiswa > 0 ? round($rekapSiswa->avg('total_hadir'), 1) : 0;
    $persenRata     = $totalPertemuan > 0 ? round(($rataHadir / $totalPertemuan) * 100, 1) : 0;
@endphp

<div class="container-fluid px-3 px-md-4 py-4" id="printArea">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 no-print-hide">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('guru.absen-mapel.rekap') }}?id_kelas={{ $kelas->id_kelas }}&nama_mapel={{ urlencode($namaMapel) }}"
                   class="btn btn-sm btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <span class="text-muted small">Rekap Presensi</span>
            </div>
            <h4 class="fw-bold mb-0 text-dark">{{ $namaMapel }}</h4>
            <p class="text-muted small mb-0 mt-1">
                Kelas <strong>{{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</strong>
                &nbsp;·&nbsp;
                <strong>{{ $totalPertemuan }}</strong> kali pertemuan tercatat
            </p>
        </div>
        <button onclick="cetakRekap()" class="btn btn-outline-primary rounded-3 no-print-hide">
            <i class="bi bi-printer me-1"></i>Cetak Rekap
        </button>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stat-card-rk p-2 p-md-3"
                 style="background:#eff6ff;border:1px solid #bfdbfe!important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rk-icon flex-shrink-0" style="background:#dbeafe;color:#2563eb;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="rk-num" style="color:#1e3a8a;">{{ $totalSiswa }}</div>
                        <div class="rk-lbl" style="color:#1d4ed8;">Total Siswa</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stat-card-rk p-2 p-md-3"
                 style="background:#f0fdf4;border:1px solid #bbf7d0!important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rk-icon flex-shrink-0" style="background:#dcfce7;color:#16a34a;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="rk-num" style="color:#166534;">{{ $memenuhi }}</div>
                        <div class="rk-lbl" style="color:#15803d;">Memenuhi ≥75%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stat-card-rk p-2 p-md-3"
                 style="background:#fffbeb;border:1px solid #fde68a!important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rk-icon flex-shrink-0" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="rk-num" style="color:#92400e;">{{ $perhatian }}</div>
                        <div class="rk-lbl" style="color:#b45309;">Perlu Perhatian</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stat-card-rk p-2 p-md-3"
                 style="background:#fff1f2;border:1px solid #fecdd3!important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rk-icon flex-shrink-0" style="background:#ffe4e6;color:#e11d48;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="rk-num" style="color:#9f1239;">{{ $kritis }}</div>
                        <div class="rk-lbl" style="color:#be123c;">Kritis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress rata-rata kehadiran kelas --}}
    <div class="card border-0 shadow-sm rounded-4 px-4 py-3 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-semibold text-dark">
                Rata-rata Kehadiran Kelas
            </span>
            <span class="fw-bold {{ $persenRata >= 75 ? 'text-success' : ($persenRata >= 50 ? 'text-warning' : 'text-danger') }}">
                {{ $persenRata }}%
            </span>
        </div>
        <div class="progress rounded-pill" style="height:10px;">
            <div class="progress-bar rounded-pill
                {{ $persenRata >= 75 ? 'bg-success' : ($persenRata >= 50 ? 'bg-warning' : 'bg-danger') }}"
                style="width:{{ $persenRata }}%">
            </div>
        </div>
        <div class="d-flex gap-3 mt-2 flex-wrap" style="font-size:.75rem;color:#64748b;">
            <span><span class="legend-dot bg-success"></span> Memenuhi ≥75%</span>
            <span><span class="legend-dot bg-warning"></span> Perlu Perhatian 50–74%</span>
            <span><span class="legend-dot bg-danger"></span> Kritis &lt;50%</span>
        </div>
    </div>

    {{-- ===== TABEL REKAP ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-table text-primary me-2"></i>Detail Kehadiran Siswa
            </h6>
            <p class="text-muted small mb-0 mt-1">
                H = Hadir &nbsp;·&nbsp; S = Sakit &nbsp;·&nbsp; I = Izin &nbsp;·&nbsp; A = Alpa
            </p>
        </div>

        <div class="table-responsive" id="tabelRekapCetak">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e9ecef;">
                        <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:4%">#</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:12%">NIS</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase">Nama Siswa</th>
                        <th class="py-3 text-center text-success fw-bold small text-uppercase" style="width:6%">H</th>
                        <th class="py-3 text-center text-warning fw-bold small text-uppercase" style="width:6%">S</th>
                        <th class="py-3 text-center text-info fw-bold small text-uppercase" style="width:6%">I</th>
                        <th class="py-3 text-center text-danger fw-bold small text-uppercase" style="width:6%">A</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:18%">Kehadiran</th>
                        <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase" style="width:12%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapSiswa as $index => $row)
                    @php
                        $persen = $totalPertemuan > 0
                            ? round(($row->total_hadir / $totalPertemuan) * 100, 1)
                            : 0;
                        $level = $persen >= 75 ? 'memenuhi' : ($persen >= 50 ? 'perhatian' : 'kritis');
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                        <td class="text-muted small">{{ $row->nis ?? '-' }}</td>
                        <td class="py-3 fw-semibold text-dark small">{{ $row->nama_siswa }}</td>
                        <td class="text-center fw-bold text-success">{{ $row->total_hadir }}</td>
                        <td class="text-center fw-bold text-warning">{{ $row->total_sakit }}</td>
                        <td class="text-center fw-bold text-info">{{ $row->total_izin }}</td>
                        <td class="text-center fw-bold text-danger">{{ $row->total_alpa }}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-fill rounded-pill" style="height:7px;">
                                    <div class="progress-bar rounded-pill
                                        {{ $level === 'memenuhi' ? 'bg-success' : ($level === 'perhatian' ? 'bg-warning' : 'bg-danger') }}"
                                        style="width:{{ $persen }}%">
                                    </div>
                                </div>
                                <span class="fw-bold small" style="min-width:38px;text-align:right;
                                    color:{{ $level === 'memenuhi' ? '#15803d' : ($level === 'perhatian' ? '#b45309' : '#dc2626') }}">
                                    {{ $persen }}%
                                </span>
                            </div>
                        </td>
                        <td class="pe-4 py-3">
                            @if($level === 'memenuhi')
                                <span class="status-badge" style="background:#dcfce7;color:#166534;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Memenuhi
                                </span>
                            @elseif($level === 'perhatian')
                                <span class="status-badge" style="background:#fef3c7;color:#92400e;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Perlu Perhatian
                                </span>
                            @else
                                <span class="status-badge" style="background:#ffe4e6;color:#9f1239;">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>Kritis
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            Tidak ada data siswa untuk kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($rekapSiswa->count() > 0)
                <tfoot>
                    <tr style="background:#f8fafc;border-top:2px solid #e9ecef;">
                        <td colspan="3" class="ps-4 py-3 text-muted fw-semibold small text-end">Total Keseluruhan</td>
                        <td class="text-center fw-bold text-success">{{ $rekapSiswa->sum('total_hadir') }}</td>
                        <td class="text-center fw-bold text-warning">{{ $rekapSiswa->sum('total_sakit') }}</td>
                        <td class="text-center fw-bold text-info">{{ $rekapSiswa->sum('total_izin') }}</td>
                        <td class="text-center fw-bold text-danger">{{ $rekapSiswa->sum('total_alpa') }}</td>
                        <td class="py-3">
                            <span class="fw-semibold small text-muted">Rata-rata: {{ $persenRata }}%</span>
                        </td>
                        <td class="pe-4">
                            <span class="small text-muted">{{ $memenuhi }}/{{ $totalSiswa }} memenuhi</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

<style>
    .stat-card-rk { transition:transform .15s ease; }
    .stat-card-rk:hover { transform:translateY(-2px); }
    .rk-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem; }
    .min-w-0 { min-width:0; overflow:hidden; }
    /* Desktop: ikon lebih besar */
    @media (min-width: 768px) {
        .rk-icon { width:42px;height:42px;border-radius:12px;font-size:1.1rem; }
    }
    .rk-num  { font-size:1.25rem;font-weight:700;line-height:1; }
    .rk-lbl  { font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    /* Desktop: angka lebih besar */
    @media (min-width: 768px) {
        .rk-num { font-size:1.5rem; }
        .rk-lbl { font-size:.7rem; }
    }

    .legend-dot { display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;vertical-align:middle; }

    .table > :not(caption) > * > * { border-bottom-color:#f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color:#f8fafc; }

    .status-badge {
        display:inline-flex;align-items:center;
        padding:.25rem .6rem;border-radius:20px;
        font-size:.72rem;font-weight:600;white-space:nowrap;
    }

    @media print {
        .no-print-hide { display: none !important; }
        nav, .btn { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        body { font-size: 11px; }
        .progress { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        .status-badge { border: 1px solid currentColor; }
    }
</style>

@push('scripts')
<script>
function cetakRekap() { window.print(); }
</script>
@endpush
@endsection
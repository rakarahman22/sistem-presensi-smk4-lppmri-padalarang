@extends('layouts.app')

@section('title', 'Dashboard Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ===== HEADER SELAMAT DATANG ===== --}}
    @php $siswa = Auth::guard('siswa')->user(); @endphp
    <div class="welcome-card rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                Halo, {{ $siswa->nama_siswa }}! ✨
            </h4>
            <p class="text-muted mb-0 small">
                NIS: <span class="fw-semibold">{{ $siswa->nis }}</span>
                &nbsp;|&nbsp;
                {{-- FIX: Ambil nama_kelas dari relasi, bukan objek langsung --}}
                Kelas: <span class="fw-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                &nbsp;|&nbsp;
                Jurusan: <span class="fw-semibold">{{ $siswa->kelas->jurusan ?? '-' }}</span>
                &nbsp;|&nbsp;
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">
                    <i class="bi bi-circle-fill me-1" style="font-size:.5rem;vertical-align:middle;"></i>Siswa Aktif
                </span>
            </p>
        </div>
        <div class="text-muted small text-end">
            <i class="bi bi-calendar3 me-1"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- ===== STAT CARDS PRESENSI ===== --}}
    <div class="row g-3 mb-4">

        {{-- Hadir --}}
        <div class="col-6 col-md-3">
            <div class="stat-card stat-hadir rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap hadir-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $totalHadir ?? 0 }}</div>
                    <div class="stat-label">Hadir</div>
                </div>
            </div>
        </div>

        {{-- Izin --}}
        <div class="col-6 col-md-3">
            <div class="stat-card stat-izin rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap izin-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $totalIzin ?? 0 }}</div>
                    <div class="stat-label">Izin</div>
                </div>
            </div>
        </div>

        {{-- Sakit --}}
        <div class="col-6 col-md-3">
            <div class="stat-card stat-sakit rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap sakit-icon">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $totalSakit ?? 0 }}</div>
                    <div class="stat-label">Sakit</div>
                </div>
            </div>
        </div>

        {{-- Alpa --}}
        <div class="col-6 col-md-3">
            <div class="stat-card stat-alpa rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrap alpa-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $totalAlpa ?? 0 }}</div>
                    <div class="stat-label">Alpa</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== TABEL RIWAYAT PRESENSI ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Presensi
                </h5>
                <p class="text-muted small mb-0 mt-1">Rekap kehadiran selama periode berjalan</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small">
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if(isset($riwayatPresensi) && $riwayatPresensi->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-header-row">
                            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:5%">#</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:22%">
                                <i class="bi bi-calendar-date me-1"></i>Tanggal
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:18%">
                                <i class="bi bi-clock me-1"></i>Jam Masuk
                            </th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase" style="width:17%">
                                <i class="bi bi-tag me-1"></i>Status
                            </th>
                            <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase">
                                <i class="bi bi-chat-left-text me-1"></i>Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatPresensi as $index => $presensi)
                        <tr>
                            <td class="ps-4 text-muted small">
                                {{-- Nomor urut tetap benar saat pagination --}}
                                {{ $riwayatPresensi->firstItem() + $index }}
                            </td>
                            <td class="py-3">
                                {{-- FIX: Kolom tanggal di model = tgl_presensi --}}
                                <span class="fw-semibold text-dark">
                                    {{ \Carbon\Carbon::parse($presensi->tgl_presensi)->translatedFormat('d M Y') }}
                                </span>
                                <br>
                                <span class="text-muted small">
                                    {{ \Carbon\Carbon::parse($presensi->tgl_presensi)->translatedFormat('l') }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($presensi->jam_masuk)
                                    <span class="d-inline-flex align-items-center gap-1 text-dark fw-semibold">
                                        <i class="bi bi-arrow-right-circle-fill text-success small"></i>
                                        {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @php $status = strtolower($presensi->status ?? ''); @endphp
                                @if($status === 'hadir')
                                    <span class="badge-status badge-hadir">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hadir
                                    </span>
                                @elseif($status === 'izin')
                                    <span class="badge-status badge-izin">
                                        <i class="bi bi-file-earmark-text-fill me-1"></i>Izin
                                    </span>
                                @elseif($status === 'sakit')
                                    <span class="badge-status badge-sakit">
                                        <i class="bi bi-heart-pulse-fill me-1"></i>Sakit
                                    </span>
                                @elseif($status === 'alpa')
                                    <span class="badge-status badge-alpa">
                                        <i class="bi bi-x-circle-fill me-1"></i>Alpa
                                    </span>
                                @elseif($status === 'terlambat')
                                    <span class="badge-status badge-terlambat">
                                        <i class="bi bi-clock-history me-1"></i>Terlambat
                                    </span>
                                @else
                                    <span class="badge-status badge-unknown">—</span>
                                @endif
                            </td>
                            <td class="pe-4 py-3 text-muted small">
                                {{ $presensi->keterangan ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($riwayatPresensi->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-end">
                {{ $riwayatPresensi->links() }}
            </div>
            @endif

            @else
            {{-- Empty State --}}
            <div class="text-center py-5 px-4">
                <div class="empty-icon mb-3">
                    <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
                </div>
                <h6 class="fw-semibold text-dark mb-1">Belum Ada Data Presensi</h6>
                <p class="text-muted small mb-0">Riwayat kehadiran Anda akan muncul di sini setelah presensi pertama.</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ===== STYLES ===== --}}
<style>
    .welcome-card {
        background: linear-gradient(135deg, #f8faff 0%, #eef3ff 100%);
        border: 1px solid #dde7ff;
    }
    .stat-card {
        border: 1px solid transparent;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.08) !important;
    }
    .stat-icon-wrap {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .stat-number { font-size: 1.6rem; font-weight: 700; line-height: 1; }
    .stat-label  { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-top: 2px; }

    .stat-hadir { background: #f0fdf4; border-color: #bbf7d0; }
    .stat-hadir .stat-number { color: #166534; }
    .stat-hadir .stat-label  { color: #15803d; }
    .hadir-icon { background: #dcfce7; color: #16a34a; }

    .stat-izin { background: #eff6ff; border-color: #bfdbfe; }
    .stat-izin .stat-number { color: #1e3a8a; }
    .stat-izin .stat-label  { color: #1d4ed8; }
    .izin-icon { background: #dbeafe; color: #2563eb; }

    .stat-sakit { background: #fffbeb; border-color: #fde68a; }
    .stat-sakit .stat-number { color: #92400e; }
    .stat-sakit .stat-label  { color: #b45309; }
    .sakit-icon { background: #fef3c7; color: #d97706; }

    .stat-alpa { background: #fff1f2; border-color: #fecdd3; }
    .stat-alpa .stat-number { color: #9f1239; }
    .stat-alpa .stat-label  { color: #be123c; }
    .alpa-icon { background: #ffe4e6; color: #e11d48; }

    .table-header-row { background-color: #f8fafc; border-bottom: 2px solid #e9ecef; }
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f8fafc; }

    .badge-status {
        display: inline-flex; align-items: center;
        padding: .28rem .65rem; border-radius: 20px;
        font-size: .75rem; font-weight: 600; white-space: nowrap;
    }
    .badge-hadir   { background: #dcfce7; color: #166534; }
    .badge-izin    { background: #dbeafe; color: #1e3a8a; }
    .badge-sakit   { background: #fef3c7; color: #92400e; }
    .badge-alpa      { background: #ffe4e6; color: #9f1239; }
    .badge-terlambat { background: #fff7ed; color: #9a3412; }
    .badge-unknown   { background: #f1f5f9; color: #64748b; }
</style>
@endsection
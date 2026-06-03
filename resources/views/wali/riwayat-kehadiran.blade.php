@extends('layouts.app')

@section('title', 'Riwayat Kehadiran Anak | SMK 4 LPPM RI')

@section('content')
    <style>
        /* ===== STATUS BADGE ===== */
        .status-hadir     { background: #dcfce7; color: #15803d; }
        .status-terlambat { background: #fef3c7; color: #92400e; }
        .status-izin      { background: #e0f2fe; color: #0369a1; }
        .status-sakit     { background: #ede9fe; color: #6d28d9; }
        .status-alpha     { background: #fee2e2; color: #b91c1c; }

        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        /* ===== TABEL ===== */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            border-bottom: 2px solid #edf2f7;
            font-size: 13px;
        }

        table td {
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        table tr:hover { background-color: #f9fafb; }
        .verify-box { color: #22c55e; font-weight: 600; font-size: 12px; }
        .security-note { font-size: 12px; color: #94a3b8; font-style: italic; }
    </style>

    {{-- Topbar --}}
    <div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:14px 28px; display:flex; align-items:center; justify-content:space-between; margin: -2.5rem -2.5rem 2rem -2.5rem;">
        <div>
            <div style="font-size:16px; font-weight:600; color:#1e293b;">Riwayat Kehadiran Anak</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:1px;">{{ now()->translatedFormat('l, d F Y') }}</div>
        </div>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
            <i class="bi bi-person-hearts me-1"></i> Wali Siswa
        </span>
    </div>

    {{-- Alert sukses/error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORM FILTER --}}
    <form method="GET" action="{{ route('wali.riwayat-kehadiran') }}" class="bg-white rounded-3 shadow-sm p-3 mb-4 border">
        <div class="row g-3 align-items-end">
            {{-- Filter Anak --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Nama Anak</label>
                <select name="id_siswa" class="form-select form-select-sm">
                    <option value="">-- Semua Anak --</option>
                    @foreach ($siswaList as $s)
                        <option value="{{ $s->id_siswa }}" {{ $idSiswaFilter == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama_siswa }} — {{ $s->kelas->nama_kelas ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan --}}
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    @foreach (range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    @foreach (range(now()->year, now()->year - 3) as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
            </div>
        </div>
    </form>

    {{-- INFO SISWA YANG DIPILIH --}}
    @if ($idSiswaFilter)
        @php $siswaAktif = $siswaList->firstWhere('id_siswa', $idSiswaFilter); @endphp
        @if ($siswaAktif)
            <div class="d-flex align-items-center gap-3 mb-3 bg-white p-3 rounded-3 border shadow-sm">
                <div style="width:44px; height:44px; background:#15803d; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; font-size:15px; flex-shrink:0;">
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

    {{-- REKAP SINGKAT --}}
    @if ($riwayat->count() > 0)
    <div class="row g-2 mb-4">
        @php
            $cHadir     = $riwayat->where('status', 'Hadir')->count();
            $cTerlambat = $riwayat->where('status', 'Terlambat')->count();
            $cSakit     = $riwayat->where('status', 'Sakit')->count();
            $cIzin      = $riwayat->where('status', 'Izin')->count();
            $cAlpha     = $riwayat->where('status', 'Alpa')->count();
        @endphp
        <div class="col-6 col-md">
            <div class="bg-white rounded-3 p-3 text-center shadow-sm border">
                <div style="font-size:22px; font-weight:700; color:#15803d;">{{ $cHadir }}</div>
                <div style="font-size:11px; color:#64748b;">Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="bg-white rounded-3 p-3 text-center shadow-sm border">
                <div style="font-size:22px; font-weight:700; color:#92400e;">{{ $cTerlambat }}</div>
                <div style="font-size:11px; color:#64748b;">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="bg-white rounded-3 p-3 text-center shadow-sm border">
                <div style="font-size:22px; font-weight:700; color:#6d28d9;">{{ $cSakit }}</div>
                <div style="font-size:11px; color:#64748b;">Sakit</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="bg-white rounded-3 p-3 text-center shadow-sm border">
                <div style="font-size:22px; font-weight:700; color:#0369a1;">{{ $cIzin }}</div>
                <div style="font-size:11px; color:#64748b;">Izin</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="bg-white rounded-3 p-3 text-center shadow-sm border">
                <div style="font-size:22px; font-weight:700; color:#b91c1c;">{{ $cAlpha }}</div>
                <div style="font-size:11px; color:#64748b;">Alpha</div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABEL RIWAYAT --}}
    <div class="table-container mb-3 border">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="text-center">Tanggal</th>
                    @if (!$idSiswaFilter)
                        <th class="text-center">Nama Anak</th>
                    @endif
                    <th class="text-center">Jam Presensi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Verifikasi Lokasi</th>
                    <th class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $p)
                <tr>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($p->tgl_presensi)->translatedFormat('d F Y') }}
                    </td>
                    @if (!$idSiswaFilter)
                        <td class="text-center fw-semibold">{{ $p->nama_siswa }}</td>
                    @endif
                    <td class="text-center">
                        @if ($p->jam_masuk)
                            <strong>{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }} WIB</strong>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-status status-{{ strtolower($p->status) }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if (strtolower($p->status) === 'hadir' || strtolower($p->status) === 'terlambat')
                            <span class="verify-box">
                                <i class="bi bi-geo-alt-fill me-1"></i> Area Sekolah
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center text-muted">
                        {{ $p->keterangan ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $idSiswaFilter ? 5 : 6 }}" class="text-center py-5 text-muted">
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
@endsection
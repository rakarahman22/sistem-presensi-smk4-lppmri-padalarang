@extends('layouts.app')

@section('title', 'Dashboard Wali Siswa | SMK 4 LPPM RI')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

  .db-wrap { font-family: 'Plus Jakarta Sans', sans-serif; }

  /* GREETING */
  .greeting-row {
    display: flex; justify-content: space-between;
    align-items: flex-start; margin-bottom: 20px;
    gap: 16px; flex-wrap: wrap;
  }
  .greeting-text h1 { font-size: 20px; font-weight: 700; color: #1f2937; line-height: 1.3; margin: 0; }
  .greeting-text p  { font-size: 13px; color: #6b7280; margin-top: 4px; }

  .action-btns { display: flex; gap: 8px; flex-wrap: wrap; flex-shrink: 0; }
  .btn-wa {
    display: inline-flex; align-items: center; gap: 6px;
    background: #25d366; color: white; border: none; border-radius: 8px;
    padding: 9px 16px; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: opacity .15s; font-family: inherit;
  }
  .btn-wa:hover { opacity: .85; color: white; }
  .btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    background: #1e40af; color: white; border: none; border-radius: 8px;
    padding: 9px 16px; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: opacity .15s; font-family: inherit;
  }
  .btn-primary:hover { opacity: .85; color: white; }

  /* ALERT */
  .alert-notif {
    background: #fff; border: 1px solid #e2e8f0;
    border-left: 4px solid #f59e0b; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 20px;
    display: flex; align-items: flex-start; gap: 12px;
  }
  .alert-icon { color: #f59e0b; font-size: 18px; flex-shrink: 0; margin-top: 1px; }
  .alert-label { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 2px; display: block; }
  .alert-body  { font-size: 13px; color: #4b5563; line-height: 1.5; }

  /* STATS */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px; margin-bottom: 20px;
  }
  .stat-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 18px 16px;
    position: relative; overflow: hidden;
  }
  .stat-card::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 3px;
  }
  .stat-card.s-blue::after  { background: #1e40af; }
  .stat-card.s-green::after { background: #22c55e; }
  .stat-card.s-yellow::after{ background: #f59e0b; }
  .stat-card.s-red::after   { background: #ef4444; }

  .stat-icon { font-size: 22px; margin-bottom: 10px; display: block; }
  .stat-card.s-blue  .stat-icon { color: #1e40af; }
  .stat-card.s-green .stat-icon { color: #22c55e; }
  .stat-card.s-yellow .stat-icon{ color: #f59e0b; }
  .stat-card.s-red   .stat-icon { color: #ef4444; }

  .stat-value { font-size: 30px; font-weight: 700; line-height: 1; margin-bottom: 4px; display: block; }
  .stat-card.s-blue  .stat-value { color: #1e40af; }
  .stat-card.s-green .stat-value { color: #22c55e; }
  .stat-card.s-yellow .stat-value{ color: #f59e0b; }
  .stat-card.s-red   .stat-value { color: #ef4444; }
  .stat-label { font-size: 12px; color: #6b7280; font-weight: 500; }

  /* PROGRESS */
  .progress-section {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 16px 20px; margin-bottom: 22px;
  }
  .progress-header {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 10px;
  }
  .progress-title { font-size: 13px; font-weight: 600; color: #1f2937; }
  .progress-pct   { font-size: 13px; font-weight: 700; color: #1e40af; }
  .progress-bar-bg {
    background: #e2e8f0; border-radius: 100px; height: 8px; overflow: hidden;
  }
  .progress-bar-fill {
    height: 100%; border-radius: 100px;
    background: linear-gradient(90deg, #1e40af, #3b82f6);
  }
  .progress-breakdown { display: flex; gap: 16px; margin-top: 10px; flex-wrap: wrap; }
  .pb-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #6b7280; }
  .pb-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  /* SECTION HEADER */
  .section-header {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap;
  }
  .section-title-text { font-size: 15px; font-weight: 700; color: #1f2937; }
  .section-count {
    font-size: 12px; background: #f1f5f9; color: #6b7280;
    padding: 2px 8px; border-radius: 100px;
    border: 1px solid #e2e8f0; font-weight: 500; margin-left: 6px;
  }

  /* TABLE */
  .table-wrap {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; overflow: hidden;
  }
  .db-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .db-table thead tr { border-bottom: 1px solid #e2e8f0; }
  .db-table thead th {
    padding: 11px 16px; text-align: left; font-size: 11px;
    font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: .05em;
    background: #f8fafc; white-space: nowrap;
  }
  .db-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
  .db-table tbody tr:last-child { border-bottom: none; }
  .db-table tbody tr:hover { background: #f8fafc; }
  .db-table td { padding: 12px 16px; color: #1f2937; vertical-align: middle; }

  .student-info { display: flex; align-items: center; gap: 10px; }
  .stu-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: #e0e7ff; color: #1e40af;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
  }
  .stu-name { font-weight: 600; font-size: 13px; color: #1f2937; }
  .stu-nis  { font-size: 11px; color: #9ca3af; font-family: monospace; }

  .badge-kelas {
    display: inline-block; background: #f1f5f9;
    border: 1px solid #e2e8f0; color: #6b7280;
    padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;
  }
  .badge-status {
    display: inline-block; padding: 3px 10px;
    border-radius: 100px; font-size: 11px; font-weight: 600;
  }
  .bs-hadir     { background: #dcfce7; color: #15803d; }
  .bs-alpha     { background: #fee2e2; color: #b91c1c; }
  .bs-izin      { background: #e0f2fe; color: #0369a1; }
  .bs-sakit     { background: #ede9fe; color: #6d28d9; }
  .bs-terlambat { background: #fef3c7; color: #92400e; }
  .bs-none      { background: #f1f5f9; color: #9ca3af; }

  .btn-detail {
    display: inline-flex; align-items: center; gap: 5px;
    background: transparent; border: 1px solid #d1d5db;
    color: #1e40af; border-radius: 100px;
    padding: 5px 14px; font-size: 12px; font-weight: 500;
    cursor: pointer; text-decoration: none; transition: background .15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .btn-detail:hover { background: #eff6ff; color: #1e40af; }

  /* EMPTY */
  .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
  .empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; }
  .empty-state p { font-size: 13px; }

  /* MOBILE CARDS */
  .mobile-cards { display: none; }
  .mob-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 14px 16px; margin-bottom: 10px;
  }
  .mob-card-header {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 8px;
  }
  .mob-card-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 0; border-top: 1px solid #f1f5f9; font-size: 12px;
  }
  .mob-label { color: #6b7280; }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 576px) {
    .greeting-row { flex-direction: column; }
    .greeting-text h1 { font-size: 17px; }
    .action-btns { width: 100%; }
    .action-btns .btn-wa,
    .action-btns .btn-primary { flex: 1; justify-content: center; font-size: 12px; padding: 8px 10px; }

    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .stat-value { font-size: 24px; }

    .table-wrap { display: none; }
    .mobile-cards { display: block; }
  }

  @media (min-width: 577px) and (max-width: 992px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .db-table th:nth-child(2),
    .db-table td:nth-child(2) { display: none; }
  }
</style>

@php
  $wali = Auth::guard('wali')->user();
  $namaWali = $wali->nama_wali;
  $initialsWali = collect(explode(' ', $namaWali))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');

  $totalSiswaAbsen = $totalHadir + $totalSakit + $totalIzin + $totalAlpha;
  $persentase = $totalSiswaAbsen > 0 ? round(($totalHadir / $totalSiswaAbsen) * 100) : 100;

  $avatarColors = [
    ['bg'=>'#e0e7ff','color'=>'#1e40af'],
    ['bg'=>'#fef9c3','color'=>'#713f12'],
    ['bg'=>'#ede9fe','color'=>'#5b21b6'],
    ['bg'=>'#dcfce7','color'=>'#15803d'],
    ['bg'=>'#fee2e2','color'=>'#b91c1c'],
  ];
@endphp

<div class="db-wrap">

  {{-- GREETING --}}
  <div class="greeting-row">
    <div class="greeting-text">
      <h1>Selamat Datang, {{ $namaWali }} 👋</h1>
      <p>Pantau kehadiran anak Anda secara berkala &middot; {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="action-btns">
      <a href="https://wa.me/628123456789" target="_blank" class="btn-wa">
        <i class="bi bi-whatsapp"></i> Hubungi Sekolah
      </a>
      <a href="{{ route('wali.riwayat-kehadiran') }}" class="btn-primary">
        <i class="bi bi-calendar3"></i> Semua Riwayat
      </a>
    </div>
  </div>

  {{-- NOTIFIKASI --}}
  <div class="alert-notif">
    <i class="bi bi-bell-fill alert-icon"></i>
    <div>
      <span class="alert-label">Pemberitahuan Wali Kelas</span>
      <span class="alert-body">
        Memasuki bulan {{ now()->translatedFormat('F Y') }}, mohon pastikan kehadiran anak Anda terpantau secara berkala melalui radius koordinat sekolah.
      </span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-grid">
    <div class="stat-card s-blue">
      <i class="bi bi-pie-chart-fill stat-icon"></i>
      <span class="stat-value">{{ $persentase }}%</span>
      <span class="stat-label">Persentase Kehadiran</span>
    </div>
    <div class="stat-card s-green">
      <i class="bi bi-check-circle-fill stat-icon"></i>
      <span class="stat-value">{{ $totalHadir }}</span>
      <span class="stat-label">Total Hadir</span>
    </div>
    <div class="stat-card s-yellow">
      <i class="bi bi-bandaid-fill stat-icon"></i>
      <span class="stat-value">{{ $totalSakit + $totalIzin }}</span>
      <span class="stat-label">Sakit / Izin</span>
    </div>
    <div class="stat-card s-red">
      <i class="bi bi-exclamation-circle-fill stat-icon"></i>
      <span class="stat-value">{{ $totalAlpha }}</span>
      <span class="stat-label">Total Alpha</span>
    </div>
  </div>

  {{-- PROGRESS --}}
  <div class="progress-section">
    <div class="progress-header">
      <span class="progress-title">
        <i class="bi bi-graph-up-arrow" style="color:#1e40af; margin-right:5px;"></i>
        Tingkat Kehadiran Bulan Ini
      </span>
      <span class="progress-pct">{{ $persentase }}%</span>
    </div>
    <div class="progress-bar-bg">
      <div class="progress-bar-fill" style="width:{{ $persentase }}%;"></div>
    </div>
    <div class="progress-breakdown">
      <div class="pb-item"><div class="pb-dot" style="background:#22c55e;"></div>Hadir: {{ $totalHadir }}</div>
      <div class="pb-item"><div class="pb-dot" style="background:#f59e0b;"></div>Sakit/Izin: {{ $totalSakit + $totalIzin }}</div>
      <div class="pb-item"><div class="pb-dot" style="background:#ef4444;"></div>Alpha: {{ $totalAlpha }}</div>
    </div>
  </div>

  {{-- SECTION HEADER --}}
  <div class="section-header">
    <div>
      <span class="section-title-text">Daftar Siswa Terhubung</span>
      <span class="section-count">{{ $siswaList->count() }} siswa</span>
    </div>
  </div>

  {{-- TABLE (desktop & tablet) --}}
  <div class="table-wrap">
    <table class="db-table">
      <thead>
        <tr>
          <th>Nama Anak</th>
          <th>NIS</th>
          <th>Kelas</th>
          <th>Status Hari Ini</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($siswaList as $siswa)
          @php
            $initials = collect(explode(' ', $siswa->nama_siswa))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
            $ac = $avatarColors[$loop->index % count($avatarColors)];

            $presensiHariIni = isset($siswa->presensi)
              ? $siswa->presensi->where('tanggal', today()->toDateString())->first()
              : null;
            $statusHariIni = $presensiHariIni->status ?? null;

            $badgeClass = match($statusHariIni) {
              'hadir'     => 'bs-hadir',
              'alpha'     => 'bs-alpha',
              'izin'      => 'bs-izin',
              'sakit'     => 'bs-sakit',
              'terlambat' => 'bs-terlambat',
              default     => 'bs-none',
            };
            $badgeLabel = match($statusHariIni) {
              'hadir'     => '<i class="bi bi-check-lg"></i> Hadir',
              'alpha'     => '<i class="bi bi-x-lg"></i> Alpha',
              'izin'      => '<i class="bi bi-calendar-event"></i> Izin',
              'sakit'     => '<i class="bi bi-bandaid"></i> Sakit',
              'terlambat' => '<i class="bi bi-clock"></i> Terlambat',
              default     => '— Belum Ada',
            };
          @endphp
          <tr>
            <td>
              <div class="student-info">
                <div class="stu-avatar" style="background:{{ $ac['bg'] }};color:{{ $ac['color'] }};">{{ $initials }}</div>
                <div>
                  <div class="stu-name">{{ $siswa->nama_siswa }}</div>
                  <div class="stu-nis">{{ $siswa->nis ?? '-' }}</div>
                </div>
              </div>
            </td>
            <td><code style="font-size:12px;color:#9ca3af;">{{ $siswa->nis ?? '-' }}</code></td>
            <td><span class="badge-kelas">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></td>
            <td><span class="badge-status {{ $badgeClass }}">{!! $badgeLabel !!}</span></td>
            <td style="text-align:center;">
              <a href="{{ route('wali.riwayat-kehadiran', ['id_siswa' => $siswa->id_siswa]) }}" class="btn-detail">
                <i class="bi bi-eye-fill"></i> Detail
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
              <div class="empty-state">
                <i class="bi bi-exclamation-circle"></i>
                <p>Belum ada data siswa yang ditautkan.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="mobile-cards">
    @forelse($siswaList as $siswa)
      @php
        $initials = collect(explode(' ', $siswa->nama_siswa))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
        $ac = $avatarColors[$loop->index % count($avatarColors)];

        $presensiHariIni = isset($siswa->presensi)
          ? $siswa->presensi->where('tanggal', today()->toDateString())->first()
          : null;
        $statusHariIni = $presensiHariIni->status ?? null;

        $badgeClass = match($statusHariIni) {
          'hadir' => 'bs-hadir', 'alpha' => 'bs-alpha',
          'izin'  => 'bs-izin',  'sakit' => 'bs-sakit',
          'terlambat' => 'bs-terlambat', default => 'bs-none',
        };
        $badgeLabel = match($statusHariIni) {
          'hadir' => 'Hadir', 'alpha' => 'Alpha',
          'izin'  => 'Izin',  'sakit' => 'Sakit',
          'terlambat' => 'Terlambat', default => 'Belum Ada',
        };
      @endphp
      <div class="mob-card">
        <div class="mob-card-header">
          <div class="student-info">
            <div class="stu-avatar" style="background:{{ $ac['bg'] }};color:{{ $ac['color'] }};">{{ $initials }}</div>
            <div>
              <div class="stu-name">{{ $siswa->nama_siswa }}</div>
              <div class="stu-nis">{{ $siswa->nis ?? '-' }}</div>
            </div>
          </div>
          <span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>
        <div class="mob-card-row">
          <span class="mob-label">Kelas</span>
          <span class="badge-kelas">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
        </div>
        <div class="mob-card-row">
          <span class="mob-label">Aksi</span>
          <a href="{{ route('wali.riwayat-kehadiran', ['id_siswa' => $siswa->id_siswa]) }}" class="btn-detail">
            <i class="bi bi-eye-fill"></i> Detail Presensi
          </a>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <i class="bi bi-exclamation-circle"></i>
        <p>Belum ada data siswa yang ditautkan.</p>
      </div>
    @endforelse
  </div>

</div>{{-- end .db-wrap --}}
@endsection
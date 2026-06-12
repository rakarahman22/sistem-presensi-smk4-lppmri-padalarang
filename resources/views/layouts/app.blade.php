<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMK 4 LPPM RI Padalarang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
            margin: 0;
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
            transition: transform .28s ease;
            overflow-y: auto;
        }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.sidebar-open { transform: translateX(0); }
        }

        /* ── OVERLAY ─────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 1040;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* ── TOPBAR ──────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0; right: 0; left: 260px;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            gap: 12px;
        }
        @media (max-width: 991.98px) {
            .topbar { left: 0; padding: 0 1rem; }
        }

        .topbar-left  { display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1; }
        .topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        /* Hamburger */
        .btn-hamburger {
            display: none;
            background: none; border: none;
            padding: 6px 8px; border-radius: 8px;
            color: #1e293b; font-size: 1.3rem;
            cursor: pointer; line-height: 1; flex-shrink: 0;
        }
        .btn-hamburger:hover { background: #f1f5f9; }
        @media (max-width: 991.98px) {
            .btn-hamburger { display: flex; align-items: center; }
        }

        /* Judul halaman di topbar */
        .topbar-page-title {
            font-size: .9rem; font-weight: 600;
            color: #1e293b;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Tanggal desktop */
        .topbar-date {
            font-size: .8rem; color: #94a3b8; white-space: nowrap;
        }
        @media (max-width: 575.98px) { .topbar-date { display: none !important; } }

        /* Profil pill */
        .navbar-profile-btn {
            display: flex; align-items: center; gap: .45rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: .28rem .65rem .28rem .28rem;
            text-decoration: none; color: #1e293b;
            font-size: .82rem; font-weight: 500;
            transition: all .15s ease; cursor: pointer;
            white-space: nowrap;
        }
        .navbar-profile-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }

        .navbar-profile-name { display: inline; }
        @media (max-width: 480px) {
            .navbar-profile-name { display: none; }
            .navbar-profile-btn  { padding: .28rem; border-radius: 50%; }
            .topbar-chevron      { display: none; }
        }

        /* Avatar */
        .navbar-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            color: #fff; font-size: .75rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .navbar-avatar.admin { background: linear-gradient(135deg,#1d4ed8,#3b82f6); }
        .navbar-avatar.guru  { background: linear-gradient(135deg,#15803d,#16a34a); }
        .navbar-avatar.wali  { background: linear-gradient(135deg,#7c3aed,#8b5cf6); }
        .navbar-avatar.siswa { background: linear-gradient(135deg,#6366f1,#818cf8); }

        /* ── SIDEBAR BRAND & NAV ─────────────────── */
        .sidebar-brand {
            font-size: 1.2rem; font-weight: 700; color: #14532d;
            padding: .25rem .75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9; margin-bottom: .75rem;
        }
        .nav-menu { display: flex; flex-direction: column; gap: .2rem; }
        .nav-link-custom {
            display: flex; align-items: center; gap: .7rem;
            padding: .65rem 1rem;
            color: #475569; font-weight: 500; font-size: .9rem;
            text-decoration: none; border-radius: 10px;
            transition: all .18s ease;
        }
        .nav-link-custom i { font-size: 1rem; flex-shrink: 0; }
        .nav-link-custom:hover  { background: #f1f5f9; color: #1e293b; }
        .nav-link-custom.active { background: #15803d; color: #fff; }

        .nav-group-label {
            font-size: .62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            color: #94a3b8; padding: .85rem 1rem .2rem;
            user-select: none;
        }

        .btn-logout-sidebar {
            display: flex; align-items: center; gap: .7rem;
            padding: .65rem 1rem;
            color: #dc2626; font-weight: 600; font-size: .9rem;
            background: none; border: none; width: 100%;
            text-align: left; border-radius: 10px;
            transition: all .18s ease; cursor: pointer;
        }
        .btn-logout-sidebar:hover { background: #fef2f2; }

        /* ── MAIN CONTENT ────────────────────────── */
        .main-content {
            margin-left: 260px;
            padding: 1.75rem 2rem;
            padding-top: calc(60px + 1.75rem);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        @media (max-width: 991.98px) {
            .main-content { margin-left: 0; padding: 1.25rem; padding-top: calc(60px + 1rem); }
        }
        @media (max-width: 575.98px) {
            .main-content { padding: .75rem; padding-top: calc(60px + .75rem); }
        }

        .main-content-body { flex: 1; }

        .main-footer {
            text-align: center; font-size: .75rem;
            color: #94a3b8; padding: 1rem 0 .5rem;
            border-top: 1px solid #f1f5f9; margin-top: 2rem;
        }

        /* ── DROPDOWN ────────────────────────────── */
        .dropdown-menu {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,.08) !important;
        }
        .dropdown-item {
            font-size: .85rem; border-radius: 8px;
            margin: 1px 4px; width: auto;
        }
        .dropdown-item:hover { background: #f1f5f9; }

        /* ── STAT CARDS ──────────────────────────── */
        .stat-card {
            background: #fff; border: none; border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
            border-left: 5px solid #10b981;
        }
        .stat-card.total-siswa   { border-left-color: #15803d; }
        .stat-card.siswa-hadir   { border-left-color: #eab308; }
        .stat-card.guru-aktif    { border-left-color: #3b82f6; }
        .stat-card.alpa-hari-ini { border-left-color: #ef4444; }
        .stat-label { font-size:.85rem; font-weight:600; color:#64748b; margin-bottom:.25rem; }
        .stat-value { font-size:1.75rem; font-weight:700; color:#0f172a; }

        .chart-card {
            background: #fff; border: none; border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04); padding: 1.5rem;
        }
    </style>

    @stack('styles')
</head>
<body>

@php
    $admin = Auth::guard('admin')->user();
    $guru  = Auth::guard('guru')->user();
    $wali  = Auth::guard('wali')->user();
    $siswa = Auth::guard('siswa')->user();

    $namaUser = $admin->nama_admin
             ?? $guru->nama_guru
             ?? $wali->nama_wali
             ?? $siswa->nama_siswa
             ?? 'User';

    $roleClass = $admin  ? 'admin'
               : ($guru  ? 'guru'
               : ($wali  ? 'wali' : 'siswa'));

    $profilRoute = match($roleClass) {
        'admin' => route('admin.profil'),
        'guru'  => route('guru.profil'),
        'wali'  => route('wali.profil'),
        'siswa' => route('siswa.profil'),
        default => '#',
    };

    $panelLabel = match($roleClass) {
        'admin' => 'Admin Panel',
        'guru'  => 'Guru Panel',
        'wali'  => 'Wali Panel',
        default => 'Siswa Panel',
    };
@endphp

{{-- OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- SIDEBAR --}}
@include('components.sidebar')

{{-- TOPBAR --}}
<header class="topbar">
    <div class="topbar-left">
        {{-- Hamburger (tablet & mobile) --}}
        <button class="btn-hamburger" id="btnHamburger" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>

        {{-- Brand label (tablet & mobile) --}}
        <span class="topbar-page-title d-lg-none">{{ $panelLabel }}</span>

        {{-- Tanggal (desktop) --}}
        <span class="topbar-date d-none d-lg-inline">
            <i class="bi bi-calendar3 me-1"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    <div class="topbar-right">
        <div class="dropdown">
            <button class="navbar-profile-btn dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false" type="button">
                <div class="navbar-avatar {{ $roleClass }}">
                    {{ strtoupper(substr($namaUser, 0, 1)) }}
                </div>
                <span class="navbar-profile-name">{{ $namaUser }}</span>
                <i class="bi bi-chevron-down text-muted topbar-chevron" style="font-size:.65rem;"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end rounded-3 mt-2" style="min-width:210px;">
                <li>
                    <div class="px-3 py-2 border-bottom mb-1">
                        <div class="fw-semibold text-dark" style="font-size:.85rem;">{{ $namaUser }}</div>
                        <div class="text-muted" style="font-size:.72rem; text-transform:capitalize;">
                            {{ $roleClass }}
                        </div>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                       href="{{ $profilRoute }}">
                        <i class="bi bi-person-circle text-primary"></i>
                        Edit Profil
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger w-100">
                            <i class="bi bi-box-arrow-right"></i>
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- MAIN CONTENT --}}
<main class="main-content">
    <div class="main-content-body">
        @yield('content')
    </div>
    <footer class="main-footer">
        &copy; 2026 Sistem Presensi Geofencing SMK 4 LPPM RI Padalarang
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btnOpen = document.getElementById('btnHamburger');

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (btnOpen) btnOpen.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('.nav-link-custom').forEach(l => l.addEventListener('click', closeSidebar));
    window.addEventListener('resize', () => { if (window.innerWidth >= 992) closeSidebar(); });
</script>

@stack('scripts')
</body>
</html>
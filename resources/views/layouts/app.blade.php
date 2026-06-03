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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* ===================================================
           SIDEBAR
        =================================================== */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1rem;
            transition: transform 0.28s ease;
        }

        /* Di mobile: sidebar disembunyikan ke kiri */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
        }

        /* ===================================================
           OVERLAY (backdrop gelap saat sidebar terbuka di mobile)
        =================================================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1040;
        }
        .sidebar-overlay.show {
            display: block;
        }

        /* ===================================================
           TOPBAR MOBILE (hanya muncul di layar kecil)
        =================================================== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1030;
            align-items: center;
            padding: 0 1rem;
            gap: 12px;
        }
        @media (max-width: 767.98px) {
            .mobile-topbar { display: flex; }
        }

        .btn-hamburger {
            background: none;
            border: none;
            padding: 6px;
            border-radius: 8px;
            color: #1e293b;
            font-size: 1.3rem;
            cursor: pointer;
            line-height: 1;
        }
        .btn-hamburger:hover { background: #f1f5f9; }

        .mobile-topbar-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #14532d;
            flex: 1;
        }

        /* ===================================================
           SIDEBAR BRAND & NAV
        =================================================== */
        .sidebar-brand {
            font-size: 1.35rem;
            font-weight: 700;
            color: #14532d;
            padding-left: 0.75rem;
            margin-bottom: 2rem;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #475569;
            font-weight: 500;
            font-size: 0.95rem;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .nav-link-custom:hover { background-color: #f1f5f9; color: #1e293b; }
        .nav-link-custom.active { background-color: #15803d; color: #ffffff; }

        .btn-logout-sidebar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #dc2626;
            font-weight: 600;
            font-size: 0.95rem;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            border-radius: 10px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-logout-sidebar:hover { background-color: #fef2f2; }

        /* ===================================================
           MAIN CONTENT
        =================================================== */
        .main-content {
            margin-left: 260px;
            padding: 2.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Di mobile: tidak ada margin kiri, tapi ada padding atas untuk topbar */
        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
                padding: 1.25rem;
                padding-top: calc(56px + 1.25rem);
            }
        }

        /* ===================================================
           STAT CARDS
        =================================================== */
        .stat-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
            border-left: 5px solid #10b981;
        }
        .stat-card.total-siswa  { border-left-color: #15803d; }
        .stat-card.siswa-hadir  { border-left-color: #eab308; }
        .stat-card.guru-aktif   { border-left-color: #3b82f6; }
        .stat-card.alpa-hari-ini{ border-left-color: #ef4444; }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
        }

        .chart-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
            padding: 1.5rem;
        }
    </style>
</head>
<body>

    {{-- ===== OVERLAY (backdrop mobile) ===== --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ===== TOPBAR MOBILE ===== --}}
    <div class="mobile-topbar">
        <button class="btn-hamburger" id="btnHamburger" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="mobile-topbar-title">
            @if (Auth::guard('admin')->check())  Admin Panel
            @elseif(Auth::guard('guru')->check()) Guru Panel
            @elseif(Auth::guard('wali')->check()) Wali Panel
            @elseif(Auth::guard('siswa')->check()) Siswa Panel
            @endif
        </span>
    </div>

    {{-- ===== SIDEBAR ===== --}}
    @include('components.sidebar')

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="main-content">
        <div>
            @yield('content')
        </div>
        <div class="text-center text-muted small py-2 border-top opacity-75 mt-4">
            &copy; 2026 Sistem Presensi Geofencing SMK 4 LPPM RI Padalarang
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const sidebar  = document.querySelector('.sidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const btnOpen  = document.getElementById('btnHamburger');

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // cegah scroll body saat sidebar terbuka
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        btnOpen.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Tutup sidebar otomatis saat link diklik (navigasi halaman baru)
        sidebar.querySelectorAll('.nav-link-custom').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });
    </script>

    @stack('scripts')
</body>
</html>
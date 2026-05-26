<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMK 4 LPPM RI Padalarang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- PENTING: HAPUS SCRIPT BOOTSTRAP DARI SINI -->
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1rem;
        }

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

        .nav-link-custom:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .nav-link-custom.active {
            background-color: #15803d;
            color: #ffffff;
        }

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
        }

        .btn-logout-sidebar:hover {
            background-color: #fef2f2;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 260px;
            padding: 2.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Stats Card Styling */
        .stat-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
            border-left: 5px solid #10b981;
        }

        .stat-card.total-siswa { border-left-color: #15803d; }
        .stat-card.siswa-hadir { border-left-color: #eab308; }
        .stat-card.guru-aktif { border-left-color: #3b82f6; }
        .stat-card.alpa-hari-ini { border-left-color: #ef4444; }

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

        /* Chart Card Styling */
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

    <!-- Memanggil komponen sidebar tunggal -->
    @include('components.sidebar')

    <!-- Konten Utama Dinamis -->
    <div class="main-content">
        <div>
            @yield('content')
        </div>

        <div class="text-center text-muted small py-2 border-top opacity-75 mt-4">
            &copy; 2026 Sistem Presensi Geofencing SMK 4 LPPM RI Padalarang
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PENTING: PINDAHKAN SCRIPT BOOTSTRAP KE SINI (DI ATAS CHART.JS DAN STACK SCRIPTS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Wali - SMK 4 LPPM RI Padalarang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-warning shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="#">
                <i class="bi bi-people-fill me-2"></i> Ruang Orang Tua / Wali
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-4 mb-4">
            <h4 class="fw-bold mb-1">Selamat Datang, Bapak/Ibu {{ Auth::guard('wali')->user()->nama_wali }} 👋</h4>
            <p class="text-muted mb-0">Kontak: {{ Auth::guard('wali')->user()->no_telp }} | <span class="badge bg-warning text-dark">Wali Siswa</span></p>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check text-warning me-2"></i>Pantau Kehadiran Anak</h5>
            <p class="text-muted">Di halaman ini Anda dapat memantau jam masuk sekolah anak Anda beserta status keterangannya (Hadir/Izin/Sakit/Alpa) setiap hari secara berkala.</p>
            <a href="#" class="btn btn-warning text-dark rounded-pill px-4 align-self-start fw-bold btn-sm">Lihat Kalender Presensi</a>
        </div>
    </div>

</body>
</html>
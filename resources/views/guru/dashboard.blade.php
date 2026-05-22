<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SMK 4 lppm ri Padalarang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-person-workspace me-2"></i> Guru Panel
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-4 mb-4">
            <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::guard('guru')->user()->nama_guru }} 👋</h4>
            <p class="text-muted mb-0">NIP: {{ Auth::guard('guru')->user()->nip }} | <span class="badge bg-success">Tenaga Pengajar</span></p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold"><i class="bi bi-check2-square text-success me-2"></i>Validasi Kehadiran Hari Ini</h5>
                    <p class="text-muted small">Periksa ajuan izin, sakit, atau konfirmasi status alpa absensi siswa bimbingan.</p>
                    <a href="#" class="btn btn-success rounded-pill px-3 btn-sm">Mulai Validasi</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold"><i class="bi bi-eye-fill text-primary me-2"></i>Monitoring Jurnal Kelas</h5>
                    <p class="text-muted small">Lihat statistik absensi real-time siswa kelas Anda yang masuk radius Geofence.</p>
                    <a href="#" class="btn btn-primary rounded-pill px-3 btn-sm">Pantau Geofence</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
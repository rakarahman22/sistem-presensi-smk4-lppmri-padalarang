<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi - Pilih Akses Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f5f7fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card-login {
            transition: all 0.3s ease-in-out;
            border-radius: 20px;
            border: none;
        }

        .card-login:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Sistem Presensi Sekolah</h2>
        <p class="text-secondary">Silakan pilih hak akses Anda untuk masuk ke dalam sistem</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Login Siswa -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card card-login p-4 text-center shadow-sm">
                <div class="icon-box bg-primary-subtle text-primary rounded-circle fs-2">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Siswa</h5>
                <p class="text-muted small mb-4">Akses untuk melakukan presensi mandiri dan melihat riwayat.</p>
                <a href="/login/siswa" class="btn btn-primary w-100 rounded-pill py-2">
                    Masuk
                </a>
            </div>
        </div>

        <!-- Login Guru -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card card-login p-4 text-center shadow-sm">
                <div class="icon-box bg-success-subtle text-success rounded-circle fs-2">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <h5 class="fw-bold mb-3">Guru</h5>
                <p class="text-muted small mb-4">Akses untuk memvalidasi presensi dan memantau siswa.</p>
                <a href="/login/guru" class="btn btn-success w-100 rounded-pill py-2">
                    Masuk
                </a>
            </div>
        </div>

        <!-- Login Wali Siswa -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card card-login p-4 text-center shadow-sm">
                <div class="icon-box bg-warning-subtle text-warning rounded-circle fs-2">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Wali Siswa</h5>
                <p class="text-muted small mb-4">Akses untuk memantau kehadiran anak atau wali di sekolah.</p>
                <a href="/login/wali" class="btn btn-warning w-100 text-dark rounded-pill py-2">
                    Masuk
                </a>
            </div>
        </div>

        <!-- Login Admin -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card card-login p-4 text-center shadow-sm">
                <div class="icon-box bg-danger-subtle text-danger rounded-circle fs-2">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Admin / Staf</h5>
                <p class="text-muted small mb-4">Akses penuh untuk rekapitulasi data dan kelola Geofencing.</p>
                <a href="/login/admin" class="btn btn-danger w-100 rounded-pill py-2">
                    Masuk
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
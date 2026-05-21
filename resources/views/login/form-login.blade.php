<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ ucfirst($type) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; min-height: 100vh; display: flex; align-items: center; }
        .card-custom { border-radius: 20px; border: none; max-width: 400px; width: 100%; margin: auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="card card-custom shadow p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Login {{ ucfirst($type == 'wali' ? 'Wali Siswa' : $type) }}</h4>
            <p class="text-muted small">Masukkan kredensial Anda untuk mengakses sistem</p>
        </div>

        <!-- Menampilkan Error jika login gagal -->
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first('username') }}
            </div>
        @endif

        <form action="{{ route('login.proses', $type) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Mengubah warna tombol otomatis mengikuti tema pilihan halaman depan -->
            @php
                $btnColor = match($type) { 'admin' => 'btn-dark', 'guru' => 'btn-success', 'wali' => 'btn-warning', 'siswa' => 'btn-primary' };
            @endphp

            <button type="submit" class="btn {{ $btnColor }} w-100 rounded-pill py-2 fw-bold">
                Masuk Sistem
            </button>
            
            <div class="text-center mt-3">
                <a href="/login" class="text-decoration-none small text-secondary">← Kembali ke Pilihan</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
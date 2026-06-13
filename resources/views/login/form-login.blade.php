<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ ucfirst($type) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; min-height: 100vh; display: flex; align-items: center; }
        .card-custom { border-radius: 20px; border: none; max-width: 400px; width: 100%; margin: auto; }

        /* Tombol mata di dalam input */
        .password-wrapper { position: relative; }
        .password-wrapper .form-control { padding-right: 2.8rem; }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: #adb5bd;
            cursor: pointer;
            line-height: 1;
            font-size: 1.1rem;
        }
        .toggle-password:hover { color: #495057; }
    </style>
</head>
<body>

<div class="container">
    <div class="card card-custom shadow p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Login {{ ucfirst($type == 'wali' ? 'Wali Siswa' : $type) }}</h4>
            <p class="text-muted small">Masukkan kredensial Anda untuk mengakses sistem</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first('username') }}
            </div>
        @endif

        <form action="{{ route('login.proses', $type) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label small fw-bold">Username</label>
                <input type="text" name="username" id="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password"
                           class="form-control" required>
                    <button type="button" class="toggle-password" id="togglePassword" tabindex="-1"
                            aria-label="Tampilkan/sembunyikan password">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            @php
                $btnColor = match($type) {
                    'admin' => 'btn-danger',
                    'guru'  => 'btn-success',
                    'wali'  => 'btn-warning',
                    'siswa' => 'btn-primary',
                    default => 'btn-secondary'
                };
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

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggleIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>

</body>
</html>
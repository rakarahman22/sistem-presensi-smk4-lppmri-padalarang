@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    {{-- Avatar --}}
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials">
                                {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="fw-bold mb-0">{{ $siswa->nama_siswa }}</h5>
                        <small class="text-muted">{{ '@' . $siswa->username }}</small>
                    </div>

                    {{-- MODE VIEW --}}
                    <div id="view-mode">
                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted fw-normal">NIS</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $siswa->nis }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Nama Lengkap</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $siswa->nama_siswa }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Username</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $siswa->username }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Kelas</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Wali Siswa</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $siswa->wali->nama_wali ?? '-' }}</dd>
                        </dl>

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary" id="btn-edit">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profil
                            </button>
                        </div>
                    </div>

                    {{-- MODE EDIT --}}
                    <div id="edit-mode" class="d-none">
                        <hr>
                        <form action="{{ route('siswa.profil.update') }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nama_siswa" class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_siswa"
                                    name="nama_siswa"
                                    class="form-control @error('nama_siswa') is-invalid @enderror"
                                    value="{{ old('nama_siswa', $siswa->nama_siswa) }}"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                >
                                @error('nama_siswa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $siswa->username) }}"
                                    placeholder="Masukkan username"
                                    required
                                >
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-lock me-1"></i>
                                Isi bagian ini hanya jika ingin mengganti password.
                            </p>

                            <div class="mb-3">
                                <label for="password_lama" class="form-label fw-semibold">Password Lama</label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="password_lama"
                                        name="password_lama"
                                        class="form-control @error('password_lama') is-invalid @enderror"
                                        placeholder="Masukkan password lama"
                                        autocomplete="current-password"
                                    >
                                    <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="password_lama">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password_lama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimal 6 karakter"
                                        autocomplete="new-password"
                                    >
                                    <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Ulangi password baru"
                                        autocomplete="new-password"
                                    >
                                    <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btn-batal">
                                    Batal
                                </button>
                            </div>

                        </form>
                    </div>
                    {{-- END MODE EDIT --}}

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background-color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-initials {
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    const viewMode = document.getElementById('view-mode');
    const editMode = document.getElementById('edit-mode');
    const btnEdit  = document.getElementById('btn-edit');
    const btnBatal = document.getElementById('btn-batal');

    @if ($errors->any())
        viewMode.classList.add('d-none');
        editMode.classList.remove('d-none');
    @endif

    btnEdit.addEventListener('click', () => {
        viewMode.classList.add('d-none');
        editMode.classList.remove('d-none');
    });

    btnBatal.addEventListener('click', () => {
        editMode.classList.add('d-none');
        viewMode.classList.remove('d-none');
    });

    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
</script>
@endpush
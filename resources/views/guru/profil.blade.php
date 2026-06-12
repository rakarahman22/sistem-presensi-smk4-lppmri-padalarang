@extends('layouts.app')

@section('title', 'Profil Guru')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">

                    {{-- Avatar + info --}}
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#15803d,#22c55e);">
                            <span style="color:#fff;font-size:2rem;font-weight:700;line-height:1;">
                                {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="fw-bold mb-0">{{ $guru->nama_guru }}</h5>
                        <small class="text-muted">{{ '@' . $guru->username }}</small>
                        <div class="mt-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1"
                                  style="border-radius:20px; font-size:0.78rem;">
                                <i class="bi bi-person-badge-fill me-1"></i>{{ $guru->jabatan ?? 'Guru' }}
                            </span>
                        </div>
                    </div>

                    {{-- MODE VIEW --}}
                    <div id="view-mode">
                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted fw-normal">Nama</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $guru->nama_guru }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">NIP</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $guru->nip ?? '-' }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Jabatan</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $guru->jabatan ?? '-' }}</dd>

                            <dt class="col-sm-4 text-muted fw-normal">Username</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $guru->username }}</dd>
                        </dl>
                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary fw-semibold" id="btn-edit"
                                    style="border-radius:10px;">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profil
                            </button>
                        </div>
                    </div>

                    {{-- MODE EDIT --}}
                    <div id="edit-mode" class="d-none">
                        <hr>
                        <form action="{{ route('guru.profil.update') }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_guru"
                                       class="form-control @error('nama_guru') is-invalid @enderror"
                                       value="{{ old('nama_guru', $guru->nama_guru) }}"
                                       placeholder="Masukkan nama lengkap"
                                       style="border-radius:8px;" required>
                                @error('nama_guru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">NIP</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $guru->nip ?? '-' }}"
                                       style="border-radius:8px;background:#f8fafc;" disabled>
                                <small class="text-muted">NIP tidak dapat diubah sendiri. Hubungi admin jika ada kesalahan.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jabatan</label>
                                <input type="text" name="jabatan"
                                       class="form-control @error('jabatan') is-invalid @enderror"
                                       value="{{ old('jabatan', $guru->jabatan) }}"
                                       placeholder="Contoh: Guru Mata Pelajaran, Wali Kelas"
                                       style="border-radius:8px;">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="username"
                                       class="form-control @error('username') is-invalid @enderror"
                                       value="{{ old('username', $guru->username) }}"
                                       placeholder="Masukkan username"
                                       style="border-radius:8px;" required>
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
                                <label class="form-label fw-semibold">Password Lama</label>
                                <div class="input-group">
                                    <input type="password" id="password_lama" name="password_lama"
                                           class="form-control @error('password_lama') is-invalid @enderror"
                                           placeholder="Masukkan password lama"
                                           style="border-radius:8px 0 0 8px;"
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary toggle-pw" type="button"
                                            data-target="password_lama">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password_lama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Minimal 6 karakter"
                                           style="border-radius:8px 0 0 8px;"
                                           autocomplete="new-password">
                                    <button class="btn btn-outline-secondary toggle-pw" type="button"
                                            data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-control"
                                           placeholder="Ulangi password baru"
                                           style="border-radius:8px 0 0 8px;"
                                           autocomplete="new-password">
                                    <button class="btn btn-outline-secondary toggle-pw" type="button"
                                            data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary fw-semibold"
                                        style="border-radius:10px;">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btn-batal"
                                        style="border-radius:10px;">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const viewMode = document.getElementById('view-mode');
    const editMode = document.getElementById('edit-mode');

    @if($errors->any())
        viewMode.classList.add('d-none');
        editMode.classList.remove('d-none');
    @endif

    document.getElementById('btn-edit').addEventListener('click', () => {
        viewMode.classList.add('d-none');
        editMode.classList.remove('d-none');
    });

    document.getElementById('btn-batal').addEventListener('click', () => {
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
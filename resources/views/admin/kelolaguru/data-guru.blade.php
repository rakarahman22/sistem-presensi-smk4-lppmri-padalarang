@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-person-workspace me-2"></i>Data Guru
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $gurus->total() }}</strong> guru terdaftar
        </p>
    </div>
    <a href="{{ route('admin.guru.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Guru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    <form method="GET" action="{{ route('admin.guru') }}">
        <div class="row g-2 mb-3 align-items-end">

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1 text-muted">Cari Guru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           class="form-control bg-light border-start-0"
                           placeholder="Nama, NIP, atau username..."
                           style="border-radius:0 10px 10px 0;">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Status Wali Kelas</label>
                <select name="status_wali" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="mengampu" {{ request('status_wali') == 'mengampu' ? 'selected' : '' }}>
                        Sudah mengampu
                    </option>
                    <option value="belum" {{ request('status_wali') == 'belum' ? 'selected' : '' }}>
                        Belum mengampu
                    </option>
                </select>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label fw-semibold small mb-1 text-muted">Tampilkan</label>
                <select name="per_page" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100, 500] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
                <button type="submit" class="btn btn-primary w-100" style="border-radius:10px;">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
                <a href="{{ route('admin.guru') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $gurus->firstItem() ?? 0 }}</strong>–<strong>{{ $gurus->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $gurus->total() }}</strong> guru
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $gurus->currentPage() }} dari {{ $gurus->lastPage() }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th style="width:140px;">NIP</th>
                    <th>Nama Guru</th>
                    <th>Jabatan</th>
                    <th>Username</th>
                    <th>Wali Kelas</th>
                    <th class="text-center" style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $i => $guru)
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $gurus->firstItem() + $i }}</td>
                        <td class="text-muted fw-medium">{{ $guru->nip }}</td>
                        <td class="fw-semibold text-dark">{{ $guru->nama_guru }}</td>
                        <td class="text-muted small">{{ $guru->jabatan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1" style="border-radius:6px;">
                                {{ $guru->username }}
                            </span>
                        </td>
                        <td>
                            @if($guru->kelasDiampu)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $guru->kelasDiampu->tingkat }} {{ $guru->kelasDiampu->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Belum mengampu</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.guru.edit', $guru->id_guru) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.guru.destroy', $guru->id_guru) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus data {{ addslashes($guru->nama_guru) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-person-x display-6 d-block mb-2 text-secondary"></i>
                            Tidak ada data guru yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($gurus->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $gurus->links() }}
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f8fafc; }
</style>
@endpush
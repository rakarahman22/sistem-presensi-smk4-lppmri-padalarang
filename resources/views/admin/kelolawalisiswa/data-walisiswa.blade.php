@extends('layouts.app')

@section('title', 'Data Wali Siswa')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-people-fill me-2"></i>Data Wali Siswa
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $walis->total() }}</strong> wali terdaftar
        </p>
    </div>
    <a href="{{ route('admin.wali.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Wali Siswa
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    <form method="GET" action="{{ route('admin.wali') }}">
        <div class="row g-2 mb-3 align-items-end">

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1 text-muted">Cari Wali Siswa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           class="form-control bg-light border-start-0"
                           placeholder="Nama, username, atau no. telp..."
                           style="border-radius:0 10px 10px 0;">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Status Siswa</label>
                <select name="status_siswa" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="punya" {{ request('status_siswa') == 'punya' ? 'selected' : '' }}>
                        Sudah punya siswa
                    </option>
                    <option value="belum" {{ request('status_siswa') == 'belum' ? 'selected' : '' }}>
                        Belum punya siswa
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
                <a href="{{ route('admin.wali') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $walis->firstItem() ?? 0 }}</strong>–<strong>{{ $walis->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $walis->total() }}</strong> wali
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $walis->currentPage() }} dari {{ $walis->lastPage() }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th>Nama Wali</th>
                    <th>Username</th>
                    <th>No. Telepon</th>
                    <th class="text-center" style="width:140px;">Siswa Terdaftar</th>
                    <th class="text-center" style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($walis as $i => $wali)
                    @php $jumlahSiswa = $wali->siswa->count(); @endphp
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $walis->firstItem() + $i }}</td>
                        <td class="fw-semibold text-dark">{{ $wali->nama_wali }}</td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1"
                                  style="border-radius:6px;">
                                {{ $wali->username }}
                            </span>
                        </td>
                        <td class="text-muted">
                            @if($wali->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wali->no_telp) }}"
                                   target="_blank" class="text-success text-decoration-none"
                                   title="Hubungi via WhatsApp">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $wali->no_telp }}
                                </a>
                            @else
                                <span class="fst-italic small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($jumlahSiswa > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    {{ $jumlahSiswa }} siswa
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    Belum ada
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.wali.edit', $wali->id_wali) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.wali.destroy', $wali->id_wali) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus {{ addslashes($wali->nama_wali) }}?\nData siswa yang terhubung akan terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus"
                                            {{ $jumlahSiswa > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                            Tidak ada data wali siswa yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($walis->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $walis->links() }}
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
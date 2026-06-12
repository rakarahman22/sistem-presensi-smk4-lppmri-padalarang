@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-mortarboard-fill me-2"></i>Data Siswa
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $siswas->total() }}</strong> siswa terdaftar
        </p>
    </div>
    <a href="{{ route('admin.siswa.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Siswa
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    {{-- FILTER FORM --}}
    <form method="GET" action="{{ route('admin.siswa') }}" id="formFilter">
        <div class="row g-2 mb-3 align-items-end">

            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Cari Siswa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           class="form-control bg-light border-start-0"
                           placeholder="Nama atau NIS..."
                           style="border-radius:0 10px 10px 0;">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Tingkat</label>
                <select name="tingkat" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($tingkat_list as $t)
                        <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Jurusan</label>
                <select name="jurusan" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($jurusan_list as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Kelas</label>
                <select name="id_kelas" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas_list as $k)
                        <option value="{{ $k->id_kelas }}" {{ request('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->tingkat }} {{ $k->nama_kelas }}
                        </option>
                    @endforeach
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
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="border-radius:10px;">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
                <a href="{{ route('admin.siswa') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    {{-- INFO HASIL --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $siswas->firstItem() ?? 0 }}</strong>–<strong>{{ $siswas->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $siswas->total() }}</strong> siswa
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $siswas->currentPage() }} dari {{ $siswas->lastPage() }}
        </p>
    </div>

    {{-- TABEL --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th style="width:120px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Siswa</th>
                    <th class="text-center" style="width:110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $i => $siswa)
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $siswas->firstItem() + $i }}</td>
                        <td class="text-muted fw-medium">{{ $siswa->nis }}</td>
                        <td class="fw-semibold text-dark">{{ $siswa->nama_siswa }}</td>
                        <td>
                            @if($siswa->kelas)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Belum set</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $siswa->kelas->jurusan ?? '-' }}</td>
                        <td class="text-muted small">{{ $siswa->wali->nama_wali ?? '-' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $siswa->id_siswa) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus data {{ addslashes($siswa->nama_siswa) }}?')">
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
                            Tidak ada data siswa yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($siswas->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $siswas->links() }}
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
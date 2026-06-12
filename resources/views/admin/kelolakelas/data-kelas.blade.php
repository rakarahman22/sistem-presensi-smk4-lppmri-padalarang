@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-collection-fill me-2"></i>Data Kelas
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $kelases->total() }}</strong> kelas terdaftar
        </p>
    </div>
    <a href="{{ route('admin.kelas.create') }}"
       class="btn btn-success fw-semibold d-flex align-items-center gap-2"
       style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Kelas
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    <form method="GET" action="{{ route('admin.kelas') }}">
        <div class="row g-2 mb-3 align-items-end">

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

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Jurusan</label>
                <select name="jurusan" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusan_list as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Wali Kelas</label>
                <select name="status_wali" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="ada" {{ request('status_wali') == 'ada' ? 'selected' : '' }}>
                        Sudah ada
                    </option>
                    <option value="belum" {{ request('status_wali') == 'belum' ? 'selected' : '' }}>
                        Belum ada
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
                <a href="{{ route('admin.kelas') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $kelases->firstItem() ?? 0 }}</strong>–<strong>{{ $kelases->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $kelases->total() }}</strong> kelas
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $kelases->currentPage() }} dari {{ $kelases->lastPage() }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th>Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th class="text-center" style="width:130px;">Jumlah Siswa</th>
                    <th class="text-center" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $i => $kelas)
                    @php $jumlahSiswa = $kelas->siswa->count(); @endphp
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $kelases->firstItem() + $i }}</td>
                        <td>
                            <span class="badge bg-success px-2 py-1"
                                  style="border-radius:6px; background:#15803d !important; font-weight:600;">
                                {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                            </span>
                        </td>
                        <td class="text-muted fw-medium">{{ $kelas->tingkat }}</td>
                        <td class="fw-semibold text-dark">{{ $kelas->jurusan }}</td>
                        <td>
                            @if($kelas->waliKelas)
                                <span class="text-dark fw-medium small">
                                    <i class="bi bi-person-badge text-success me-1"></i>
                                    {{ $kelas->waliKelas->nama_guru }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">
                                    <i class="bi bi-dash-circle me-1"></i>Belum ditentukan
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jumlahSiswa > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' }}"
                                  style="border-radius:6px; font-weight:600; min-width:60px;">
                                {{ $jumlahSiswa }} siswa
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.siswa') }}?id_kelas={{ $kelas->id_kelas }}"
                                   class="btn btn-sm btn-light text-success"
                                   style="border-radius:8px;" title="Lihat Siswa Kelas Ini">
                                    <i class="bi bi-people-fill"></i>
                                </a>
                                <a href="{{ route('admin.kelas.edit', $kelas->id_kelas) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $kelas->id_kelas) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus kelas {{ addslashes($kelas->tingkat . ' ' . $kelas->nama_kelas) }}?')">
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
                            <i class="bi bi-folder-x display-6 d-block mb-2 text-secondary"></i>
                            Tidak ada data kelas yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kelases->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $kelases->links() }}
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
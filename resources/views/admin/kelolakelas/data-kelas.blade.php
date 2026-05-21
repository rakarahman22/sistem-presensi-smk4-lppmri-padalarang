@extends('layouts.app')

@section('title', 'Data Kelas - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-collection-fill me-2"></i>Data Kelas</h3>
    <a href="{{ route('admin.kelas.create') }}" class="btn btn-success d-flex align-items-center gap-2" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.2rem; text-decoration: none;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Kelas
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="border-radius: 10px 0 0 10px; width: 80px;">No</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Kompetensi / Jurusan</th>
                    <th>Wali Kelas</th>
                    <th class="text-end pe-3" style="border-radius: 0 10px 10px 0; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $index => $kelas)
                    <tr>
                        <td class="ps-3 fw-medium text-secondary">{{ $index + 1 }}</td>
                        <td>
                            <span class="badge bg-success px-2 py-1" style="border-radius: 6px; background-color: #15803d !important; font-weight: 600;">
                                {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                            </span>
                        </td>
                        <td class="text-secondary fw-medium">{{ $kelas->tingkat }}</td>
                        <td class="fw-semibold text-dark">{{ $kelas->jurusan }}</td>
                        <td>
                            @if($kelas->waliKelas)
                                <span class="text-dark fw-medium"><i class="bi bi-person-badge text-success me-1"></i>{{ $kelas->waliKelas->nama_guru }}</span>
                            @else
                                <span class="text-muted italic small"><i class="bi bi-dash-circle me-1"></i>Belum Ditentukan</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <!-- Tombol Lihat Siswa -->
                                <a href="{{ route('admin.siswa') }}" class="btn btn-sm btn-light text-success" style="border-radius: 8px;" title="Lihat Siswa">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                
                                <!-- Tombol Ubah Kelas -->
                                <a href="{{ route('admin.kelas.edit', $kelas->id_kelas) }}" class="btn btn-sm btn-light text-primary" style="border-radius: 8px;" title="Ubah Kelas">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Tombol Hapus Kelas -->
                                <form action="{{ route('admin.kelas.destroy', $kelas->id_kelas) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" style="border-radius: 8px;" title="Hapus Kelas">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-folder-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data kelas yang terdaftar di sistem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
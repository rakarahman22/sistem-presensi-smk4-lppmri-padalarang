@extends('layouts.app')

@section('title', 'Data Wali Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-people-fill me-2"></i>Data Wali Siswa</h3>
    <!-- Tombol beralih ke halaman input baru -->
    <a href="{{ route('admin.wali.create') }}" class="btn btn-success d-flex align-items-center gap-2" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.2rem; text-decoration: none;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Wali Siswa
    </a>
</div>

<!-- Notifikasi Sukses Berhasil CRUD -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
    <div class="d-flex justify-content-between align-items-center mb-3 group-search">
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control bg-light border-start-0" placeholder="Cari nama wali..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="border-radius: 10px 0 0 10px;">No</th>
                    <th>Nama Wali</th>
                    <th>Username Akun</th>
                    <th>No. Telepon</th>
                    <th class="text-end pe-3" style="border-radius: 0 10px 10px 0;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($walis as $index => $wali)
                    <tr>
                        <td class="ps-3 fw-medium text-secondary">{{ $index + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $wali->nama_wali }}</td>
                        <td><span class="badge bg-light text-secondary border px-2 py-1" style="border-radius: 6px;">{{ $wali->username }}</span></td>
                        <td>{{ $wali->no_telp }}</td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <!-- Pindah ke halaman edit -->
                                <a href="{{ route('admin.wali.edit', $wali->id_wali) }}" class="btn btn-sm btn-light text-primary" style="border-radius: 8px;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Kirim perintah hapus langsung lewat form inline aman -->
                                <form action="{{ route('admin.wali.destroy', $wali->id_wali) }}" method="POST" onsubmit="return confirm('Menghapus data wali dapat memicu error/terhapusnya data siswa yang terhubung. Apakah Anda yakin ingin menghapus {{ $wali->nama_wali }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" style="border-radius: 8px;">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data wali siswa terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
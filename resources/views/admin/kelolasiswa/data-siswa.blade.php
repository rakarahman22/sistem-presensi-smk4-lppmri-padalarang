@extends('layouts.app')

@section('title', 'Data Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-mortarboard-fill me-2"></i>Data Siswa</h3>
    <!-- FIX: Diubah menjadi Link biasa yang mengarah ke halaman create-siswa -->
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-success d-flex align-items-center gap-2" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.2rem; text-decoration: none;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Siswa
    </a>
</div>

<!-- Notifikasi Sukses -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Notifikasi Error Validasi -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal Menyimpan Data:</div>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
    <div class="d-flex justify-content-between align-items-center mb-3 group-search">
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control bg-light border-start-0" placeholder="Cari NIS atau nama..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
        </div>
        <select class="form-select bg-light" style="max-width: 150px; border-radius: 10px; font-size: 0.9rem;">
            <option value="">Semua Kelas</option>
            @foreach($kelas_list as $kelas)
                <option value="{{ $kelas->id_kelas }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="border-radius: 10px 0 0 10px; width: 80px;">No</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th class="text-end pe-3" style="border-radius: 0 10px 10px 0; width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $index => $siswa)
                    <tr>
                        <td class="ps-3 fw-medium text-secondary">{{ $index + 1 }}</td>
                        <td class="text-secondary">{{ $siswa->nis }}</td>
                        <td class="fw-semibold text-dark">{{ $siswa->nama_siswa }}</td>
                        
                        <!-- FIX: MENAMPILKAN TINGKAT DAN NAMA KELAS SEKALIGUS (CONTOH: XII PPLG 1) -->
                        <td>
                            @if($siswa->kelas)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 6px; font-weight: 600;">
                                    {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small italic">Belum Set Kelas</span>
                            @endif
                        </td>
                        
                        <td class="text-muted">{{ $siswa->kelas->jurusan ?? '-' }}</td>
                        
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <!-- Link pindah ke halaman edit-siswa -->
                                <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}" class="btn btn-sm btn-light text-primary" style="border-radius: 8px;" title="Ubah Siswa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Tombol hapus langsung memicu form hapus dengan konfirmasi aman -->
                                <form action="{{ route('admin.siswa.destroy', $siswa->id_siswa) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $siswa->nama_siswa }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" style="border-radius: 8px;" title="Hapus Siswa">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-person-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data siswa terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
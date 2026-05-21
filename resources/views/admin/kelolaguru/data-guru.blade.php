@extends('layouts.app')

@section('title', 'Data Guru - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-person-workspace me-2"></i>Data Guru</h3>
    <a href="{{ route('admin.guru.create') }}" class="btn btn-success d-flex align-items-center gap-2" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 1.2rem; text-decoration: none;">
        <i class="bi bi-plus-circle-fill"></i> Tambah Guru
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
                    <th>NIP / Nomor ID</th>
                    <th>Nama Lengkap Guru</th>
                    <th>Jabatan</th>
                    <th>Username Akun</th>
                    <th>Wali Kelas</th> <!-- Tambah kolom baru di header -->
                    <th class="text-end pe-3" style="border-radius: 0 10px 10px 0; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $index => $guru)
                    <tr>
                        <td class="ps-3 fw-medium text-secondary">{{ $index + 1 }}</td>
                        <td class="text-secondary fw-medium">{{ $guru->nip }}</td>
                        <td class="fw-semibold text-dark">{{ $guru->nama_guru }}</td>
                        <td class="fw-semibold text-dark">{{ $guru->jabatan }}</td>
                        <td><span class="badge bg-light text-secondary border px-2 py-1" style="border-radius: 6px;">{{ $guru->username }}</span></td>
                        
                        <!-- FIX: MENAMPILKAN STATUS KELAS DIAMPU SECARA DINAMIS -->
                        <td>
                            @if($guru->kelasDiampu)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 6px;">
                                    Wali Kelas {{ $guru->kelasDiampu->tingkat }} {{ $guru->kelasDiampu->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small italic">Tidak Mengampu Kelas</span>
                            @endif
                        </td>
            
                        <td class="text-end pe-3">
                            <!-- Tombol Aksi Tetap Seperti Kode Kamu -->
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- FIX: Link pindah ke halaman edit-siswa -->
                                    <a href="{{ route('admin.guru.edit', $guru->id_guru) }}" class="btn btn-sm btn-light text-primary" style="border-radius: 8px;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
    
                                    <!-- FIX: Tombol hapus langsung memicu form hapus dengan konfirmasi aman -->
                                    <form action="{{ route('admin.guru.destroy', $guru->id_guru) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $guru->nama_guru }}?')">
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
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-person-x display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data guru yang terdaftar di sistem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


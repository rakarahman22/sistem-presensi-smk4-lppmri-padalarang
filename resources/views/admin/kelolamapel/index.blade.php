@extends('layouts.app')

@section('title', 'Kelola Master Mata Pelajaran')

@section('content')
<div class="container-fluid py-4">
    
    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 12px;">
        <form action="{{ route('admin.mapel') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="filter_jurusan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua Jurusan --</option>
                    
                    <option value="Umum" {{ $filterJurusan == 'Umum' ? 'selected' : '' }}>Mata Pelajaran Umum (Normatif/Adaptif)</option>
                    
                    @foreach($daftarJurusanDatabase as $jurusanDb)
                        @if($jurusanDb != 'Umum') {{-- Menghindari duplikasi jika ada teks 'Umum' di DB kelas --}}
                            <option value="{{ $jurusanDb }}" {{ $filterJurusan == $jurusanDb ? 'selected' : '' }}>
                                Jurusan {{ $jurusanDb }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                @if($filterJurusan)
                    <a href="{{ route('admin.mapel') }}" class="btn btn-sm btn-light w-100 text-muted">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle-fill text-success me-2"></i>Tambah Mapel</h5>
                <hr>

                @if(session('success'))
                    <div class="alert alert-success py-2 small border-0">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger py-2 small border-0">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('admin.mapel.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Jurusan</label>
                        <select name="jurusan" id="inputEditJurusan" class="form-select" required>
                            <option value="Umum">Umum</option>
                            @foreach($daftarJurusanDatabase as $jurusanDb)
                                @if($jurusanDb != 'Umum')
                                    <option value="{{ $jurusanDb }}">{{ $jurusanDb }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Pemrograman Web" required autocomplete="off">
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 fw-bold py-2" style="background-color: #15803d; border-radius: 8px;">
                        <i class="bi bi-save me-1"></i> Simpan Mata Pelajaran
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-secondary mb-3">
                    <i class="bi bi-list-stars me-2"></i>Daftar Mata Pelajaran 
                    @if($filterJurusan) <span class="text-primary">Sesi Jurusan {{ $filterJurusan }}</span> @endif
                </h5>
                <hr>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th width="120">Jurusan</th>
                                <th>Nama Mata Pelajaran</th>
                                <th width="150" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarMapel as $index => $mapel)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge @if($mapel->jurusan == 'Umum') bg-secondary-subtle text-secondary @else bg-primary-subtle text-primary @endif px-2.5 py-1" style="font-weight: 600; border-radius: 5px;">
                                            {{ $mapel->jurusan }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $mapel->nama_mapel }}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;"
                                                onclick="bukaModalEdit('{{ $mapel->id_mapel }}', '{{ $mapel->nama_mapel }}', '{{ $mapel->jurusan }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="{{ route('admin.mapel.destroy', $mapel->id_mapel) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data mata pelajaran yang didaftarkan untuk kriteria ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalEditMapel" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5); display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Ubah Mata Pelajaran</h6>
                <button type="button" class="btn-close" onclick="tutupModalEdit()"></button>
            </div>
            <form id="formEditMapel" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Jurusan</label>
                        <select name="jurusan" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            
                            <option value="Umum">Umum (Normatif/Adaptif)</option>
                            
                            @foreach($daftarJurusanDatabase as $jurusanDb)
                                @if($jurusanDb != 'Umum') {{-- Mencegah duplikasi kata Umum jika ada di db --}}
                                    <option value="{{ $jurusanDb }}">{{ $jurusanDb }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Mata Pelajaran</label>
                        <input type="text" id="inputEditNamaMapel" name="nama_mapel" class="form-control" required autocomplete="off">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #1e40af; border-radius: 8px;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modalEditMapel = document.getElementById('modalEditMapel');
    const formEditMapel = document.getElementById('formEditMapel');
    const inputEditNamaMapel = document.getElementById('inputEditNamaMapel');
    const inputEditJurusan = document.getElementById('inputEditJurusan');

    function bukaModalEdit(id, nama, jurusan) {
        formEditMapel.action = `/admin/data-mapel/${id}`;
        inputEditNamaMapel.value = nama;
        inputEditJurusan.value = jurusan; // Otomatis memilih opsi jurusan lama
        
        modalEditMapel.style.display = 'block';
        modalEditMapel.classList.add('show');
    }

    function tutupModalEdit() {
        modalEditMapel.style.display = 'none';
        modalEditMapel.classList.remove('show');
    }
</script>
@endsection
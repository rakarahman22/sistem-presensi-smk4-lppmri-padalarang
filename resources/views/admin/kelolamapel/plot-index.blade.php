@extends('layouts.app')

@section('title', 'Plotting Guru Mengajar')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-plus-fill text-primary me-2"></i>Plotting Mengajar</h5>
                <p class="text-muted small">Tentukan guru resmi penanggung jawab mata pelajaran di kelas tertentu.</p>
                <hr>

                @if(session('success'))
                    <div class="alert alert-success py-2 small border-0">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 small border-0">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.plot.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Guru</label>
                        <select name="id_guru" class="form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($daftarGuru as $guru)
                                <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Kelas</label>
                        <select name="id_kelas" id="selectKelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                        
                            @foreach($daftarKelas->groupBy('tingkat') as $tingkat => $kelasGroup)
                                <optgroup label="Kelas {{ $tingkat }}">
                                    @foreach($kelasGroup as $kelas)
                                        <option value="{{ $kelas->id_kelas }}">
                                            {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Pilih Mata Pelajaran</label>
                        <select name="id_mapel" id="selectMapel" class="form-select" required disabled>
                            <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #1e40af; border-radius: 8px; border: none;">
                        <i class="bi bi-link-45deg me-1"></i> Hubungkan Penugasan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-table me-2"></i>Daftar Distribusi Mengajar Guru</h5>
                <hr>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Guru</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarPlot as $index => $plot)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $plot->guru->nama_guru ?? 'Guru Dihapus' }}</td>
                                    <td><span class="badge bg-success-subtle text-success fw-semibold">{{ $plot->kelas->tingkat ?? '' }} {{ $plot->kelas->nama_kelas ?? 'Kelas Dihapus' }}</span></td>
                                    <td class="fw-semibold text-secondary">{{ $plot->mapel->nama_mapel ?? 'Mapel Dihapus' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.plot.destroy', $plot->id_plot) }}" method="POST" onsubmit="return confirm('Hapus plotting penugasan mengajar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data distribusi mengajar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectKelas = document.getElementById('selectKelas');
        const selectMapel = document.getElementById('selectMapel');

        selectKelas.addEventListener('change', function () {
            const idKelas = this.value;

            // Jika pilihan kelas dikosongkan kembali oleh admin
            if (!idKelas) {
                selectMapel.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
                selectMapel.disabled = true;
                return;
            }

            // Status indikator loading sewaktu sistem mengambil data ke server
            selectMapel.innerHTML = '<option value="">⏳ Memuat Mata Pelajaran Kelas...</option>';
            selectMapel.disabled = true;

            // Lakukan pemanggilan AJAX ke method getMapelByKelas yang berada di PlotMengajarController
            fetch(`{{ url('/admin/get-mapel-by-kelas') }}?id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(data => {
                    // Kosongkan option bawaan lama
                    selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    
                    if (data.length === 0) {
                        selectMapel.innerHTML = '<option value="">⚠️ Tidak ada mapel kurikulum untuk jurusan ini</option>';
                        return;
                    }

                    // Terapkan penambahan data mapel tersaring ke dalam elemen select
                    data.forEach(mapel => {
                        const option = document.createElement('option');
                        option.value = mapel.id_mapel;
                        option.text = `${mapel.nama_mapel} [${mapel.jurusan}]`;
                        selectMapel.add(option);
                    });

                    // Buka kunci dropdown mata pelajaran
                    selectMapel.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectMapel.innerHTML = '<option value="">❌ Gagal mengambil data mapel</option>';
                });
        });
    });

    // DEBUG SEMENTARA - hapus setelah masalah selesai
    fetch(`{{ url('/admin/get-mapel-by-kelas') }}?id_kelas=1`)
        .then(r => r.text())  // pakai .text() dulu bukan .json() agar error terlihat
        .then(data => console.log('RESPONSE:', data))
        .catch(err => console.error('FETCH ERROR:', err));
    
    console.log('URL fetch:', `{{ url('/admin/get-mapel-by-kelas') }}`);
</script>
@endsection
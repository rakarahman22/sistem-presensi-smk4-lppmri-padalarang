@extends('layouts.app')

@section('title', 'Filter Rekap Presensi Mapel')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-check-fill text-success me-2"></i>Rekap Absensi Kelas (Per Mapel)</h5>
                <p class="text-muted small">Silakan pilih kelas dan mata pelajaran untuk melihat akumulasi persentase kehadiran siswa.</p>
                <hr>

                <form action="{{ route('guru.absen-mapel.rekap.tampil') }}" method="GET">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Kelas</label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas Anda --</option>
                            @forelse($daftarKelas as $kelas)
                                <option value="{{ $kelas->id_kelas }}">{{ $kelas->tingkat }} {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})</option>
                            @empty
                                <option value="" disabled>⚠️ Anda belum ditugaskan di kelas mana pun</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Mata Pelajaran</label>
                        <select name="nama_mapel" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran Anda --</option>
                            @forelse($daftarMapel as $mapel)
                                <option value="{{ $mapel }}">{{ $mapel }}</option>
                            @empty
                                <option value="" disabled>⚠️ Anda belum ditugaskan untuk mapel mana pun</option>
                            @endforelse
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2.5" style="border-radius: 8px; background-color: #1e40af; border: none;">
                        <i class="bi bi-search me-1"></i> Tampilkan Rekapitulasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
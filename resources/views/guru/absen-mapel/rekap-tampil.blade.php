@extends('layouts.app')

@section('title', 'Hasil Rekap Presensi Mapel')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-md-center align-items-start flex-column flex-md-row mb-3">
            <div>
                <span class="badge bg-primary mb-2" style="background-color: #1e40af;">Laporan Akumulasi</span>
                <h3 class="fw-bold text-dark m-0">Rekap: {{ $namaMapel }}</h3>
                <p class="text-muted m-0">Kelas: <strong>{{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</strong> | Total Tatap Muka: <strong>{{ $totalPertemuan }} Kali Pertemuan</strong></p>
            </div>
            <a href="{{ route('guru.absen-mapel.rekap') }}" class="btn btn-light mt-3 mt-md-0 fw-medium" style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Filter
            </a>
        </div>
        <hr>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50" rowspan="2" class="align-middle">No</th>
                        <th width="120" rowspan="2" class="align-middle">NIS</th>
                        <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                        <th colspan="4">Total Status Kehadiran</th>
                        <th width="135" rowspan="2" class="align-middle">Persentase (%)</th>
                    </tr>
                    <tr>
                        <th class="text-success" width="70">Hadir</th>
                        <th class="text-warning" width="70">Sakit</th>
                        <th class="text-info" width="70">Izin</th>
                        <th class="text-danger" width="70">Alpa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapSiswa as $index => $row)
                        @php
                            // Rumus mencari persentase kehadiran matematika untuk skripsi
                            $persentase = $totalPertemuan > 0 
                                ? round(($row->total_hadir / $totalPertemuan) * 100, 1) 
                                : 0;
                            
                            // Pewarnaan indikator kelulusan absen siswa
                            $bgBadge = $persentase >= 75 ? 'bg-success' : 'bg-danger';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-muted">{{ $row->nis ?? '-' }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $row->nama_siswa }}</td>
                            <td class="fw-bold text-success">{{ $row->total_hadir }}</td>
                            <td class="fw-bold text-warning">{{ $row->total_sakit }}</td>
                            <td class="fw-bold text-info">{{ $row->total_izin }}</td>
                            <td class="fw-bold text-danger">{{ $row->total_alpa }}</td>
                            <td>
                                <span class="badge {{ $bgBadge }} px-2.5 py-1.5" style="border-radius: 6px; font-size: 13px;">
                                    {{ $persentase }} %
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data siswa ditemukan untuk kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
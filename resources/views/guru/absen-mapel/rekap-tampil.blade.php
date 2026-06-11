@extends('layouts.app')

@section('title', 'Hasil Rekap Presensi Mapel')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">

        {{-- ── Header ── --}}
        <div class="d-flex justify-content-between align-items-md-center
                    align-items-start flex-column flex-md-row mb-3">
            <div>
                <span class="badge mb-2"
                      style="background-color: #1e40af; color: #fff;">
                    Laporan Akumulasi
                </span>
                <h3 class="fw-bold text-dark m-0">{{ $namaMapel }}</h3>
                <p class="text-muted m-0" style="font-size: 0.9rem;">
                    Kelas: <strong>{{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</strong>
                    &nbsp;|&nbsp;
                    Total Tatap Muka:
                    <strong>{{ $totalPertemuan }} Kali Pertemuan</strong>
                </p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('guru.absen-mapel.rekap') }}?id_kelas={{ $kelas->id_kelas }}&nama_mapel={{ urlencode($namaMapel) }}"
                   class="btn btn-light fw-medium"
                   style="border-radius: 8px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="button"
                        onclick="cetakRekap()"
                        class="btn btn-outline-secondary fw-medium"
                        style="border-radius: 8px;">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <hr>

        {{-- ── Kartu ringkasan ── --}}
        @php
            $totalSiswa      = $rekapSiswa->count();
            $lulusKehadiran  = $rekapSiswa->filter(function ($s) use ($totalPertemuan) {
                return $totalPertemuan > 0
                    ? round(($s->total_hadir / $totalPertemuan) * 100, 1) >= 75
                    : false;
            })->count();
            $tidakLulus      = $totalSiswa - $lulusKehadiran;
            $rataHadir       = $totalSiswa > 0
                ? round($rekapSiswa->avg('total_hadir'), 1)
                : 0;
            $persenRata      = $totalPertemuan > 0
                ? round(($rataHadir / $totalPertemuan) * 100, 1)
                : 0;
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="fs-3 fw-bold text-dark">{{ $totalSiswa }}</div>
                    <div class="small text-muted">Total Siswa</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 text-center" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="fs-3 fw-bold text-success">{{ $lulusKehadiran }}</div>
                    <div class="small text-muted">Lulus Kehadiran (≥75%)</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 text-center" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <div class="fs-3 fw-bold text-danger">{{ $tidakLulus }}</div>
                    <div class="small text-muted">Tidak Lulus</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 text-center" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <div class="fs-3 fw-bold text-primary">{{ $persenRata }}%</div>
                    <div class="small text-muted">Rata-rata Kehadiran</div>
                </div>
            </div>
        </div>

        {{-- ── Tabel rekap ── --}}
        <div class="table-responsive" id="tabelRekapCetak">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50" rowspan="2" class="align-middle">No</th>
                        <th width="110" rowspan="2" class="align-middle">NIS</th>
                        <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                        <th colspan="4">Total Status Kehadiran</th>
                        <th width="130" rowspan="2" class="align-middle">Persentase</th>
                        <th width="90" rowspan="2" class="align-middle">Keterangan</th>
                    </tr>
                    <tr>
                        <th class="text-success" width="65">H</th>
                        <th class="text-warning" width="65">S</th>
                        <th class="text-info"    width="65">I</th>
                        <th class="text-danger"  width="65">A</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapSiswa as $index => $row)
                        @php
                            $persentase = $totalPertemuan > 0
                                ? round(($row->total_hadir / $totalPertemuan) * 100, 1)
                                : 0;
                            $lulus      = $persentase >= 75;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="text-muted" style="font-size: 0.875rem;">
                                {{ $row->nis ?? '-' }}
                            </td>
                            <td class="text-start fw-semibold text-dark">
                                {{ $row->nama_siswa }}
                            </td>
                            <td class="fw-bold text-success">{{ $row->total_hadir }}</td>
                            <td class="fw-bold text-warning">{{ $row->total_sakit }}</td>
                            <td class="fw-bold text-info">{{ $row->total_izin }}</td>
                            <td class="fw-bold text-danger">{{ $row->total_alpa }}</td>
                            <td>
                                {{-- Progress bar + angka --}}
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-fill"
                                         style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar {{ $lulus ? 'bg-success' : 'bg-danger' }}"
                                             style="width: {{ $persentase }}%;"
                                             role="progressbar"
                                             aria-valuenow="{{ $persentase }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span style="font-size: 0.8rem; font-weight: 600;
                                                 min-width: 38px; text-align: right;
                                                 color: {{ $lulus ? '#15803d' : '#dc2626' }};">
                                        {{ $persentase }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($lulus)
                                    <span class="badge bg-success-subtle text-success"
                                          style="border-radius: 6px; font-size: 0.75rem;">
                                        Lulus
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger"
                                          style="border-radius: 6px; font-size: 0.75rem;">
                                        Tidak Lulus
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                                Tidak ada data siswa untuk kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($rekapSiswa->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-semibold text-muted">Total</td>
                        <td class="fw-bold text-success">{{ $rekapSiswa->sum('total_hadir') }}</td>
                        <td class="fw-bold text-warning">{{ $rekapSiswa->sum('total_sakit') }}</td>
                        <td class="fw-bold text-info">{{ $rekapSiswa->sum('total_izin') }}</td>
                        <td class="fw-bold text-danger">{{ $rekapSiswa->sum('total_alpa') }}</td>
                        <td class="fw-semibold" style="font-size: 0.85rem;">
                            Rata-rata: {{ $persenRata }}%
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

    </div>
</div>

@push('scripts')
<script>
function cetakRekap() {
    window.print();
}
</script>

<style>
@media print {
    /* Sembunyikan navigasi & tombol saat cetak */
    nav, .btn, .badge:not(.badge-cetak) { display: none !important; }
    .card { box-shadow: none !important; border: none !important; }
    body { font-size: 12px; }
    .progress { display: none; }
}
</style>
@endpush
@endsection
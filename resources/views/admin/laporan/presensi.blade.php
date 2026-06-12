@extends('layouts.app')

@section('title', 'Cetak Laporan - SMK 4 LPPM RI Padalarang')

@section('content')
<h3 class="fw-bold text-success mb-4" style="color: #14532d !important;">
    <i class="bi bi-box-arrow-right me-2"></i>Rekap & Laporan
</h3>

<div class="row g-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
            <h5 class="fw-bold text-dark mb-3">Ekspor Absensi per Bulan</h5>

            {{-- FIX: pakai GET action ke route export, bukan form kosong --}}
            <form method="GET" action="{{ route('admin.laporan.presensi.export') }}">

                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Pilih Bulan</label>
                    <select name="bulan" class="form-select" style="border-radius: 10px; padding: 0.6rem;">
                        @foreach([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April',   5 => 'Mei',      6 => 'Juni',
                            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
                           10 => 'Oktober',11 => 'November',12 => 'Desember',
                        ] as $num => $nama)
                            {{-- FIX: default bulan dari now()->month (bukan hardcode) --}}
                            <option value="{{ $num }}" {{ $num == $bulan ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Pilih Tahun</label>
                    <select name="tahun" class="form-select" style="border-radius: 10px; padding: 0.6rem;">
                        {{-- FIX: generate tahun dinamis 3 tahun ke belakang sampai sekarang --}}
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100"
                    style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem;">
                    <i class="bi bi-download me-2"></i>Unduh Laporan Excel
                </button>
            </form>
        </div>
    </div>

    {{-- Opsional: form rentang tanggal bebas --}}
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
            <h5 class="fw-bold text-dark mb-3">Ekspor Rentang Tanggal Bebas</h5>

            <form method="GET" action="{{ route('admin.laporan.presensi.export') }}">
                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Tanggal Dari</label>
                    <input type="date" name="tgl_dari" class="form-control"
                        style="border-radius: 10px; padding: 0.6rem;"
                        value="{{ $tglDari ?? now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" class="form-control"
                        style="border-radius: 10px; padding: 0.6rem;"
                        value="{{ $tglSampai ?? now()->format('Y-m-d') }}">
                </div>

                <button type="submit" class="btn btn-success w-100"
                    style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem;">
                    <i class="bi bi-download me-2"></i>Unduh Laporan Excel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
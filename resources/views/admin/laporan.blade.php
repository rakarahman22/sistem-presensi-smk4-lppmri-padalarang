@extends('layouts.app')

@section('title', 'Cetak Laporan - SMK 4 LPPM RI Padalarang')

@section('content')
<h3 class="fw-bold text-success mb-4" style="color: #14532d !important;"><i class="bi bi-box-arrow-right me-2"></i>Rekap & Laporan</h3>

<div class="row g-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
            <h5 class="fw-bold text-dark mb-3">Ekspor Absensi Bulanan</h5>
            <form>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Pilih Bulan</label>
                    <select class="form-select" style="border-radius: 10px; padding: 0.6rem;">
                        <option>Januari</option>
                        <option>Februari</option>
                        <option>Maret</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-medium">Format File</label>
                    <select class="form-select" style="border-radius: 10px; padding: 0.6rem;">
                        <option>Microsoft Excel (.xlsx)</option>
                        <option>PDF Document (.pdf)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem;"><i class="bi bi-download me-2"></i>Unduh Laporan</button>
            </form>
        </div>
    </div>
</div>
@endsection
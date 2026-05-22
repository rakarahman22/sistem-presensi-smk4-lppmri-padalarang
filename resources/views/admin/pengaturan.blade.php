@extends('layouts.app')

@section('title', 'Pengaturan - SMK 4 LPPM RI Padalarang')

@section('content')
<h3 class="fw-bold text-success mb-4" style="color: #14532d !important;"><i class="bi bi-gear-fill me-2"></i>Pengaturan Sistem</h3>

<div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: white; max-width: 600px;">
    <h5 class="fw-bold text-dark mb-4">Kebijakan Jam Kerja Presensi</h5>
    <form>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label small text-muted fw-medium">Jam Masuk (Mulai)</label>
                <input type="time" class="form-control" value="06:30" style="border-radius: 10px; padding: 0.6rem;">
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-medium">Batas Akhir Absen Masuk</label>
                <input type="time" class="form-control" value="07:00" style="border-radius: 10px; padding: 0.6rem;">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label small text-muted fw-medium">Toleransi Keterlambatan (Menit)</label>
            <input type="number" class="form-control" value="15" style="border-radius: 10px; padding: 0.6rem;">
        </div>
        <button type="submit" class="btn btn-success" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.6rem 2rem;"><i class="bi bi-check-circle-fill me-2"></i>Simpan Sistem</button>
    </form>
</div>
@endsection
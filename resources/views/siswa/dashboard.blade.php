@extends('layouts.app')

@section('title', 'Dashboard Siswa - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <!-- Card Selamat Datang -->
    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 mb-4">
        <h4 class="fw-bold mb-1 text-dark">Halo, {{ Auth::guard('siswa')->user()->nama_siswa }}! ✨</h4>
        <p class="text-muted mb-0">
            NIS: <span class="fw-semibold">{{ Auth::guard('siswa')->user()->nis }}</span> | 
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5">Siswa Aktif</span>
        </p>
    </div>
        
        <!-- Panel Riwayat Singkat -->
        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Singkat Hari Ini
                </h5>
                
                <!-- Info Status Absen Hari Ini (Dinamis dari Controller jika mau dikembangkan) -->
                <div class="alert alert-info border-0 shadow-inner py-3 small mb-0 rounded-3">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill text-info style="font-size: 1.2rem;"></i>
                        <div>
                            <span class="fw-semibold d-block">Belum Ada Catatan</span>
                            Anda belum melakukan presensi masuk pada hari ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
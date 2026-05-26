@extends('layouts.app')

@section('title', 'Dashboard Guru - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="container-fluid px-0 px-md-2">
    
    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 mb-4" style="border-radius: 15px !important;">
        <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::guard('guru')->user()->nama_guru }} 👋</h4>
        <p class="text-muted mb-0 small">
            NIP: {{ Auth::guard('guru')->user()->nip }} | 
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 6px; font-weight: 600;">
                <i class="bi bi-person-badge-fill me-1"></i>Tenaga Pengajar
            </span>
        </p>
    </div>

    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 15px;">
                <h5 class="fw-bold text-success"><i class="bi bi-journal-check me-2"></i>Absen Per Mapel</h5>
                <p class="text-muted small mb-4">Buka sesi mata pelajaran baru di kelas dan kelola checklist kehadiran siswa secara realtime.</p>
                <div class="mt-auto">
                    <a href="{{ route('guru.absen-mapel.index') }}" class="btn btn-success btn-sm px-4 fw-semibold" style="background-color: #15803d; border: none; border-radius: 8px;">
                        <i class="bi bi-play-fill me-1"></i> Mulai Absen
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark"><i class="bi bi-check2-square text-success me-2"></i>Validasi Kehadiran</h5>
                <p class="text-muted small mb-4">Periksa ajuan izin, sakit, atau konfirmasi status alpa absensi siswa bimbingan kelas Anda.</p>
                <div class="mt-auto">
                    <a href="#" class="btn btn-outline-success btn-sm px-4 fw-semibold" style="border-radius: 8px;">
                        Mulai Validasi
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 15px;">
                <h5 class="fw-bold text-dark"><i class="bi bi-eye-fill text-primary me-2"></i>Monitoring Jurnal</h5>
                <p class="text-muted small mb-4">Lihat statistik absensi real-time siswa kelas Anda yang masuk dalam radius Geofence sekolah.</p>
                <div class="mt-auto">
                    <a href="#" class="btn btn-primary btn-sm px-4 fw-semibold" style="background-color: #1e40af; border: none; border-radius: 8px;">
                        Pantau Geofence
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Notifikasi Wali Siswa | SMK 4 LPPM RI')

@section('content')
    {{-- Topbar --}}
    <div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:14px 28px; display:flex; align-items:center; justify-content:space-between; margin: -2.5rem -2.5rem 2rem -2.5rem;">
        <div>
            <div style="font-size:16px; font-weight:600; color:#1e293b;">Notifikasi</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:1px;">Pemberitahuan Sistem Presensi</div>
        </div>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
            <i class="bi bi-bell-fill me-1"></i> Notif Panel
        </span>
    </div>

    {{-- Konten Notifikasi Polos --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="bg-white p-5 rounded-3 shadow-sm border text-center py-5">
                <i class="bi bi-bell-slash text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h4 class="fw-semibold mt-3" style="color: #1e293b;">Belum Ada Notifikasi</h4>
                <p class="text-muted mx-auto" style="font-size: 14px; max-width: 400px;">
                    Semua riwayat pemberitahuan otomatis mengenai presensi atau info sekolah untuk anak Anda akan muncul di halaman ini.
                </p>
            </div>
        </div>
    </div>
@endsection
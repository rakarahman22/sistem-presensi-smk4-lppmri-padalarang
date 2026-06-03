@extends('layouts.app')

@section('title', 'Dashboard Wali Siswa | SMK 4 LPPM RI')

@section('content')
<style>
    /* ===== PENYESUAIAN THEME VARIABEL & KOMPONEN DASHBOARD ===== */
    :root {
        --primary: #1e40af;
        --success: #22c55e;
        --warning: #f59e0b;
        --danger: #ef4444;
    }

    /* OVERRIDE STYLE UNTUK MENYESUAIKAN TEMPLATE HTML */
    .header-dashboard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 28px;
        margin: -2.5rem -2.5rem 2rem -2.5rem;
    }
    .header-dashboard h1 { font-size: 1.3em; color: #1f2937; margin: 0; font-weight: 600; }
    
    .profile-box { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        background: #f8fafc; 
        padding: 8px 15px; 
        border-radius: 50px; 
        border: 1px solid #e2e8f0;
    }
    .profile-box img { width: 30px; height: 30px; border-radius: 50%; }

    /* NOTIFIKASI PESAN GURU */
    .alert-message {
        background: #fffbeb; 
        border-left: 4px solid var(--warning); 
        padding: 15px;
        border-radius: 10px; 
        margin-bottom: 25px; 
        display: flex; 
        align-items: center; 
        gap: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .alert-message i { color: var(--warning); font-size: 1.2em; }

    /* STATS CARDS */
    .cards-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
        gap: 20px; 
        margin-bottom: 30px; 
    }
    .card-stat { 
        background: white; 
        border-radius: 15px; 
        padding: 20px; 
        text-align: center; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.02); 
        border-bottom: 4px solid transparent; 
        border-left: none !important; /* Reset border-left layout default */
    }
    .card-stat h3 { font-size: 0.85em; color: #6b7280; margin: 0; font-weight: 500; }
    .card-stat p { font-size: 1.8rem; font-weight: 700; margin: 8px 0; }
    
    .c-blue { border-color: var(--primary) !important; color: var(--primary); }
    .c-green { border-color: var(--success) !important; color: var(--success); }
    .c-yellow { border-color: var(--warning) !important; color: var(--warning); }
    .c-red { border-color: var(--danger) !important; color: var(--danger); }

    /* ACTION BUTTONS & TABLE */
    .section-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .btn-wa { background: #25d366; color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.85em; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-wa:hover { background: #128c7e; transform: translateY(-2px); color: white; }
    .btn-download { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 8px; font-size: 0.85em; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-download:hover { background: #1e3a8a; transform: translateY(-2px); }

    .table-container-dash { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #edf2f7; }
    
    /* BADGE STATUS STYLING */
    .badge-status-dash { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .status-hadir { background: #dcfce7; color: #15803d; }
    .status-terlambat { background: #fef3c7; color: #92400e; }
    .status-izin { background: #e0f2fe; color: #0369a1; }
    .status-sakit { background: #ede9fe; color: #6d28d9; }
    .status-alpha { background: #fee2e2; color: #b91c1c; }
</style>

    {{-- HEADER / TOPBAR --}}
    <div class="header-dashboard">
        <h1>Selamat Datang, {{ Auth::guard('wali')->user()->nama_wali }}</h1>
        <div class="profile-box">
            <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" alt="Wali Avatar">
            <span style="font-size: 0.85em; font-weight: 600; color: #4b5563;">
                Wali dari: 
                @foreach($siswaList as $index => $siswa)
                    {{ $siswa->nama_siswa }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </span>
        </div>
    </div>

    {{-- NOTIFIKASI PESAN DARI WALI KELAS --}}
    <div class="alert-message">
        <i class="bi bi-envelope-open-text-fill"></i>
        <div style="flex: 1;">
            <strong style="font-size: 0.9em; display: block; color: #92400e;">Pemberitahuan Sistem / Wali Kelas:</strong>
            <span style="font-size: 0.85em; color: #4b5563;">
                Memasuki bulan {{ now()->translatedFormat('F Y') }}, mohon pastikan kehadiran anak Anda terpantau secara berkala melalui radius koordinat sekolah.
            </span>
        </div>
    </div>

    {{-- STATS CARDS GRID --}}
    @php
        $totalSiswaAbsen = $totalHadir + $totalSakit + $totalIzin + $totalAlpha;
        $persentase = $totalSiswaAbsen > 0 ? round(($totalHadir / $totalSiswaAbsen) * 100) : 100;
    @endphp
    <div class="cards-grid">
        <div class="card card-stat c-blue">
            <h3>Persentase Kehadiran</h3>
            <p class="text-primary">{{ $persentase }}%</p>
        </div>
        <div class="card card-stat c-green">
            <h3>Total Hadir</h3>
            <p class="text-success">{{ $totalHadir }}</p>
        </div>
        <div class="card card-stat c-yellow">
            <h3>Total Sakit / Izin</h3>
            <p class="text-warning">{{ $totalSakit + $totalIzin }}</p>
        </div>
        <div class="card card-stat c-red">
            <h3>Total Alpha</h3>
            <p class="text-danger">{{ $totalAlpha }}</p>
        </div>
    </div>

    {{-- SECTION TITLE & BUTTON ACTIONS --}}
    <div class="section-title">
        <h2 style="font-size: 1.1em; margin: 0; color: #1f2937; font-weight: 600;">Daftar Siswa Terhubung</h2>
        <div style="display: flex; gap: 10px;">
            <a href="https://wa.me/628123456789" target="_blank" class="btn-wa">
                <i class="bi bi-whatsapp"></i> Hubungi Sekolah
            </a>
            <a href="{{ route('wali.riwayat-kehadiran') }}" class="btn-download">
                <i class="bi bi-calendar3"></i> Lihat Semua Riwayat
            </a>
        </div>
    </div>

    {{-- DATA SISWA TABLE KONTEN --}}
    <div class="table-container-dash">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nama Anak</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th class="text-center">Aksi Halaman</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $siswa)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $siswa->nama_siswa }}</td>
                        <td><code class="text-secondary">{{ $siswa->nis ?? '-' }}</code></td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('wali.riwayat-kehadiran', ['id_siswa' => $siswa->id_siswa]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 12px; font-weight: 500;">
                                <i class="bi bi-eye-fill me-1"></i> Detail Presensi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-exclamation-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                            Belum ada data siswa yang ditautkan dengan akun Wali ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
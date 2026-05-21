@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Admin Panel')

@section('content')
<div class="main-content-fluid px-0 px-md-2">
    <div class="header-page mb-4">
        <h1 class="fw-bold text-success" style="font-size: 1.6rem;"><i class="fas fa-cogs me-2"></i> Pengaturan Sistem</h1>
    </div>

    <!-- AREA ALERTS SINKRONISASI DATABASE -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 py-3 small" style="border-radius: 10px; background-color: #f0fdf4; color: #166534;">
            <i class="fas fa-check-circle me-1"></i> <strong>{{ session('success') }}</strong>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 py-3 small" style="border-radius: 10px; background-color: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-circle me-1"></i> <strong>Ada kesalahan saat menyimpan!</strong> Harap periksa inputan Anda kembali.
        </div>
    @endif

    <div class="settings-grid">
        
        <!-- CARD 1: IDENTITAS SEKOLAH -->
        <div class="card bg-white border-0 shadow-sm p-4" style="border-radius: 15px;">
            <h3><i class="fas fa-school me-2"></i> Identitas Sekolah</h3>
            
            <form action="{{ route('admin.pengaturan.identitas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $pengaturan->nama_sekolah ?? 'SMK 4 LPPM RI Padalarang') }}" required>
                </div>
                
                <div class="form-group">
                    <label>NPSN</label>
                    <input type="text" name="npsn" value="{{ old('npsn', $pengaturan->npsn ?? '2022XXXX') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Nama Kepala Sekolah</label>
                    <input type="text" name="nama_kepsek" value="{{ old('nama_kepsek', $pengaturan->nama_kepsek ?? '') }}" placeholder="Masukkan nama lengkap & gelar">
                </div>
                
                <div class="form-group">
                    <label>Upload Logo Sekolah</label>
                    <input type="file" name="logo_sekolah" accept="image/*" class="form-control" style="font-size: 0.85rem;">
                    @if(!empty($pengaturan->logo_sekolah))
                        <small class="text-muted d-block mt-1"><i class="fas fa-image me-1"></i> Logo saat ini: <a href="{{ asset('storage/' . $pengaturan->logo_sekolah) }}" target="_blank">Lihat File</a></small>
                    @endif
                </div>
                
                <div class="form-group">
                    <label>Tahun Ajaran Aktif</label>
                    <select name="tahun_ajaran">
                        <option value="2025/2026 Ganjil" {{ (old('tahun_ajaran', $pengaturan->tahun_ajaran ?? '') == '2025/2026 Ganjil') ? 'selected' : '' }}>2025/2026 Ganjil</option>
                        <option value="2025/2026 Genap" {{ (old('tahun_ajaran', $pengaturan->tahun_ajaran ?? '') == '2025/2026 Genap') ? 'selected' : '' }}>2025/2026 Genap</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-save shadow-sm">Simpan Identitas</button>
            </form>
        </div>

        <!-- CARD 2: ATURAN PRESENSI -->
        <div class="card bg-white border-0 shadow-sm p-4" style="border-radius: 15px;">
            <h3><i class="fas fa-clock me-2"></i> Aturan Presensi</h3>
            
            <form action="{{ route('admin.pengaturan.aturan') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Jam Masuk (Mulai)</label>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $pengaturan->jam_masuk ?? '07:00') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Batas Terlambat</label>
                    <input type="time" name="batas_terlambat" value="{{ old('batas_terlambat', $pengaturan->batas_terlambat ?? '07:15') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Hari Kerja Aktif</label>
                    <div class="days-list">
                        @php
                            // Decode array hari dari database jika Anda menyimpannya dalam bentuk JSON []
                            $hariAktif = json_decode($pengaturan->hari_kerja ?? '["Senin","Selasa","Rabu","Kamis","Jumat"]', true);
                        @endphp
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                            <label class="day-item">
                                <input type="checkbox" name="hari_kerja[]" value="{{ $hari }}" {{ in_array($hari, $hariAktif) ? 'checked' : '' }}> {{ $hari }}
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="toggle-group mt-4">
                    <span class="small fw-semibold text-dark">Mode Maintenance Sistem</span>
                    <label class="switch">
                        <input type="checkbox" name="is_maintenance" value="1" {{ ($pengaturan->is_maintenance ?? 0) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <button type="submit" class="btn-save shadow-sm">Update Aturan</button>
            </form>
        </div>

        <!-- CARD 3: AKUN & KEAMANAN -->
        <div class="card bg-white border-0 shadow-sm p-4" style="border-radius: 15px;">
            <h3><i class="fas fa-user-shield me-2"></i> Akun & Keamanan</h3>
            
            <form action="{{ route('admin.pengaturan.keamanan') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Username / Email Admin</label>
                    <input type="text" name="username" value="{{ old('username', Auth::guard('admin')->user()->username ?? 'admin') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
                
                <div class="toggle-group">
                    <div style="font-size: 0.85em; font-weight: 600;">
                        Batas 1 Device per Siswa
                        <p style="font-weight: 400; font-size: 0.8em; color: #64748b; margin-bottom: 0;">Mencegah titip absen / tukar akun.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="lock_device" value="1" {{ ($pengaturan->lock_device ?? 1) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="margin-top: 25px;">
                    <label style="font-size: 0.85em; font-weight: 600; color: #64748b;">Maintenance Data</label>
                    <!-- Link unduh backup database riil mengarah ke controller -->
                    <a href="{{ route('admin.pengaturan.backup') }}" class="btn-backup shadow-sm text-white"><i class="fas fa-database me-1"></i> Backup Database (.sql)</a>
                </div>
                
                <button type="submit" class="btn-save" style="margin-top: 20px;">Simpan Perubahan Akun</button>
            </form>
        </div>

    </div>

    <footer>
        <p style="text-align: center; margin-top: 40px; font-size: 0.8em; color: #94a3b8;">
            © 2026 Panel Administrator - SMK 4 LPPM RI Padalarang
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<style>
    /* INTEGRASI EMBEDDED CSS BAWAAN HTML DESAIN ASLI KAMU */
    .settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
    .card h3 { font-size: 1.1em; margin-bottom: 20px; color: #15803d; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px; font-weight: 600; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 0.85em; margin-bottom: 5px; font-weight: 600; color: #64748b; }
    .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-family: inherit; font-size: 0.9rem; }
    .form-group input:focus { border-color: #15803d; }

    .days-list { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 5px; }
    .day-item { font-size: 0.85em; display: flex; align-items: center; gap: 8px; cursor: pointer; color: #1e293b; }

    .toggle-group { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-top: 1px solid #f1f5f9; margin-top: 10px; }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    
    input:checked + .slider { background-color: #15803d; }
    input:checked + .slider:before { transform: translateX(18px); }

    .btn-save { background: #15803d; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; margin-top: 10px; transition: 0.3s; font-size: 0.95rem; }
    .btn-save:hover { background: #166534; }
    .btn-backup { background: #3b82f6; color: white; text-decoration: none; display: block; text-align: center; padding: 11px; border-radius: 8px; font-size: 0.9em; margin-top: 10px; font-weight: 600; transition: 0.3s; border: none; }
    .btn-backup:hover { background: #2563eb; }
</style>
@endpush
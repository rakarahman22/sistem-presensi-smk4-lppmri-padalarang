<div class="sidebar">
    <div>
        <!-- BRANDING JUDUL PANEL SIDEBAR -->
        <div class="sidebar-brand">
            @if (Auth::guard('admin')->check())
                Admin Panel
            @elseif(Auth::guard('guru')->check())
                Guru Panel
            @elseif(Auth::guard('wali')->check())
                Wali Panel
            @elseif(Auth::guard('siswa')->check())
                Siswa Panel
            @endif
        </div>

        <div class="nav-menu">
            <!-- ========================================================================= -->
            <!-- FITUR AKSES: ADMIN PANEL (FIXED USING GUARD CHECK) -->
            <!-- ========================================================================= -->
            @if(Auth::guard('admin')->check())
                <a href="/admin/dashboard" class="nav-link-custom {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda
                </a>
                <a href="/admin/data-siswa" class="nav-link-custom {{ request()->is('admin/data-siswa*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i> Data Siswa
                </a>
                <a href="/admin/data-guru" class="nav-link-custom {{ request()->is('admin/data-guru*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> Data Guru
                </a>
                <a href="/admin/data-wali" class="nav-link-custom {{ request()->is('admin/data-wali*') ? 'active' : '' }}">
                    <i class="bi bi-person-hearts"></i> Data Wali Siswa
                </a>
                <a href="/admin/data-kelas" class="nav-link-custom {{ request()->is('admin/data-kelas*') ? 'active' : '' }}">
                    <i class="bi bi-collection-fill"></i> Data Kelas
                </a>
                <a href="/admin/presensi-siswa" class="nav-link-custom {{ request()->is('admin/presensi-siswa*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i> Presensi Siswa
                </a>
                <a href="/admin/laporan" class="nav-link-custom {{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                </a>
                <a href="/admin/pengaturan-lokasi" class="nav-link-custom {{ request()->is('admin/pengaturan-lokasi*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> Pengaturan Lokasi
                </a>
                <a href="/admin/pengaturan" class="nav-link-custom {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>
            @endif

            <!-- ========================================================================= -->
            <!-- FITUR AKSES: GURU PANEL (FIXED USING GUARD CHECK) -->
            <!-- ========================================================================= -->
            @if(Auth::guard('guru')->check())
                <a href="/guru/dashboard" class="nav-link-custom {{ request()->is('guru/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda Guru
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="bi bi-calendar-check-fill"></i> Rekap Absensi Kelas
                </a>
            @endif

            <!-- ========================================================================= -->
            <!-- FITUR AKSES: WALI MURID (FIXED USING GUARD CHECK) -->
            <!-- ========================================================================= -->
            @if(Auth::guard('wali')->check())
                <a href="/wali/dashboard" class="nav-link-custom {{ request()->is('wali/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda Wali
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="bi bi-person-check-fill"></i> Kehadiran Anak
                </a>
            @endif

            <!-- ========================================================================= -->
            <!-- FITUR AKSES: SISWA PANEL (FIXED USING GUARD CHECK) -->
            <!-- ========================================================================= -->
            @if(Auth::guard('siswa')->check())
                <a href="/siswa/dashboard" class="nav-link-custom {{ request()->is('siswa/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>

                <a href="/siswa/presensi" class="nav-link-custom {{ request()->is('siswa/presensi') ? 'active' : '' }}">
                    <i class="bi bi-camera-fill"></i> Presensi Harian
                </a>

                <a href="/siswa/riwayat-presensi" class="nav-link-custom {{ request()->is('siswa/riwayat-presensi') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Presensi
                </a>

                <a href="/siswa/pesan-guru" class="nav-link-custom {{ request()->is('siswa/pesan-guru') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Pesan Guru
                </a>

                <a href="/siswa/profil" class="nav-link-custom {{ request()->is('siswa/profil') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil
                </a>
            @endif
        </div>
    </div>

    <!-- TOMBOL KELUAR SISTEM (LOGOUT SANITIZED) -->
    <div>
        <hr class="text-muted opacity-25">
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
            <button type="submit" class="btn-logout-sidebar w-100 border-0 bg-transparent text-start" style="padding: 10px 15px;">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>
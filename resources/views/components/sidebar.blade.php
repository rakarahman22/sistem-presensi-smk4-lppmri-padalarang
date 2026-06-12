<div class="sidebar"
     style="height:100vh;overflow-y:auto;position:fixed;display:flex;flex-direction:column;justify-content:space-between;">
    <div>
        {{-- ===== BRANDING ===== --}}
        <div class="sidebar-brand">
            @if(Auth::guard('admin')->check())
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

            {{-- ================================================================
                 ADMIN PANEL
            ================================================================ --}}
            @if(Auth::guard('admin')->check())

                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda
                </a>

                <a href="{{ route('admin.profil') }}"
                   class="nav-link-custom {{ request()->routeIs('admin.profil*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>

                <div class="nav-group-label">Data Master</div>
                <a href="{{ route('admin.siswa') }}"
                   class="nav-link-custom {{ request()->is('admin/data-siswa*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i> Data Siswa
                </a>
                <a href="{{ route('admin.guru') }}"
                   class="nav-link-custom {{ request()->is('admin/data-guru*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> Data Guru
                </a>
                <a href="{{ route('admin.wali') }}"
                   class="nav-link-custom {{ request()->is('admin/data-wali*') ? 'active' : '' }}">
                    <i class="bi bi-person-hearts"></i> Data Wali Siswa
                </a>
                <a href="{{ route('admin.kelas') }}"
                   class="nav-link-custom {{ request()->is('admin/data-kelas*') ? 'active' : '' }}">
                    <i class="bi bi-collection-fill"></i> Data Kelas
                </a>
                <a href="{{ route('admin.mapel') }}"
                   class="nav-link-custom {{ request()->is('admin/data-mapel*') ? 'active' : '' }}">
                    <i class="bi bi-book-half"></i> Data Mapel
                </a>

                <div class="nav-group-label">Akademik</div>
                <a href="{{ route('admin.plot') }}"
                   class="nav-link-custom {{ request()->is('admin/plot-mengajar*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i> Plotting Mengajar
                </a>
                <a href="{{ route('admin.presensi') }}"
                   class="nav-link-custom {{ request()->is('admin/presensi-siswa*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i> Presensi Siswa
                </a>

                <div class="nav-group-label">Laporan</div>
                <a href="{{ route('admin.laporan.presensi') }}"
                   class="nav-link-custom {{ request()->routeIs('admin.laporan.presensi*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-excel-fill"></i> Rekap Presensi
                </a>

                <div class="nav-group-label">Pengaturan</div>
                <a href="{{ route('admin.lokasi') }}"
                   class="nav-link-custom {{ request()->routeIs('admin.lokasi') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> Lokasi Presensi
                </a>
                <a href="{{ route('admin.pengaturan') }}"
                   class="nav-link-custom {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan Sistem
                </a>
            @endif

            {{-- ================================================================
                 GURU PANEL
            ================================================================ --}}
            @if(Auth::guard('guru')->check())

                <a href="/guru/dashboard"
                   class="nav-link-custom {{ request()->is('guru/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda
                </a>

                <div class="nav-group-label">Absensi Mapel</div>
                <a href="{{ route('guru.absen-mapel.index') }}"
                   class="nav-link-custom {{ request()->is('guru/absen-mapel*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Isi Absen
                </a>
                <a href="{{ route('guru.absen-mapel.rekap') }}"
                   class="nav-link-custom {{ request()->is('guru/rekap-absen-mapel*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i> Rekap Absensi
                </a>

                <div class="nav-group-label">Akun</div>
                <a href="{{ route('guru.profil') }}"
                   class="nav-link-custom {{ request()->routeIs('guru.profil*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>

            @endif

            {{-- ================================================================
                 WALI PANEL
            ================================================================ --}}
            @if(Auth::guard('wali')->check())

                <a href="{{ route('wali.dashboard') }}"
                   class="nav-link-custom {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Beranda
                </a>

                <div class="nav-group-label">Pantau Anak</div>
                <a href="{{ route('wali.riwayat-kehadiran') }}"
                   class="nav-link-custom {{ request()->routeIs('wali.riwayat-kehadiran*') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i> Kehadiran Anak
                </a>
                <a href="{{ route('wali.notifikasi') }}"
                   class="nav-link-custom {{ request()->routeIs('wali.notifikasi*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i> Notifikasi
                </a>

                <div class="nav-group-label">Akun</div>
                <a href="{{ route('wali.profil') }}"
                   class="nav-link-custom {{ request()->routeIs('wali.profil*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>

            @endif

            {{-- ================================================================
                 SISWA PANEL
            ================================================================ --}}
            @if(Auth::guard('siswa')->check())

                <a href="/siswa/dashboard"
                   class="nav-link-custom {{ request()->is('siswa/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>

                <div class="nav-group-label">Presensi</div>
                <a href="/siswa/presensi"
                   class="nav-link-custom {{ request()->is('siswa/presensi') ? 'active' : '' }}">
                    <i class="bi bi-camera-fill"></i> Presensi Harian
                </a>
                <a href="/siswa/riwayat-presensi"
                   class="nav-link-custom {{ request()->is('siswa/riwayat-presensi') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Presensi
                </a>
                <a href="/siswa/riwayat-absen-mapel"
                   class="nav-link-custom {{ request()->is('siswa/riwayat-absen-mapel') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Absensi Mapel
                </a>

                <div class="nav-group-label">Akun</div>
                <a href="{{ route('siswa.profil') }}"
                   class="nav-link-custom {{ request()->routeIs('siswa.profil*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil
                </a>

            @endif

        </div>
    </div>

    {{-- ===== TOMBOL KELUAR ===== --}}
    <div class="mt-3">
        <hr class="text-muted opacity-25 mb-2">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout-sidebar w-100 border-0 bg-transparent text-start">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>

<style>
    .nav-group-label {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        padding: .75rem 1rem .25rem;
        margin-top: .25rem;
        user-select: none;
    }
</style>
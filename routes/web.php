<?php

use App\Http\Controllers\Admin\GeofenceController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PresensiAdminController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\WaliSiswaController; 
use App\Http\Controllers\WaliController; 
use App\Http\Controllers\Admin\PlotMengajarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\PresensiController as SiswaPresensiController; 
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SMK 4 LPPM RI Padalarang
|--------------------------------------------------------------------------
*/

// GUEST / PUBLIC ROUTES
Route::get('/', function () { return view('welcome'); });

Route::get('/login', [AuthController::class, 'pilihLogin'])->name('login');
Route::get('/login/{type}', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login/{type}', [AuthController::class, 'login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/**
 * =========================================================================
 * AREA ROUTE SISWA
 * =========================================================================
 */
Route::prefix('siswa')->middleware('auth:siswa')->group(function () {
    Route::get('/dashboard', function() { return view('siswa.dashboard'); })->name('siswa.dashboard');
    Route::get('/presensi', [SiswaPresensiController::class, 'index'])->name('siswa.presensi');
    Route::post('/presensi/store', [SiswaPresensiController::class, 'store'])->name('siswa.presensi.store');
    Route::get('/riwayat-presensi', function () { return view('siswa.riwayat-presensi'); });
    Route::get('/pesan-guru', function () { return view('siswa.pesan-guru'); });
    Route::get('/profil', function () { return view('siswa.profil'); });
});

/**
 * =========================================================================
 * AREA ROUTE GURU
 * =========================================================================
 */
Route::middleware(['web', 'auth:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', function() { return view('guru.dashboard'); })->name('guru.dashboard');

    // FITUR ABSEN PER MATA PELAJARAN (MAPEL)
    Route::get('/absen-mapel', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'index'])->name('guru.absen-mapel.index');
    Route::post('/absen-mapel/buka-sesi', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'storeSesi'])->name('guru.absen-mapel.buka-sesi');
    Route::get('/absen-mapel/isi/{id_mengajar}', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'isiAbsen'])->name('guru.absen-mapel.isi');
    Route::post('/absen-mapel/simpan/{id_mengajar}', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'updateAbsen'])->name('guru.absen-mapel.simpan');

    // FITUR REKAP ABSEN PER MATA PELAJARAN (MAPEL)
    Route::get('/rekap-absen-mapel', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'rekapIndex'])->name('guru.absen-mapel.rekap');
    Route::get('/rekap-absen-mapel/tampil', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'rekapTampil'])->name('guru.absen-mapel.rekap.tampil');

    // API Cek Pertemuan Absen Mapel
    Route::get('/absen-mapel/cek-pertemuan', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'cekPertemuanKe'])->name('guru.absen-mapel.cek-pertemuan');

    // =========================================================================
    // FIX: TAMBAHKAN BARIS INI (SINKRONISASI FILTER MAPEL GURU)
    // =========================================================================
    Route::get('/absen-mapel/get-mapel', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'getMapelByKelasGuru'])->name('guru.absen-mapel.get-mapel');

    Route::post('/absen-mapel/tambah-mapel-ajax', [\App\Http\Controllers\Guru\AbsenMapelController::class, 'tambahMapelAjax'])->name('guru.absen-mapel.tambah-mapel-ajax');
});

/**
 * =========================================================================
 * AREA ROUTE WALI SISWA
 * =========================================================================
 */
Route::middleware('auth:wali')->prefix('wali')->group(function () {
    Route::get('/dashboard', function() { return view('wali.dashboard'); })->name('wali.dashboard');
});

/**
 * =========================================================================
 * AREA ROUTE ADMIN PANEL (CENTRALIZED & OPTIMIZED)
 * =========================================================================
 */
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    
    // Halaman Dashboard Utama Admin
    Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('admin.dashboard');

    
    // CRUD DATA SISWA
    Route::get('/data-siswa', [SiswaController::class, 'index'])->name('admin.siswa');
    Route::get('/data-siswa/create', [SiswaController::class, 'create'])->name('admin.siswa.create');
    Route::post('/data-siswa', [SiswaController::class, 'store'])->name('admin.siswa.store');
    Route::get('/data-siswa/{id_siswa}/edit', [SiswaController::class, 'edit'])->name('admin.siswa.edit');
    Route::put('/data-siswa/{id_siswa}', [SiswaController::class, 'update'])->name('admin.siswa.update');
    Route::delete('/data-siswa/{id_siswa}', [SiswaController::class, 'destroy'])->name('admin.siswa.destroy');

    // CRUD DATA WALI SISWA
    Route::get('/data-wali', [WaliSiswaController::class, 'index'])->name('admin.wali');
    Route::get('/data-wali/create', [WaliSiswaController::class, 'create'])->name('admin.wali.create');
    Route::post('/data-wali', [WaliSiswaController::class, 'store'])->name('admin.wali.store');
    Route::get('/data-wali/{id_wali}/edit', [WaliSiswaController::class, 'edit'])->name('admin.wali.edit');
    Route::put('/data-wali/{id_wali}', [WaliSiswaController::class, 'update'])->name('admin.wali.update');
    Route::delete('/data-wali/{id_wali}', [WaliSiswaController::class, 'destroy'])->name('admin.wali.destroy');

    // CRUD DATA GURU
    Route::get('/data-guru', [GuruController::class, 'index'])->name('admin.guru');
    Route::get('/data-guru/create', [GuruController::class, 'create'])->name('admin.guru.create');
    Route::post('/data-guru', [GuruController::class, 'store'])->name('admin.guru.store');
    Route::get('/data-guru/{id_guru}/edit', [GuruController::class, 'edit'])->name('admin.guru.edit');
    Route::put('/data-guru/{id_guru}', [GuruController::class, 'update'])->name('admin.guru.update');
    Route::delete('/data-guru/{id_guru}', [GuruController::class, 'destroy'])->name('admin.guru.destroy');

    // CRUD DATA KELAS
    Route::get('/data-kelas', [KelasController::class, 'index'])->name('admin.kelas');
    Route::get('/data-kelas/create', [KelasController::class, 'create'])->name('admin.kelas.create');
    Route::post('/data-kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
    Route::get('/data-kelas/{id_kelas}/edit', [KelasController::class, 'edit'])->name('admin.kelas.edit');
    Route::put('/data-kelas/{id_kelas}', [KelasController::class, 'update'])->name('admin.kelas.update');
    Route::delete('/data-kelas/{id_kelas}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');

    // CRUD DATA MASTER MATA PELAJARAN
    Route::get('/data-mapel', [\App\Http\Controllers\Admin\MapelController::class, 'index'])->name('admin.mapel');
    Route::post('/data-mapel', [\App\Http\Controllers\Admin\MapelController::class, 'store'])->name('admin.mapel.store');
    Route::put('/data-mapel/{id_mapel}', [\App\Http\Controllers\Admin\MapelController::class, 'update'])->name('admin.mapel.update');
    Route::delete('/data-mapel/{id_mapel}', [\App\Http\Controllers\Admin\MapelController::class, 'destroy'])->name('admin.mapel.destroy');

    // CRUD PLOTTING GURU MENGAJAR KELAS & MAPEL
Route::get('/plot-mengajar', [\App\Http\Controllers\Admin\PlotMengajarController::class, 'index'])->name('admin.plot');
Route::post('/plot-mengajar', [\App\Http\Controllers\Admin\PlotMengajarController::class, 'store'])->name('admin.plot.store');
Route::delete('/plot-mengajar/{id_plot}', [\App\Http\Controllers\Admin\PlotMengajarController::class, 'destroy'])->name('admin.plot.destroy');

// ✅ PERBAIKAN - samakan prefix dan namespace dengan route lainnya
Route::get('/get-mapel-by-kelas', [\App\Http\Controllers\Admin\PlotMengajarController::class, 'getMapelByKelas'])->name('admin.get.mapel.by.kelas');

    // MONITORING PRESENSI SISWA (REALTIME HARI INI)
    Route::get('/presensi-siswa', [PresensiAdminController::class, 'index'])->name('admin.presensi');

    // LAPORAN PRESENSI
    Route::get('/laporan', function() { return view('admin.laporan'); })->name('admin.laporan');

    // CONFIG GEOFENCING LOKASI SEKOLAH
    Route::get('/pengaturan-lokasi', [GeofenceController::class, 'index'])->name('admin.lokasi');
    Route::post('/pengaturan-lokasi', [GeofenceController::class, 'update'])->name('admin.lokasi.update');

    // PENGATURAN SISTEM (Mengirimkan dummy object untuk mencegah error undefined variable $pengaturan)
    Route::get('/pengaturan', [PengaturanController::class, 'index'])
        ->name('admin.pengaturan');

    Route::put('/pengaturan/identitas', [PengaturanController::class, 'updateIdentitas'])
        ->name('admin.pengaturan.identitas');

    Route::put('/pengaturan/aturan', [PengaturanController::class, 'updateAturan'])
        ->name('admin.pengaturan.aturan');

    Route::put('/pengaturan/keamanan', [PengaturanController::class, 'updateKeamanan'])
        ->name('admin.pengaturan.keamanan');

    Route::get('/pengaturan/backup', [PengaturanController::class, 'backup'])
        ->name('admin.pengaturan.backup');
});

// AREA GRUP RUTE WALI SISWA (Hanya gunakan yang ini)
Route::middleware(['auth:wali'])->prefix('wali')->name('wali.')->group(function () {
        
    Route::get('/dashboard',            [WaliController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat-kehadiran',    [WaliController::class, 'riwayatKehadiran'])->name('riwayat-kehadiran');
    Route::get('/notifikasi',           [WaliController::class, 'notifikasi'])->name('notifikasi');
    Route::get('/profil',               [WaliController::class, 'profil'])->name('profil');
    Route::put('/profil',               [WaliController::class, 'updateProfil'])->name('profil.update');
    Route::put('/profil/ganti-password', [WaliController::class, 'gantiPassword'])->name('profil.ganti-password');
    Route::post('/logout',              [WaliController::class, 'logout'])->name('logout');
    
});
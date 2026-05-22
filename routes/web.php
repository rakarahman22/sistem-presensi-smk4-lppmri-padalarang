<?php

use App\Http\Controllers\Admin\GeofenceController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PresensiAdminController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\WaliSiswaController; 
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
Route::middleware('auth:guru')->prefix('guru')->group(function () {
    Route::get('/dashboard', function() { return view('guru.dashboard'); })->name('guru.dashboard');
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
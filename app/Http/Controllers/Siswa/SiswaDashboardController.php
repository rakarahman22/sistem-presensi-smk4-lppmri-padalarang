<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;

class SiswaDashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $siswa->load('kelas');

        $idSiswa = $siswa->id_siswa;

        $baseQuery = Presensi::where('id_siswa', $idSiswa);

        // 'terlambat' dihitung sebagai hadir (siswa tetap masuk sekolah)
        $totalHadir = (clone $baseQuery)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalIzin  = (clone $baseQuery)->where('status', 'izin')->count();
        $totalSakit = (clone $baseQuery)->where('status', 'sakit')->count();
        $totalAlpa  = (clone $baseQuery)->where('status', 'alpa')->count();

        $riwayatPresensi = (clone $baseQuery)
            ->orderByDesc('tgl_presensi')
            ->paginate(10);

        return view('siswa.dashboard', compact(
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'riwayatPresensi',
        ));
    }
}
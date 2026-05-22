<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class PresensiAdminController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();

        $presensiHariIni = Presensi::with(['siswa.kelas'])
                            ->where('tgl_presensi', $hariIni)
                            ->orderBy('jam_masuk', 'desc')
                            ->get();

        $totalSiswa      = Siswa::count();
        $totalHadir      = $presensiHariIni->where('status', 'Hadir')->count();
        $totalTerlambat  = $presensiHariIni->where('status', 'Terlambat')->count();
        $totalBelumAbsen = $totalSiswa - $presensiHariIni->count();

        return view('admin.kelolapresensi.presensi-siswa', compact(
            'presensiHariIni',
            'totalSiswa',
            'totalHadir',
            'totalTerlambat',
            'totalBelumAbsen'
        ));
    }
}
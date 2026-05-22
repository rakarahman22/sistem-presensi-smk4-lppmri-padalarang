<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Kelas;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing page.
     */
    public function index()
    {
        // Statistik untuk ditampilkan di hero
        $stats = [
            'total_siswa'   => Siswa::where('status', 'aktif')->count(),
            'kelas_aktif'   => Kelas::where('status', 'aktif')->count(),
            'kehadiran'     => $this->getPersentaseKehadiran(),
        ];

        return view('pages.landing', compact('stats'));
    }

    /**
     * Hitung persentase kehadiran hari ini.
     */
    private function getPersentaseKehadiran(): string
    {
        $today = now()->toDateString();

        $totalSiswa  = Siswa::where('status', 'aktif')->count();
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
                                ->where('status', 'hadir')
                                ->count();

        if ($totalSiswa === 0) {
            return '0%';
        }

        $persen = round(($hadirHariIni / $totalSiswa) * 100);
        return $persen . '%';
    }
}
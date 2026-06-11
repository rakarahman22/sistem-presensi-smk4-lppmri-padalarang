<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $hari_ini = Carbon::today();

        // ── STAT CARDS ──────────────────────────────────────────────

        $totalSiswa = Siswa::count();

        $hadirHariIni = Presensi::whereDate('tgl_presensi', $hari_ini)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $alpaHariIni = Presensi::whereDate('tgl_presensi', $hari_ini)
            ->where('status', 'alpa')
            ->count();

        // Izin + Sakit digabung sebagai "Tidak Hadir Bereterangan"
        $tidakHadirHariIni = Presensi::whereDate('tgl_presensi', $hari_ini)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        // Persentase kehadiran hari ini
        $totalPresensiHariIni = $hadirHariIni + $alpaHariIni + $tidakHadirHariIni;
        $pctHadir = $totalPresensiHariIni > 0
            ? round(($hadirHariIni / $totalPresensiHariIni) * 100)
            : 0;

        // ── GRAFIK — Minggu ini Senin s/d Jumat ─────────────────────

        // Cari Senin minggu ini
        $senin  = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $jumat  = Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4);

        $hariLabel  = [];
        $dataHadir  = [];
        $dataAlpa   = [];
        $dataIzin   = [];
        $dataSakit  = [];

        for ($i = 0; $i <= 4; $i++) {
            $tgl = $senin->copy()->addDays($i);

            $hariLabel[] = $tgl->translatedFormat('D, d M'); // e.g. "Sen, 09 Jun"

            $dataHadir[] = Presensi::whereDate('tgl_presensi', $tgl)
                ->whereIn('status', ['hadir', 'terlambat'])->count();

            $dataAlpa[]  = Presensi::whereDate('tgl_presensi', $tgl)
                ->where('status', 'alpa')->count();

            $dataIzin[]  = Presensi::whereDate('tgl_presensi', $tgl)
                ->where('status', 'izin')->count();

            $dataSakit[] = Presensi::whereDate('tgl_presensi', $tgl)
                ->where('status', 'sakit')->count();
        }

        // ── TABEL SISWA ALPA HARI INI ────────────────────────────────

        $siswaAlpaHariIni = Presensi::with(['siswa.kelas'])
            ->whereDate('tgl_presensi', $hari_ini)
            ->where('status', 'alpa')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ── REKAP PER KELAS HARI INI ─────────────────────────────────

        $rekapKelas = Kelas::withCount([
            'siswa as total_siswa',
        ])
        ->get()
        ->map(function ($kelas) use ($hari_ini) {
            $hadir = Presensi::whereHas('siswa', fn($q) => $q->where('id_kelas', $kelas->id_kelas))
                ->whereDate('tgl_presensi', $hari_ini)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->count();

            $alpa = Presensi::whereHas('siswa', fn($q) => $q->where('id_kelas', $kelas->id_kelas))
                ->whereDate('tgl_presensi', $hari_ini)
                ->where('status', 'alpa')
                ->count();

            $izinSakit = Presensi::whereHas('siswa', fn($q) => $q->where('id_kelas', $kelas->id_kelas))
                ->whereDate('tgl_presensi', $hari_ini)
                ->whereIn('status', ['izin', 'sakit'])
                ->count();

            $total = $hadir + $alpa + $izinSakit;
            $pct   = $total > 0 ? round(($hadir / $total) * 100) : 0;

            return [
                'nama_kelas'   => $kelas->nama_kelas,
                'jurusan'      => $kelas->jurusan,
                'total_siswa'  => $kelas->total_siswa,
                'hadir'        => $hadir,
                'alpa'         => $alpa,
                'izin_sakit'   => $izinSakit,
                'pct_hadir'    => $pct,
            ];
        })
        ->sortByDesc('alpa')
        ->values();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
            'alpaHariIni',
            'tidakHadirHariIni',
            'pctHadir',
            'hariLabel',
            'dataHadir',
            'dataAlpa',
            'dataIzin',
            'dataSakit',
            'siswaAlpaHariIni',
            'rekapKelas',
        ));
    }
}
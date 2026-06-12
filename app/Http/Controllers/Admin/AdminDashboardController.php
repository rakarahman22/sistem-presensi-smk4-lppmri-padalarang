<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
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

        $tidakHadirHariIni = Presensi::whereDate('tgl_presensi', $hari_ini)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        $totalPresensiHariIni = $hadirHariIni + $alpaHariIni + $tidakHadirHariIni;
        $pctHadir = $totalPresensiHariIni > 0
            ? round(($hadirHariIni / $totalPresensiHariIni) * 100)
            : 0;

        // ── GRAFIK — Minggu ini Senin s/d Jumat ─────────────────────

        $senin = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $hariLabel = [];
        $dataHadir = [];
        $dataAlpa  = [];
        $dataIzin  = [];
        $dataSakit = [];

        for ($i = 0; $i <= 4; $i++) {
            $tgl = $senin->copy()->addDays($i);

            $hariLabel[] = $tgl->translatedFormat('D, d M');

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
        // perPage bisa dipilih admin: 10 / 20 / 50 / 100
        $perPage  = in_array((int) request('per_page'), [10, 20, 50, 100])
            ? (int) request('per_page')
            : 10;

        $siswaAlpaHariIni = Presensi::with(['siswa.kelas'])
            ->whereDate('tgl_presensi', $hari_ini)
            ->where('status', 'alpa')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // ── REKAP PER KELAS HARI INI ─────────────────────────────────

        $rekapKelas = Kelas::withCount(['siswa as total_siswa'])
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

                return [
                    'nama_kelas'  => $kelas->nama_kelas,
                    'tingkat'     => $kelas->tingkat,
                    'jurusan'     => $kelas->jurusan,
                    'total_siswa' => $kelas->total_siswa,
                    'hadir'       => $hadir,
                    'alpa'        => $alpa,
                    'izin_sakit'  => $izinSakit,
                ];
            })
            // ── Urutkan: Tingkat → Jurusan → Nama Kelas ─────────────────
            ->sortBy([
                ['tingkat', 'asc'],
                ['jurusan', 'asc'],
                ['nama_kelas', 'asc'],
            ])
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
            'perPage',
            'rekapKelas',
        ));
    }

    // ── REKAP ALPA — Halaman lengkap dengan filter & pagination ──────
    public function alpa(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        $query = Presensi::with(['siswa.kelas'])
            ->where('status', 'alpa')
            ->whereDate('tgl_presensi', $tanggal);

        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', fn($q) => $q->where('id_kelas', $request->id_kelas));
        }

        $siswaAlpa = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $totalAlpa = Presensi::where('status', 'alpa')->whereDate('tgl_presensi', $tanggal)->count();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.rekap.alpa', compact(
            'siswaAlpa',
            'totalAlpa',
            'tanggal',
            'kelasList',
        ));
    }
}

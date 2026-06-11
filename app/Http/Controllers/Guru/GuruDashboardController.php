<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mengajar;
use App\Models\PresensiMapel;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $guru      = auth()->guard('guru')->user();
        $idGuru    = $guru->id_guru;
        $hariIni   = Carbon::today()->toDateString();
        $bulanIni  = Carbon::now()->month;
        $tahunIni  = Carbon::now()->year;

        // ── 1. STAT CARDS ──────────────────────────────────────────────

        // Total sesi mengajar hari ini
        $sesiHariIni = Mengajar::where('id_guru', $idGuru)
                            ->where('tgl_mengajar', $hariIni)
                            ->count();

        // Total sesi bulan ini
        $sesiBulanIni = Mengajar::where('id_guru', $idGuru)
                            ->whereMonth('tgl_mengajar', $bulanIni)
                            ->whereYear('tgl_mengajar', $tahunIni)
                            ->count();

        // Jumlah kelas unik yang diajar bulan ini
        $jumlahKelas = Mengajar::where('id_guru', $idGuru)
                            ->whereMonth('tgl_mengajar', $bulanIni)
                            ->whereYear('tgl_mengajar', $tahunIni)
                            ->distinct('id_kelas')
                            ->count('id_kelas');

        // Jumlah mapel unik yang diajar bulan ini
        $jumlahMapel = Mengajar::where('id_guru', $idGuru)
                            ->whereMonth('tgl_mengajar', $bulanIni)
                            ->whereYear('tgl_mengajar', $tahunIni)
                            ->distinct('nama_mapel')
                            ->count('nama_mapel');

        // ── 2. REKAP ABSEN PER MAPEL (bulan ini) ──────────────────────

        // Ambil semua sesi bulan ini milik guru ini
        $semuaSesiIds = Mengajar::where('id_guru', $idGuru)
                            ->whereMonth('tgl_mengajar', $bulanIni)
                            ->whereYear('tgl_mengajar', $tahunIni)
                            ->pluck('id_mengajar');

        // Group by nama_mapel + id_kelas untuk tabel rekap
        $rekapPerMapel = Mengajar::with('kelas')
                            ->where('id_guru', $idGuru)
                            ->whereMonth('tgl_mengajar', $bulanIni)
                            ->whereYear('tgl_mengajar', $tahunIni)
                            ->selectRaw('nama_mapel, id_kelas, COUNT(*) as total_pertemuan')
                            ->groupBy('nama_mapel', 'id_kelas')
                            ->get()
                            ->map(function ($row) use ($idGuru, $bulanIni, $tahunIni) {
                                // Ambil semua id_mengajar untuk kombinasi mapel+kelas ini
                                $ids = Mengajar::where('id_guru', $idGuru)
                                        ->where('nama_mapel', $row->nama_mapel)
                                        ->where('id_kelas', $row->id_kelas)
                                        ->whereMonth('tgl_mengajar', $bulanIni)
                                        ->whereYear('tgl_mengajar', $tahunIni)
                                        ->pluck('id_mengajar');

                                $total  = PresensiMapel::whereIn('id_mengajar', $ids)->count();
                                $hadir  = PresensiMapel::whereIn('id_mengajar', $ids)->where('status', 'Hadir')->count();
                                $sakit  = PresensiMapel::whereIn('id_mengajar', $ids)->where('status', 'Sakit')->count();
                                $izin   = PresensiMapel::whereIn('id_mengajar', $ids)->where('status', 'Izin')->count();
                                $alpa   = PresensiMapel::whereIn('id_mengajar', $ids)->where('status', 'Alpa')->count();

                                $row->total_hadir  = $hadir;
                                $row->total_sakit  = $sakit;
                                $row->total_izin   = $izin;
                                $row->total_alpa   = $alpa;
                                $row->persen_hadir = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

                                return $row;
                            });

        // ── 3. SESI HARI INI (untuk tabel bawah) ──────────────────────
        $sesiAktifHariIni = Mengajar::with('kelas')
                                ->where('id_guru', $idGuru)
                                ->where('tgl_mengajar', $hariIni)
                                ->orderBy('jam_mulai', 'asc')
                                ->get()
                                ->map(function ($s) use ($idGuru) {
                                    $s->pertemuan_ke = Mengajar::where('id_guru', $idGuru)
                                        ->where('id_kelas', $s->id_kelas)
                                        ->where('nama_mapel', $s->nama_mapel)
                                        ->where('id_mengajar', '<=', $s->id_mengajar)
                                        ->count();
                                    return $s;
                                });

        return view('guru.dashboard', compact(
            'guru',
            'sesiHariIni',
            'sesiBulanIni',
            'jumlahKelas',
            'jumlahMapel',
            'rekapPerMapel',
            'sesiAktifHariIni',
            'bulanIni',
            'tahunIni'
        ));
    }
}
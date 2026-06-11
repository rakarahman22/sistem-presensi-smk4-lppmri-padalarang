<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Mengajar;
use App\Models\PresensiMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RiwayatAbsenMapelController extends Controller
{
    // =========================================================================
    // 1. Halaman utama — kirim daftar mapel unik milik siswa ke view
    // =========================================================================
    public function index()
    {
        $siswa = auth()->guard('siswa')->user();

        // Ambil semua nama mapel unik yang pernah tercatat untuk siswa ini
        $daftarMapel = PresensiMapel::with('mengajar')
            ->where('id_siswa', $siswa->id_siswa)
            ->get()
            ->pluck('mengajar.nama_mapel')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('siswa.riwayat-absen-mapel', compact('daftarMapel'));
    }

    // =========================================================================
    // 2. Endpoint AJAX — kembalikan data JSON untuk filter bulan/tahun/mapel
    // =========================================================================
    public function data(Request $request)
    {
        $siswa  = auth()->guard('siswa')->user();
        $bulan  = (int) ($request->bulan ?? now()->month);
        $tahun  = (int) ($request->tahun ?? now()->year);
        $mapel  = $request->mapel ?? ''; // string nama mapel, kosong = semua

        // ── Ambil semua id_mengajar yang cocok dengan bulan/tahun ──────────
        $queryMengajar = Mengajar::whereYear('tgl_mengajar', $tahun)
            ->whereMonth('tgl_mengajar', $bulan);

        if ($mapel !== '') {
            $queryMengajar->where('nama_mapel', $mapel);
        }

        $idMengajars = $queryMengajar->pluck('id_mengajar');

        // ── Ambil data presensi siswa pada sesi-sesi tersebut ──────────────
        $presensi = PresensiMapel::with('mengajar')
            ->where('id_siswa', $siswa->id_siswa)
            ->whereIn('id_mengajar', $idMengajars)
            ->get()
            ->sortBy('mengajar.tgl_mengajar');

        // ── Hitung statistik global (untuk stat cards) ─────────────────────
        $stats = [
            'hadir' => $presensi->where('status', 'Hadir')->count(),
            'sakit' => $presensi->where('status', 'Sakit')->count(),
            'izin'  => $presensi->where('status', 'Izin')->count(),
            'alpa'  => $presensi->where('status', 'Alpa')->count(),
        ];

        // ── Hitung ringkasan persentase per mapel ──────────────────────────
        $ringkasanPerMapel = $presensi
            ->groupBy('mengajar.nama_mapel')
            ->map(function ($group, $namaMapel) {
                $total  = $group->count();
                $hadir  = $group->where('status', 'Hadir')->count();
                $sakit  = $group->where('status', 'Sakit')->count();
                $izin   = $group->where('status', 'Izin')->count();
                $alpa   = $group->where('status', 'Alpa')->count();
                $persen = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

                return [
                    'nama_mapel'       => $namaMapel,
                    'total_pertemuan'  => $total,
                    'hadir'            => $hadir,
                    'sakit'            => $sakit,
                    'izin'             => $izin,
                    'alpa'             => $alpa,
                    'persen_hadir'     => $persen,
                    'lulus'            => $persen >= 75,
                ];
            })
            ->values();

        // ── Susun baris tabel detail ───────────────────────────────────────
        $rows = $presensi->map(function ($item) {
            $tgl = Carbon::parse($item->mengajar->tgl_mengajar ?? null);
            return [
                'nama_mapel'   => $item->mengajar->nama_mapel ?? '-',
                'tgl_formatted'  => $tgl->translatedFormat('d M Y'),
                'hari_formatted' => $tgl->translatedFormat('l'),
                'jam_mulai'    => $item->mengajar->jam_mulai
                    ? Carbon::parse($item->mengajar->jam_mulai)->format('H:i')
                    : '-',
                'pertemuan_ke' => null, // dihitung di bawah jika dibutuhkan
                'status'       => $item->status,
            ];
        })->values();

        return response()->json([
            'stats'              => $stats,
            'ringkasan_per_mapel' => $ringkasanPerMapel,
            'rows'               => $rows,
        ]);
    }
}
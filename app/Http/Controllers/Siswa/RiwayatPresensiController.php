<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatPresensiController extends Controller
{
    // Halaman utama (view saja, data diambil via AJAX)
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $siswa->load('kelas');

        return view('siswa.riwayat-presensi');
    }

    // Endpoint AJAX — dipanggil oleh JavaScript filter realtime
    public function data(Request $request)
    {
        $siswa      = Auth::guard('siswa')->user();
        $bulan      = (int) $request->get('bulan', now()->month);
        $tahun      = (int) $request->get('tahun', now()->year);

        $baseQuery = Presensi::where('id_siswa', $siswa->id_siswa)
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun);

        // Statistik per status
        $totalHadir = (clone $baseQuery)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalIzin  = (clone $baseQuery)->where('status', 'izin')->count();
        $totalSakit = (clone $baseQuery)->where('status', 'sakit')->count();
        $totalAlpa  = (clone $baseQuery)->where('status', 'alpa')->count();

        // Paginate data tabel — 15 per halaman
        $presensi = (clone $baseQuery)
            ->orderByDesc('tgl_presensi')
            ->paginate(15);

        // Format tiap baris agar siap dikonsumsi JS
        $rows = $presensi->map(function ($item) {
            return [
                'tgl_formatted'  => Carbon::parse($item->tgl_presensi)->translatedFormat('d M Y'),
                'hari_formatted' => Carbon::parse($item->tgl_presensi)->translatedFormat('l'),
                'jam_masuk'      => $item->jam_masuk
                    ? Carbon::parse($item->jam_masuk)->format('H:i')
                    : null,
                'status'         => $item->status,
                'status_awal'    => $item->status_awal,
                'keterangan'     => $item->keterangan,
            ];
        });

        return response()->json([
            'stats' => [
                'hadir' => $totalHadir,
                'izin'  => $totalIzin,
                'sakit' => $totalSakit,
                'alpa'  => $totalAlpa,
            ],
            'rows'         => $rows,
            'current_page' => $presensi->currentPage(),
            'last_page'    => $presensi->lastPage(),
            'from'         => $presensi->firstItem() ?? 0,
            'to'           => $presensi->lastItem()  ?? 0,
            'total'        => $presensi->total(),
        ]);
    }
}
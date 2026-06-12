<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\RekapPresensiExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminLaporanController extends Controller
{
    /**
     * Halaman filter laporan presensi
     */
    public function presensiIndex(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $tglDari   = $request->input('tgl_dari') ?: null;
        $tglSampai = $request->input('tgl_sampai') ?: null;

        return view('admin.laporan.presensi', compact(
            'bulan',
            'tahun',
            'tglDari',
            'tglSampai'
        ));
    }

    /**
     * Generate & download file Excel rekap presensi
     */
    public function presensiExport(Request $request)
    {
        $tglDari   = $request->input('tgl_dari') ?: null;
        $tglSampai = $request->input('tgl_sampai') ?: null;

        if ($tglDari && $tglSampai) {
            $awal  = Carbon::parse($tglDari);
            $akhir = Carbon::parse($tglSampai);
        } else {
            $bulan = (int) $request->input('bulan', now()->month);
            $tahun = (int) $request->input('tahun', now()->year);

            $awal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $akhir = $awal->copy()->endOfMonth();
        }

        // Validasi sederhana: batasi maksimal range 1 tahun agar tidak terlalu berat
        if ($awal->diffInDays($akhir) > 366) {
            return back()->withErrors(['tgl_dari' => 'Rentang tanggal maksimal 1 tahun.']);
        }

        $namaFile = 'rekap-presensi_' . $awal->format('Ymd') . '_' . $akhir->format('Ymd') . '.xlsx';

        return Excel::download(
            new RekapPresensiExport($awal->format('Y-m-d'), $akhir->format('Y-m-d')),
            $namaFile
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiAdminController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', Carbon::today()->toDateString());

        $presensiHariIni = Presensi::with(['siswa.kelas'])
            ->where('tgl_presensi', $tanggal)
            ->orderBy('jam_masuk', 'asc')
            ->get();

        $totalSiswa      = Siswa::count();
        $totalHadir      = $presensiHariIni->where('status', 'Hadir')->count();
        $totalTerlambat  = $presensiHariIni->where('status', 'Terlambat')->count();
        $totalAlpa       = $presensiHariIni->where('status', 'Alpa')->count();
        $totalIzinSakit  = $presensiHariIni->whereIn('status', ['Izin', 'Sakit'])->count();
        $totalBelumAbsen = $totalSiswa - $presensiHariIni->count();

        return view('admin.kelolapresensi.presensi-siswa', compact(
            'presensiHariIni',
            'totalSiswa',
            'totalHadir',
            'totalTerlambat',
            'totalAlpa',
            'totalIzinSakit',
            'totalBelumAbsen',
            'tanggal'
        ));
    }

    public function koreksi(Request $request, $id)
    {
        $request->validate([
            'status'     => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $presensi = Presensi::findOrFail($id);

        // Simpan status lama ke status_awal HANYA jika belum pernah dikoreksi sebelumnya.
        // Tujuannya agar status_awal selalu menyimpan status ASLI dari siswa, bukan hasil koreksi sebelumnya.
        if (empty($presensi->status_awal)) {
            $presensi->status_awal = $presensi->status;
        }

        // Nama admin yang melakukan koreksi
        $admin = Auth::guard('admin')->user();
        $presensi->dikoreksi_oleh = $admin->nama_admin ?? $admin->username ?? 'Admin';

        // Update status dan keterangan
        $presensi->status     = $request->status;
        $presensi->keterangan = $request->keterangan;
        $presensi->edited_at  = Carbon::now();

        $presensi->save();

        return back()->with(
            'success',
            '✅ Presensi ' . ($presensi->siswa->nama_siswa ?? 'siswa') . ' berhasil dikoreksi menjadi ' . $request->status . '!'
        );
    }
}
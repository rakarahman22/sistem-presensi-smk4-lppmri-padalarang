<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WaliController extends Controller
{
    /**
     * Helper privat untuk membagikan data inisial dan notifikasi ke sidebar global
     */
    private function shareSidebarData($wali)
    {
        $jumlahNotif = 0;
        $inisial     = '';

        if ($wali && $wali->nama_wali) {
            $namaArr = explode(' ', $wali->nama_wali);
            $inisial = strtoupper(substr($namaArr[0], 0, 1) . (isset($namaArr[1]) ? substr($namaArr[1], 0, 1) : ''));
        }

        view()->share([
            'wali'        => $wali,
            'jumlahNotif' => $jumlahNotif,
            'inisial'     => $inisial,
        ]);
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard()
    {
        $wali      = Auth::guard('wali')->user();
        $siswaList = $wali->siswa()->with('kelas')->get();

        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin  = 0;
        $totalAlpha = 0;

        foreach ($siswaList as $s) {
            $totalHadir += $s->presensi()->whereMonth('tgl_presensi', now()->month)->whereYear('tgl_presensi', now()->year)->where('status', 'Hadir')->count();
            $totalSakit += $s->presensi()->whereMonth('tgl_presensi', now()->month)->whereYear('tgl_presensi', now()->year)->where('status', 'Sakit')->count();
            $totalIzin  += $s->presensi()->whereMonth('tgl_presensi', now()->month)->whereYear('tgl_presensi', now()->year)->where('status', 'Izin')->count();
            $totalAlpha += $s->presensi()->whereMonth('tgl_presensi', now()->month)->whereYear('tgl_presensi', now()->year)->where('status', 'Alpa')->count();
        }

        $this->shareSidebarData($wali);

        return view('wali.dashboard', compact(
            'wali', 'siswaList', 'totalHadir', 'totalSakit', 'totalIzin', 'totalAlpha'
        ));
    }

    // =========================================================================
    // RIWAYAT KEHADIRAN ANAK
    // =========================================================================
    public function riwayatKehadiran(Request $request)
    {
        $wali      = Auth::guard('wali')->user();
        $siswaList = $wali->siswa()->with('kelas')->get();

        // ── Parameter filter ──────────────────────────────────
        // Cast ke (int) wajib — request selalu mengirim string, Carbon menolak string di whereMonth/whereYear
        $bulan         = (int) $request->input('bulan', now()->month);
        $tahun         = (int) $request->input('tahun', now()->year);
        $idSiswaFilter = $request->input('id_siswa');

        // null-kan string kosong agar kondisi ($tglDari && $tglSampai) tidak lolos
        // ketika user mengosongkan input lalu klik filter
        $tglDari   = $request->input('tgl_dari')   ?: null;
        $tglSampai = $request->input('tgl_sampai') ?: null;

        // ── Ambil data presensi ───────────────────────────────
        $riwayat = collect();

        foreach ($siswaList as $siswa) {
            if ($idSiswaFilter && $siswa->id_siswa != $idSiswaFilter) continue;

            $query = $siswa->presensi();

            // Prioritaskan filter tanggal spesifik jika keduanya diisi
            if ($tglDari && $tglSampai) {
                $query->whereBetween('tgl_presensi', [$tglDari, $tglSampai]);
            } else {
                $query->whereMonth('tgl_presensi', $bulan)
                      ->whereYear('tgl_presensi', $tahun);
            }

            $presensi = $query
                ->orderBy('tgl_presensi', 'desc')
                ->get()
                ->map(function ($p) use ($siswa) {
                    $p->nama_siswa = $siswa->nama_siswa;
                    $p->kelas      = $siswa->kelas->nama_kelas ?? '-';
                    return $p;
                });

            $riwayat = $riwayat->merge($presensi);
        }

        $riwayat = $riwayat->sortByDesc('tgl_presensi')->values();

        $this->shareSidebarData($wali);

        return view('wali.riwayat-kehadiran', compact(
            'wali',
            'siswaList',
            'riwayat',
            'bulan',
            'tahun',
            'idSiswaFilter',
            'tglDari',       // <-- BARU, dibutuhkan view untuk toggle filter & label progress bar
            'tglSampai'      // <-- BARU
        ));
    }

    // =========================================================================
    // PROFIL SAYA
    // =========================================================================
    public function profil()
    {
        $wali = Auth::guard('wali')->user();
        $this->shareSidebarData($wali);

        return view('wali.profil', compact('wali'));
    }

    // =========================================================================
    // NOTIFIKASI
    // =========================================================================
    public function notifikasi()
    {
        $wali = Auth::guard('wali')->user();
        $this->shareSidebarData($wali);

        return view('wali.notifikasi', compact('wali'));
    }

    // =========================================================================
    // UPDATE PROFIL
    // =========================================================================
    public function updateProfil(Request $request)
    {
        $wali = Auth::guard('wali')->user();

        $request->validate([
            'nama_wali' => 'required|string|max:100',
            'no_telp'   => 'nullable|string|max:20',
            'username'  => 'required|string|max:50|unique:wali_siswas,username,' . $wali->id_wali . ',id_wali',
        ], [
            'nama_wali.required' => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
        ]);

        $wali->update([
            'nama_wali' => $request->nama_wali,
            'no_telp'   => $request->no_telp,
            'username'  => $request->username,
        ]);

        return redirect()->route('wali.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    // =========================================================================
    // GANTI PASSWORD
    // =========================================================================
    public function gantiPassword(Request $request)
    {
        $wali = Auth::guard('wali')->user();

        $request->validate([
            'password_lama'              => 'required',
            'password_baru'              => 'required|min:6|confirmed',
            'password_baru_confirmation' => 'required',
        ], [
            'password_lama.required'  => 'Password lama wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->password_lama, $wali->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $wali->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->route('wali.profil')->with('success', 'Password berhasil diperbarui.');
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================
    public function logout(Request $request)
    {
        Auth::guard('wali')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
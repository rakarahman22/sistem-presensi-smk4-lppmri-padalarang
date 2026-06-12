<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
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

        // ── Tentukan id_siswa mana saja yang boleh diakses wali ini ──
        $idSiswaList = $siswaList->pluck('id_siswa');

        // Kalau filter nama anak diisi, pastikan id tersebut memang milik wali ini
        if ($idSiswaFilter && $idSiswaList->contains($idSiswaFilter)) {
            $idSiswaIds = [$idSiswaFilter];
        } else {
            // Kalau id_siswa tidak valid / tidak diisi, gunakan semua anak milik wali
            $idSiswaFilter = $idSiswaFilter && $idSiswaList->contains($idSiswaFilter) ? $idSiswaFilter : ($idSiswaFilter ?: null);
            $idSiswaIds    = $idSiswaList->all();
        }

        // ── Base query (belum di-get / paginate) ──────────────
        $baseQuery = Presensi::query()
            ->with('siswa.kelas')
            ->whereIn('id_siswa', $idSiswaIds)
            ->when($tglDari && $tglSampai, function ($q) use ($tglDari, $tglSampai) {
                $q->whereBetween('tgl_presensi', [$tglDari, $tglSampai]);
            })
            ->when(!($tglDari && $tglSampai), function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tgl_presensi', $bulan)
                  ->whereYear('tgl_presensi', $tahun);
            });

        // ── Rekap dihitung dari SELURUH data periode (clone, tanpa pagination) ──
        $rekapData = (clone $baseQuery)->get();

        $cHadir     = $rekapData->where('status', 'Hadir')->count();
        $cTerlambat = $rekapData->where('status', 'Terlambat')->count();
        $cSakit     = $rekapData->where('status', 'Sakit')->count();
        $cIzin      = $rekapData->where('status', 'Izin')->count();
        $cAlpha     = $rekapData->whereIn('status', ['Alpa', 'Alpha'])->count();

        $totalHari  = $rekapData->count();
        $persentase = $totalHari > 0 ? round((($cHadir + $cTerlambat) / $totalHari) * 100) : 0;

        // ── Data tabel: pagination, terbaru dulu ──────────────
        $perPage = in_array((int) $request->input('per_page'), [10, 20, 50])
            ? (int) $request->input('per_page')
            : 15;

        $riwayat = (clone $baseQuery)
            ->orderBy('tgl_presensi', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // ── Tambahkan nama_siswa & kelas ke setiap item (dipakai view) ──
        $riwayat->getCollection()->transform(function ($p) {
            $p->nama_siswa = $p->siswa->nama_siswa ?? '-';
            $p->kelas      = $p->siswa->kelas->nama_kelas ?? '-';
            return $p;
        });

        $this->shareSidebarData($wali);

        return view('wali.riwayat-kehadiran', compact(
            'wali',
            'siswaList',
            'riwayat',
            'bulan',
            'tahun',
            'idSiswaFilter',
            'tglDari',
            'tglSampai',
            'perPage',
            'cHadir',
            'cTerlambat',
            'cSakit',
            'cIzin',
            'cAlpha',
            'totalHari',
            'persentase',
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
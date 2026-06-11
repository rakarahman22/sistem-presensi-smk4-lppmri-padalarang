<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mengajar;
use App\Models\PresensiMapel;
use App\Models\Mapel;
use App\Models\PlotMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsenMapelController extends Controller
{
    // =========================================================================
    // 1. Halaman utama: buka sesi + riwayat hari ini + riwayat pertemuan lalu
    // =========================================================================
    public function index()
    {
        $guru = auth()->user();

        // Ambil plotting resmi admin untuk guru ini
        $plotSesi = PlotMengajar::with(['kelas', 'mapel'])
            ->where('id_guru', $guru->id_guru)
            ->get();

        // Daftar kelas unik dari plotting
        $daftarKelas = $plotSesi->pluck('kelas')->unique('id_kelas')->filter()->values();

        // Dropdown mapel awal dikosongkan — diisi dinamis via AJAX setelah kelas dipilih
        $daftarMasterMapel = collect();

        // Riwayat sesi mengajar hari ini
        $riwayatHariIni = Mengajar::with('kelas')
            ->where('id_guru', $guru->id_guru)
            ->where('tgl_mengajar', Carbon::today()->toDateString())
            ->orderBy('jam_mulai', 'desc')
            ->get();

        foreach ($riwayatHariIni as $riwayat) {
            $riwayat->pertemuan_ke = Mengajar::where('id_guru', $guru->id_guru)
                ->where('id_kelas', $riwayat->id_kelas)
                ->where('nama_mapel', $riwayat->nama_mapel)
                ->where('id_mengajar', '<=', $riwayat->id_mengajar)
                ->count();
        }

        // ── BARU: Riwayat sesi mengajar SEBELUM hari ini (max 50 record terbaru)
        $riwayatLalu = Mengajar::with('kelas')
            ->where('id_guru', $guru->id_guru)
            ->where('tgl_mengajar', '<', Carbon::today()->toDateString())
            ->orderBy('tgl_mengajar', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->limit(50)
            ->get();

        foreach ($riwayatLalu as $riwayat) {
            $riwayat->pertemuan_ke = Mengajar::where('id_guru', $guru->id_guru)
                ->where('id_kelas', $riwayat->id_kelas)
                ->where('nama_mapel', $riwayat->nama_mapel)
                ->where('id_mengajar', '<=', $riwayat->id_mengajar)
                ->count();
        }

        return view('guru.absen-mapel.index', compact(
            'daftarKelas',
            'daftarMasterMapel',
            'riwayatHariIni',
            'riwayatLalu'
        ));
    }

    // =========================================================================
    // 2. Proses membuka sesi mengajar baru
    // =========================================================================
    public function storeSesi(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id_mapel',
        ]);

        $guru      = auth()->user();
        $hariIni   = Carbon::today()->toDateString();
        $mapelMaster = Mapel::findOrFail($request->id_mapel);

        $sesiMengajar = Mengajar::create([
            'id_guru'      => $guru->id_guru,
            'id_kelas'     => $request->id_kelas,
            'nama_mapel'   => $mapelMaster->nama_mapel,
            'tgl_mengajar' => $hariIni,
            'jam_mulai'    => Carbon::now()->toTimeString(),
        ]);

        $daftarSiswa = Siswa::where('id_kelas', $request->id_kelas)->get();

        if ($daftarSiswa->isEmpty()) {
            $sesiMengajar->delete();
            return redirect()->back()->with('error', 'Gagal membuka absen! Tidak ada siswa di kelas tersebut.');
        }

        foreach ($daftarSiswa as $siswa) {
            PresensiMapel::create([
                'id_mengajar' => $sesiMengajar->id_mengajar,
                'id_siswa'    => $siswa->id_siswa,
                'status'      => 'Hadir',
            ]);
        }

        return redirect()->route('guru.absen-mapel.isi', $sesiMengajar->id_mengajar)
            ->with('success', 'Sesi mapel berhasil dibuka! Silakan sesuaikan kehadiran siswa jika ada yang absen.');
    }

    // =========================================================================
    // 3. Tampilkan lembar absensi siswa (selalu bisa diedit)
    // =========================================================================
    public function isiAbsen($id_mengajar)
    {
        $sesi = Mengajar::with('kelas')->findOrFail($id_mengajar);

        if ($sesi->id_guru !== auth()->user()->id_guru) {
            abort(403, 'Anda tidak memiliki akses ke sesi mengajar ini.');
        }

        $presensiSiswa = PresensiMapel::with('siswa')
            ->where('id_mengajar', $id_mengajar)
            ->get();

        // Selalu bisa diedit — $readOnly selalu false
        $readOnly = false;

        // Tandai apakah sesi ini dari pertemuan lalu (untuk label info di header)
        $adalahPertemuanLalu = $sesi->tgl_mengajar !== Carbon::today()->toDateString();

        // Hitung nomor pertemuan untuk ditampilkan di header
        $pertemuanKe = Mengajar::where('id_guru', auth()->user()->id_guru)
            ->where('id_kelas', $sesi->id_kelas)
            ->where('nama_mapel', $sesi->nama_mapel)
            ->where('id_mengajar', '<=', $sesi->id_mengajar)
            ->count();

        return view('guru.absen-mapel.isi', compact(
            'sesi',
            'presensiSiswa',
            'readOnly',
            'adalahPertemuanLalu',
            'pertemuanKe'
        ));
    }

    // =========================================================================
    // 4. Proses update status absensi (boleh edit kapan saja)
    // =========================================================================
    public function updateAbsen(Request $request, $id_mengajar)
    {
        $sesi = Mengajar::findOrFail($id_mengajar);

        if ($sesi->id_guru !== auth()->user()->id_guru) {
            abort(403);
        }

        $request->validate([
            'status'   => 'required|array',
            'status.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
        ]);

        foreach ($request->status as $id_presensi_mapel => $statusSiswa) {
            PresensiMapel::where('id_presensi_mapel', $id_presensi_mapel)
                ->update(['status' => $statusSiswa]);
        }

        // Setelah simpan, kembali ke halaman yang sesuai (hari ini atau lalu)
        $kembaliKe = $sesi->tgl_mengajar === Carbon::today()->toDateString()
            ? route('guru.absen-mapel.index')
            : route('guru.absen-mapel.index', ['#tab-lalu']);

        return redirect()->route('guru.absen-mapel.index')
            ->with('success', '✅ Data absensi berhasil diperbarui.');
    }

    // =========================================================================
    // 4b. Hapus sesi mengajar beserta seluruh data presensinya (BARU)
    // =========================================================================
    public function hapusSesi($id_mengajar)
    {
        $sesi = Mengajar::findOrFail($id_mengajar);

        // Keamanan: hanya guru pemilik sesi yang boleh hapus
        if ($sesi->id_guru !== auth()->user()->id_guru) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus sesi ini.');
        }

        // Hapus semua data presensi siswa yang terkait dulu (cascade manual)
        PresensiMapel::where('id_mengajar', $id_mengajar)->delete();

        // Baru hapus induk sesi mengajar
        $sesi->delete();

        return redirect()->route('guru.absen-mapel.index')
            ->with('success', '🗑️ Sesi mengajar berhasil dihapus beserta seluruh data absensinya.');
    }

    // =========================================================================
    // 5. Halaman filter rekap (pilih kelas → mapel dinamis via AJAX)
    // =========================================================================
    public function rekapIndex()
    {
        $guru = auth()->user();

        $plotSesi = PlotMengajar::with(['kelas', 'mapel'])
            ->where('id_guru', $guru->id_guru)
            ->get();

        $daftarKelas = $plotSesi->pluck('kelas')->unique('id_kelas')->filter()->values();

        // Mapel awal dikosongkan — diisi dinamis via AJAX setelah kelas dipilih
        $daftarMapel = collect();

        return view('guru.absen-mapel.rekap-index', compact('daftarKelas', 'daftarMapel'));
    }

    // =========================================================================
    // 6. Tampilkan tabel rekapitulasi absensi
    // =========================================================================
    public function rekapTampil(Request $request)
    {
        $request->validate([
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'nama_mapel' => 'required|string',
        ]);

        $guru      = auth()->user();
        $kelas     = Kelas::findOrFail($request->id_kelas);
        $namaMapel = $request->nama_mapel;

        $daftarSesiIds = Mengajar::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $request->id_kelas)
            ->where('nama_mapel', $namaMapel)
            ->pluck('id_mengajar');

        $totalPertemuan = $daftarSesiIds->count();

        $rekapSiswa = Siswa::where('id_kelas', $request->id_kelas)
            ->select('id_siswa', 'nis', 'nama_siswa')
            ->selectSub(function ($q) use ($daftarSesiIds) {
                $q->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Hadir');
            }, 'total_hadir')
            ->selectSub(function ($q) use ($daftarSesiIds) {
                $q->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Sakit');
            }, 'total_sakit')
            ->selectSub(function ($q) use ($daftarSesiIds) {
                $q->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Izin');
            }, 'total_izin')
            ->selectSub(function ($q) use ($daftarSesiIds) {
                $q->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Alpa');
            }, 'total_alpa')
            ->orderBy('nama_siswa', 'asc')
            ->get();

        return view('guru.absen-mapel.rekap-tampil', compact(
            'kelas',
            'namaMapel',
            'totalPertemuan',
            'rekapSiswa'
        ));
    }

    // =========================================================================
    // API: Cek pertemuan ke-berapa (AJAX)
    // =========================================================================
    public function cekPertemuanKe(Request $request)
    {
        $jumlahSesi = Mengajar::where('id_guru', auth()->user()->id_guru)
            ->where('id_kelas', $request->id_kelas)
            ->where('nama_mapel', $request->nama_mapel)
            ->count();

        return response()->json(['pertemuan_ke' => $jumlahSesi + 1]);
    }

    // =========================================================================
    // API: Cek apakah sesi hari ini sudah pernah dibuka (AJAX — BARU)
    // =========================================================================
    public function cekDuplikatSesi(Request $request)
    {
        $guru = auth()->user();

        // Ambil nama mapel dari id_mapel yang dikirim
        $mapel = Mapel::find($request->id_mapel);

        if (!$mapel) {
            return response()->json(['duplikat' => false]);
        }

        $duplikat = Mengajar::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $request->id_kelas)
            ->where('nama_mapel', $mapel->nama_mapel)
            ->where('tgl_mengajar', Carbon::today()->toDateString())
            ->exists();

        return response()->json(['duplikat' => $duplikat]);
    }

    // =========================================================================
    // API: Ambil mapel yang di-plot untuk guru berdasarkan kelas (AJAX)
    // =========================================================================
    public function getMapelByKelasGuru(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $id_guru  = auth()->user()->id_guru;

        $kelas = Kelas::find($id_kelas);

        if (!$kelas) {
            return response()->json([]);
        }

        $jurusanKelas = $kelas->jurusan;

        $plotSesi = PlotMengajar::with('mapel')
            ->where('id_guru', $id_guru)
            ->where('id_kelas', $id_kelas)
            ->whereHas('mapel', function ($query) use ($jurusanKelas) {
                $query->where('jurusan', 'LIKE', '%' . $jurusanKelas . '%')
                    ->orWhere('jurusan', 'Umum')
                    ->orWhere(function ($sub) use ($jurusanKelas) {
                        $sub->whereRaw('? LIKE CONCAT("%", jurusan, "%")', [$jurusanKelas]);
                    });
            })
            ->get();

        $mapelTersaring = $plotSesi->pluck('mapel')->unique('id_mapel')->filter()->values();

        return response()->json($mapelTersaring);
    }

    // =========================================================================
    // API: Ambil nama mapel (string) berdasarkan kelas — untuk filter rekap (BARU)
    // =========================================================================
    public function getMapelNamaByKelas(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $id_guru  = auth()->user()->id_guru;

        // Ambil nama mapel unik yang pernah benar-benar diajarkan di kelas ini
        $daftarNama = Mengajar::where('id_guru', $id_guru)
            ->where('id_kelas', $id_kelas)
            ->pluck('nama_mapel')
            ->unique()
            ->values();

        return response()->json($daftarNama);
    }

    // =========================================================================
    // API: Tambah master mapel baru via modal AJAX
    // =========================================================================
    public function tambahMapelAjax(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mapels,nama_mapel',
        ], [
            'nama_mapel.unique' => 'Mata pelajaran ini sudah ada di dalam daftar pilihan.',
        ]);

        $mapelBaru = Mapel::create([
            'nama_mapel' => trim($request->nama_mapel),
            'jurusan'    => 'Umum',
        ]);

        return response()->json([
            'success'   => true,
            'id_mapel'  => $mapelBaru->id_mapel,
            'nama_mapel' => $mapelBaru->nama_mapel,
        ]);
    }
}
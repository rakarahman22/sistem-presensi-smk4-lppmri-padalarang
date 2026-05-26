<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mengajar;
use App\Models\PresensiMapel;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsenMapelController extends Controller
{
    // 1. Tampilkan halaman utama absen (Sudah dikunci berdasarkan Plotting Admin)
    public function index()
    {
        $guru = auth()->user();
        
        // AMBIL DATA KELAS YANG HANYA DIPLOT OLEH ADMIN UNTUK GURU INI
        $plotSesi = \App\Models\PlotMengajar::with(['kelas', 'mapel'])
                        ->where('id_guru', $guru->id_guru)
                        ->get();

        // Ambil data kelas unik saja dari hasil plotting untuk dropdown kelas
        $daftarKelas = $plotSesi->pluck('kelas')->unique('id_kelas')->filter();

        // Catatan: $daftarMasterMapel dikosongkan/tidak diparsing penuh lagi 
        // karena dropdown mapel sekarang di-load dinamis via JavaScript (getMapelByKelasGuru)
        $daftarMasterMapel = collect();
        
        // Riwayat mengajar hari ini tetap tampil seperti biasa
        $riwayatHariIni = Mengajar::with('kelas')
                            ->where('id_guru', $guru->id_guru)
                            ->where('tgl_mengajar', Carbon::today()->toDateString())
                            ->orderBy('jam_mulai', 'desc')
                            ->get();

        foreach ($riwayatHariIni as $riwayat) {
            $hitungPertemuan = Mengajar::where('id_guru', $guru->id_guru)
                                ->where('id_kelas', $riwayat->id_kelas)
                                ->where('nama_mapel', $riwayat->nama_mapel)
                                ->where('id_mengajar', '<=', $riwayat->id_mengajar)
                                ->count();
            
            $riwayat->pertemuan_ke = $hitungPertemuan;
        }

        return view('guru.absen-mapel.index', compact('daftarKelas', 'daftarMasterMapel', 'riwayatHariIni'));
    }

    // 2. Memproses pembuatan sesi mengajar baru berdasarkan pilihan dropdown master mapel
    public function storeSesi(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id_mapel', 
        ]);

        $guru = auth()->user();
        $hariIni = Carbon::today()->toDateString();
        
        // Mencari nama string mata pelajaran asli dari data master
        $mapelMaster = Mapel::findOrFail($request->id_mapel);

        // Membuat induk record mengajar
        $sesiMengajar = Mengajar::create([
            'id_guru'      => $guru->id_guru,
            'id_kelas'     => $request->id_kelas,
            'nama_mapel'   => $mapelMaster->nama_mapel, 
            'tgl_mengajar' => $hariIni,
            'jam_mulai'    => Carbon::now()->toTimeString(),
        ]);

        // Mengambil data seluruh siswa yang terdaftar di kelas terpilih
        $daftarSiswa = Siswa::where('id_kelas', $request->id_kelas)->get();

        if ($daftarSiswa->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal membuka absen! Tidak ada siswa di kelas tersebut.');
        }

        // FIX TYPO: Mengamankan properti objek id_mengajar agar tidak pincang/null
        foreach ($daftarSiswa as $siswa) {
            PresensiMapel::create([
                'id_mengajar' => $sesiMengajar->id_mengajar,
                'id_siswa'    => $siswa->id_siswa,
                'status'      => 'Hadir'
            ]);
        }

        return redirect()->route('guru.absen-mapel.isi', $sesiMengajar->id_mengajar)
                         ->with('success', 'Sesi mapel berhasil dibuka! Silakan sesuaikan kehadiran siswa jika ada yang absen.');
    }

    // 3. Menampilkan lembar checklist absensi siswa (H / S / I / A)
    public function isiAbsen($id_mengajar)
    {
        $sesi = Mengajar::with('kelas')->findOrFail($id_mengajar);
        
        // Keamanan: Validasi agar guru lain tidak bisa mengutak-atik sesi mengajar guru ini
        if ($sesi->id_guru !== auth()->user()->id_guru) {
            abort(403, 'Anda tidak memiliki akses ke sesi mengajar ini.');
        }

        // Menarik daftar siswa beserta status absensinya pada sesi ini
        $presensiSiswa = PresensiMapel::with('siswa')
                            ->where('id_mengajar', $id_mengajar)
                            ->get();

        return view('guru.absen-mapel.isi', compact('sesi', 'presensiSiswa'));
    }

    // 4. Memproses update perubahan status absensi siswa di dalam kelas
    public function updateAbsen(Request $request, $id_mengajar)
    {
        $request->validate([
            'status'   => 'required|array',
            'status.*' => 'required|in:Hadir,Sakit,Izin,Alpa'
        ]);

        // Melakukan update massal data status siswa berdasarkan array input radio button
        foreach ($request->status as $id_presensi_mapel => $statusSiswa) {
            PresensiMapel::where('id_presensi_mapel', $id_presensi_mapel)
                         ->update(['status' => $statusSiswa]);
        }

        return redirect()->route('guru.absen-mapel.index')
                         ->with('success', '✅ Data absensi kelas berhasil disimpan ke sistem.');
    }

    // 5. Tampilkan halaman awal rekap (DIKUNCI BERDASARKAN PLOTTING ADMIN)
    public function rekapIndex()
    {
        $guru = auth()->user();
        
        // AMBIL DATA PLOTTING RESMI DARI ADMIN UNTUK GURU YANG SEDANG LOGIN
        $plotSesi = \App\Models\PlotMengajar::with(['kelas', 'mapel'])
                        ->where('id_guru', $guru->id_guru)
                        ->get();

        // 1. Ambil daftar kelas unik yang diajar oleh guru ini
        $daftarKelas = $plotSesi->pluck('kelas')->unique('id_kelas')->filter();
        
        // 2. Ambil daftar nama mapel unik yang diajar oleh guru ini
        $daftarMapel = $plotSesi->pluck('mapel')->unique('id_mapel')->pluck('nama_mapel')->filter();

        return view('guru.absen-mapel.rekap-index', compact('daftarKelas', 'daftarMapel'));
    }

    // 6. Memproses kalkulasi hitungan persentase dan menampilkan tabel rekapitulasi absen mapel
    public function rekapTampil(Request $request)
    {
        $request->validate([
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'nama_mapel' => 'required|string',
        ]);

        $guru = auth()->user();
        $kelas = Kelas::findOrFail($request->id_kelas);
        $namaMapel = $request->nama_mapel;

        // Mencari semua ID sesi pertemuan mengajar yang cocok dengan kriteria filter
        $daftarSesiIds = Mengajar::where('id_guru', $guru->id_guru)
                                ->where('id_kelas', $request->id_kelas)
                                ->where('nama_mapel', $namaMapel)
                                ->pluck('id_mengajar');

        $totalPertemuan = $daftarSesiIds->count();

        // Menggunakan subquery select manual untuk menghitung status tanpa bentrok dengan tabel 'presensis' harian
        $rekapSiswa = Siswa::where('id_kelas', $request->id_kelas)
            ->select('id_siswa', 'nis', 'nama_siswa')
            ->selectSub(function ($query) use ($daftarSesiIds) {
                $query->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Hadir');
            }, 'total_hadir')
            ->selectSub(function ($query) use ($daftarSesiIds) {
                $query->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Sakit');
            }, 'total_sakit')
            ->selectSub(function ($query) use ($daftarSesiIds) {
                $query->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Izin');
            }, 'total_izin')
            ->selectSub(function ($query) use ($daftarSesiIds) {
                $query->selectRaw('count(*)')->from('presensi_mapels')
                    ->whereColumn('presensi_mapels.id_siswa', 'siswas.id_siswa')
                    ->whereIn('presensi_mapels.id_mengajar', $daftarSesiIds)
                    ->where('presensi_mapels.status', 'Alpa');
            }, 'total_alpa')
            ->orderBy('nama_siswa', 'asc')
            ->get();

        return view('guru.absen-mapel.rekap-tampil', compact('kelas', 'namaMapel', 'totalPertemuan', 'rekapSiswa'));
    }

    // 7. API Realtime untuk menghitung otomatis status pertemuan ke-berapa via AJAX JavaScript
    public function cekPertemuanKe(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $nama_mapel = $request->nama_mapel;
        $id_guru = auth()->user()->id_guru;

        // Menghitung jumlah record pertemuan yang sudah pernah dibuat sebelumnya
        $jumlahSesi = Mengajar::where('id_guru', $id_guru)
                                ->where('id_kelas', $id_kelas)
                                ->where('nama_mapel', $nama_mapel)
                                ->count();

        // Sesi pertemuan berikutnya adalah jumlah total saat ini ditambah satu (N + 1)
        $pertemuanKe = $jumlahSesi + 1;

        return response()->json([
            'pertemuan_ke' => $pertemuanKe
        ]);
    }

    /**
     * =========================================================================
     * FIX: AMBIL MATA PELAJARAN YANG DI-PLOT UNTUK GURU INI BERDASARKAN KELAS
     * =========================================================================
     */
    public function getMapelByKelasGuru(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $id_guru = auth()->user()->id_guru;

        // 1. Ambil data kelas target untuk mendeteksi teks jurusannya
        $kelas = \App\Models\Kelas::find($id_kelas);

        if (!$kelas) {
            return response()->json([]);
        }

        $jurusanKelas = $kelas->jurusan;

        // 2. Query cerdas: Ambil data plotting milik guru ini di kelas ini,
        // yang nama jurusan mapelnya mengandung potongan kata dari jurusan kelas, ATAU mapel Umum
        $plotSesi = \App\Models\PlotMengajar::with('mapel')
                        ->where('id_guru', $id_guru)
                        ->where('id_kelas', $id_kelas)
                        ->whereHas('mapel', function($query) use ($jurusanKelas) {
                            $query->where('jurusan', 'LIKE', '%' . $jurusanKelas . '%')
                                  ->orWhere('jurusan', 'Umum')
                                  ->orWhere(function($sub) use ($jurusanKelas) {
                                      $sub->whereRaw('? LIKE CONCAT("%", jurusan, "%")', [$jurusanKelas]);
                                  });
                        })
                        ->get();

        // Ekstrak data mata pelajaran unik dari hasil penugasan admin
        $mapelTersaring = $plotSesi->pluck('mapel')->unique('id_mapel')->filter()->values();

        return response()->json($mapelTersaring);
    }

    // API untuk menambah master mata pelajaran baru secara instant via AJAX Modal
    public function tambahMapelAjax(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mapels,nama_mapel',
        ], [
            'nama_mapel.unique' => 'Mata pelajaran ini sudah ada di dalam daftar pilihan.'
        ]);

        // FIX: Menambahkan fallback nilai 'Umum' pada kolom jurusan agar terhindar dari SQL Error
        $mapelBaru = \App\Models\Mapel::create([
            'nama_mapel' => trim($request->nama_mapel),
            'jurusan'    => 'Umum' 
        ]);

        return response()->json([
            'success' => true,
            'id_mapel' => $mapelBaru->id_mapel,
            'nama_mapel' => $mapelBaru->nama_mapel,
        ]);
    }
}
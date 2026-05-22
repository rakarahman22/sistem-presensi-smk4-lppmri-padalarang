<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\PengaturanGeofencing;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
    // 1. Tampilkan Halaman Utama Absen di Akun Siswa
    public function index()
    {
        $siswa = auth()->user();
        $hariIni = Carbon::today()->toDateString();

        $presensiHariIni = Presensi::where('id_siswa', $siswa->id_siswa)
            ->where('tgl_presensi', $hariIni)
            ->first();

        $geofence = PengaturanGeofencing::first();

        return view('siswa.absen', compact('presensiHariIni', 'geofence'));
    }

    // 2. Logika Inti Memproses Ketukan Absen Masuk Siswa
    public function store(Request $request)
    {
        $request->validate([
            'lat_siswa'  => 'required|numeric',
            'long_siswa' => 'required|numeric',
        ]);

        $siswa = auth()->user();

        $hariIni = Carbon::today()->toDateString();

        /*
    |--------------------------------------------------------------------------
    | AMBIL PENGATURAN SISTEM
    |--------------------------------------------------------------------------
    */
        $pengaturan = Pengaturan::first();

        if (!$pengaturan) {
            return redirect()->back()->with(
                'error',
                'Pengaturan sistem belum dikonfigurasi admin.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | CEK MAINTENANCE MODE
    |--------------------------------------------------------------------------
    */
        if ($pengaturan->is_maintenance) {
            return redirect()->back()->with(
                'error',
                'Sistem presensi sedang maintenance.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | CEK HARI KERJA
    |--------------------------------------------------------------------------
    */
        $hariSekarang = Carbon::now()->locale('id')->dayName;

        $hariAktif = $pengaturan->hari_kerja ?? [];

        if (!in_array(ucfirst($hariSekarang), $hariAktif)) {
            return redirect()->back()->with(
                'error',
                'Hari ini bukan hari presensi.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | CEK SUDAH ABSEN ATAU BELUM
    |--------------------------------------------------------------------------
    */
        $cekAbsen = Presensi::where('id_siswa', $siswa->id_siswa)
            ->where('tgl_presensi', $hariIni)
            ->exists();

        if ($cekAbsen) {
            return redirect()->back()->with(
                'error',
                'Anda sudah melakukan presensi hari ini!'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA GEOFENCING
    |--------------------------------------------------------------------------
    */
        $sekolah = PengaturanGeofencing::first();

        if (!$sekolah) {
            return redirect()->back()->with(
                'error',
                'Konfigurasi lokasi sekolah belum diatur admin.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | HITUNG JARAK SISWA
    |--------------------------------------------------------------------------
    */
        $jarakSiswa = $this->hitungJarakHaversine(
            $sekolah->latitude_sekolah,
            $sekolah->longitude_sekolah,
            $request->lat_siswa,
            $request->long_siswa
        );

        /*
    |--------------------------------------------------------------------------
    | VALIDASI GEOFENCING
    |--------------------------------------------------------------------------
    */
        if ($jarakSiswa > $sekolah->radius_meter) {

            return redirect()->back()->with(
                'error',
                'Presensi gagal! Anda berada di luar area sekolah. Jarak Anda '
                    . round($jarakSiswa)
                    . ' meter dari sekolah.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | CEK TERLAMBAT
    |--------------------------------------------------------------------------
    */
        $jamSekarang = Carbon::now();

        $batasTerlambat = Carbon::today()->setTimeFromTimeString(
            $pengaturan->batas_terlambat
        );

        $status = $jamSekarang->lessThanOrEqualTo($batasTerlambat)
            ? 'Hadir'
            : 'Terlambat';

        /*
    |--------------------------------------------------------------------------
    | SIMPAN PRESENSI
    |--------------------------------------------------------------------------
    */
        Presensi::create([
            'id_siswa'     => $siswa->id_siswa,
            'id_guru'      => null,
            'tgl_presensi' => $hariIni,
            'jam_masuk'    => $jamSekarang->format('H:i:s'),
            'lat_siswa'    => $request->lat_siswa,
            'long_siswa'   => $request->long_siswa,
            'status'       => $status,
        ]);

        /*
    |--------------------------------------------------------------------------
    | PESAN BERHASIL
    |--------------------------------------------------------------------------
    */
        $pesan = $status === 'Hadir'
            ? '✅ Presensi berhasil direkam. Selamat belajar!'
            : '⚠️ Presensi tercatat, namun Anda terlambat.';

        return redirect()->back()->with(
            'success',
            $pesan
        );
    }
    /**
     * Algoritma Formula Haversine (Sangat Penting untuk Teori Skripsi)
     * Menghitung jarak antara 2 titik koordinat bumi (satuan meter)
     */
    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius rata-rata bumi dalam satuan METER

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

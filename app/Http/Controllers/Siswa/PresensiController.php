<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\PengaturanGeofencing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
    // 1. Tampilkan Halaman Utama Absen di Akun Siswa
    public function index()
    {
        $siswa = auth()->user(); // Mengambil data siswa yang sedang login
        $hariIni = Carbon::today()->toDateString();

        // Cek apakah siswa sudah absen hari ini
        $presensiHariIni = Presensi::where('id_siswa', $siswa->id_siswa)
                                   ->where('tgl_presensi', $hariIni)
                                   ->first();

        // Ambil konfigurasi lokasi sekolah untuk parameter jangkauan di view
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

        // Proteksi ganda: Mencegah siswa melakukan absen masuk dua kali dalam sehari
        $cekAbsen = Presensi::where('id_siswa', $siswa->id_siswa)
                            ->where('tgl_presensi', $hariIni)
                            ->exists();

        if ($cekAbsen) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi hari ini!');
        }

        // Ambil data koordinat jangkar sekolah dari database
        $sekolah = PengaturanGeofencing::first();
        if (!$sekolah) {
            return redirect()->back()->with('error', 'Konfigurasi lokasi sekolah belum diatur oleh Admin.');
        }

        // Hitung jarak riil posisi siswa ke sekolah menggunakan rumus Haversine
        $jarakSiswa = $this->hitungJarakHaversine(
            $sekolah->latitude_sekolah, $sekolah->longitude_sekolah,
            $request->lat_siswa, $request->long_siswa
        );

        // UJI RADIUS GEOFENCING: Jika jarak siswa lebih besar dari radius aman -> TOLAK!
        if ($jarakSiswa > $sekolah->radius_meter) {
            return redirect()->back()->with('error', 'Presensi Gagal! Anda berada di luar area sekolah. Jarak Anda: ' . round($jarakSiswa) . ' meter dari sekolah.');
        }

        // JIKA LOLOS VALIDASI RADIUS: Simpan data kehadiran ke database
        Presensi::create([
            'id_siswa'     => $siswa->id_siswa,
            'id_guru'      => null, // Null karena siswa melakukan absen mandiri
            'tgl_presensi' => $hariIni,
            'jam_masuk'    => Carbon::now()->toTimeString(),
            'lat_siswa'    => $request->lat_siswa,
            'long_siswa'   => $request->long_siswa,
            'status'       => 'Hadir'
        ]);

        return redirect()->back()->with('success', '✅ Presensi berhasil direkam! Selamat belajar.');
    }

    /**
     * Algoritma Formula Haversine (Sangat Penting untuk Teori Skripsi)
     * Menghitung jarak antara 2 titik koordinat bumi (satuan meter)
     */
    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius rata-rata bumi dalam satuan METER

        // Konversi derajat koordinat ke radian
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        // Perhitungan kuadrat setengah tali busur
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c; // Menghasilkan output jarak (double) dalam meter
    }
}
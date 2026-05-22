<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanGeofencing;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    // 1. Tampilkan Halaman Pengaturan Lokasi
    public function index()
    {
        // Ambil data pertama. Jika database masih kosong, berikan nilai default koordinat Padalarang
        $geo = PengaturanGeofencing::first() ?? new PengaturanGeofencing([
            'latitude_sekolah' => -6.8436,
            'longitude_sekolah' => 107.4972,
            'radius_meter' => 100
        ]);

        return view('admin.kelolalokasi.pengaturan-lokasi', compact('geo'));
    }

    // 2. Simpan Perubahan Parameter Geofencing
    public function update(Request $request)
    {
        $request->validate([
            'latitude_sekolah'  => 'required|numeric',
            'longitude_sekolah' => 'required|numeric',
            'radius_meter'      => 'required|integer|min:10',
        ]);

        // Menggunakan updateOrCreate dengan ID 1 karena ini konfigurasi tunggal
        PengaturanGeofencing::updateOrCreate(
            ['id_geofence' => 1],
            [
                'latitude_sekolah'  => $request->latitude_sekolah,
                'longitude_sekolah' => $request->longitude_sekolah,
                'radius_meter'      => $request->radius_meter,
            ]
        );

        return redirect()->route('admin.lokasi')->with('success', 'Konfigurasi Geofencing sekolah berhasil diperbarui!');
    }
}
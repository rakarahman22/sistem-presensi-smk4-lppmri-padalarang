<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index()
    {
        // Ambil pengaturan pertama
        $pengaturan = Pengaturan::first();

        // Jika belum ada data pengaturan
        if (!$pengaturan) {
            $pengaturan = Pengaturan::create([
                'nama_sekolah' => 'SMK 4 LPPM RI Padalarang',
                'npsn' => '2022XXXX',
                'nama_kepsek' => '',
                'tahun_ajaran' => '2025/2026 Ganjil',
                'jam_masuk' => '07:00',
                'batas_terlambat' => '07:15',
                'hari_kerja' => [
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat'
                ],
                'is_maintenance' => false,
                'lock_device' => true,
            ]);
        }

        return view(
            'admin.kelolapengaturan.pengaturan',
            compact('pengaturan')
        );
    }

    /**
     * Update identitas sekolah
     */
    public function updateIdentitas(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:50',
            'nama_kepsek' => 'nullable|string|max:255',
            'tahun_ajaran' => 'required|string|max:100',
            'logo_sekolah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pengaturan = Pengaturan::first();

        $data = [
            'nama_sekolah' => $request->nama_sekolah,
            'npsn' => $request->npsn,
            'nama_kepsek' => $request->nama_kepsek,
            'tahun_ajaran' => $request->tahun_ajaran,
        ];

        // Upload logo
        if ($request->hasFile('logo_sekolah')) {
            $path = $request->file('logo_sekolah')
                ->store('logo-sekolah', 'public');

            $data['logo_sekolah'] = $path;
        }

        $pengaturan->update($data);

        return back()->with(
            'success',
            '✅ Identitas sekolah berhasil diperbarui!'
        );
    }

    /**
     * Update aturan presensi
     */
    public function updateAturan(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required',
            'batas_terlambat' => 'required',
        ]);

        $pengaturan = Pengaturan::first();

        $pengaturan->update([
            'jam_masuk' => $request->jam_masuk,
            'batas_terlambat' => $request->batas_terlambat,
            'hari_kerja' => $request->hari_kerja ?? [],
            'is_maintenance' => $request->has('is_maintenance'),
        ]);

        return back()->with(
            'success',
            '✅ Aturan presensi berhasil diperbarui!'
        );
    }

    /**
     * Update keamanan akun admin
     */
    public function updateKeamanan(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $admin = Auth::guard('admin')->user();

        $admin->username = $request->username;

        // Jika password diisi
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        // Update pengaturan lock device
        $pengaturan = Pengaturan::first();

        $pengaturan->update([
            'lock_device' => $request->has('lock_device'),
        ]);

        return back()->with(
            'success',
            '✅ Pengaturan keamanan berhasil diperbarui!'
        );
    }

    /**
     * Backup database
     */
    public function backup()
    {
        return back()->with(
            'success',
            '✅ Fitur backup database sedang dikembangkan!'
        );
    }};
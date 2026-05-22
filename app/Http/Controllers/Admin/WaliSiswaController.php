<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaliSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaliSiswaController extends Controller
{
    // 1. Menampilkan Tabel Utama Wali Siswa
    public function index()
    {
        $walis = WaliSiswa::orderBy('nama_wali')->get();
        
        // FIX: Mengarah ke resources/views/admin/kelolawalisiswa/data-walisiswa.blade.php
        return view('admin.kelolawalisiswa.data-walisiswa', compact('walis'));
    }

    // 2. Menampilkan Form Tambah Wali Siswa
    public function create()
    {
        // FIX: Mengarah ke resources/views/admin/kelolawalisiswa/create-walisiswa.blade.php
        return view('admin.kelolawalisiswa.create-walisiswa');
    }

    // 3. Memproses Data Wali Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'nama_wali' => 'required|string|max:100',
            'username'  => 'required|unique:wali_siswas,username',
            'password'  => 'required|min:6',
            'no_telp'   => 'required|string|max:15',
        ]);

        WaliSiswa::create([
            'nama_wali' => $request->nama_wali,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'no_telp'   => $request->no_telp,
        ]);

        return redirect()->route('admin.wali')->with('success', 'Wali Siswa baru berhasil didaftarkan!');
    }

    // 4. Menampilkan Form Edit Wali Siswa
    public function edit($id_wali)
    {
        $wali = WaliSiswa::findOrFail($id_wali);
        
        // FIX: Mengarah ke resources/views/admin/kelolawalisiswa/edit-walisiswa.blade.php
        return view('admin.kelolawalisiswa.edit-walisiswa', compact('wali'));
    }

    // 5. Memproses Perubahan Data Wali Siswa
    public function update(Request $request, $id_wali)
    {
        $wali = WaliSiswa::findOrFail($id_wali);

        $request->validate([
            'nama_wali' => 'required|string|max:100',
            'username'  => 'required|unique:wali_siswas,username,' . $id_wali . ',id_wali',
            'password'  => 'nullable|min:6',
            'no_telp'   => 'required|string|max:15',
        ]);

        $data = [
            'nama_wali' => $request->nama_wali,
            'username'  => $request->username,
            'no_telp'   => $request->no_telp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $wali->update($data);

        return redirect()->route('admin.wali')->with('success', 'Data Wali Siswa berhasil diperbarui!');
    }

    // 6. Memproses Hapus Data Wali Siswa
    public function destroy($id_wali)
    {
        $wali = WaliSiswa::findOrFail($id_wali);
        $wali->delete();

        return redirect()->route('admin.wali')->with('success', 'Data Wali Siswa berhasil dihapus!');
    }
}
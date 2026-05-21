<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru; // Kita hanya butuh model Guru di sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    // 1. Menampilkan Tabel Utama Data Guru
    public function index()
    {
        // Memuat relasi kelasDiampu agar badge status kelas di file Blade langsung muncul dinamis
        $gurus = Guru::with('kelasDiampu')->orderBy('nama_guru')->get();
        return view('admin.kelolaguru.data-guru', compact('gurus'));
    }

    // 2. Menampilkan Form Tambah Guru
    public function create()
    {
        return view('admin.kelolaguru.create-guru');
    }

    // 3. FIX: Memproses Penyimpanan Data Guru Baru (Bukan data kelas lagi)
    public function store(Request $request)
    {
        $request->validate([
            'nip'       => 'required|unique:gurus,nip',
            'nama_guru' => 'required|string|max:100',
            'jabatan' =>'required|string|max:100',
            'username'  => 'required|unique:gurus,username',
            'password'  => 'required|min:6',
        ]);

        Guru::create([
            'nip'       => $request->nip,
            'nama_guru' => $request->nama_guru,
            'jabatan' => $request->jabatan,
            'username'  => $request->username,
            'password'  => Hash::make($request->password), // Enkripsi password otomatis
        ]);

        return redirect()->route('admin.guru')->with('success', 'Data Guru baru berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit Guru
    public function edit($id_guru)
    {
        $guru = Guru::findOrFail($id_guru);
        return view('admin.kelolaguru.edit-guru', compact('guru'));
    }

    // 5. FIX: Memproses Perubahan Data Guru (Bukan data kelas lagi)
    public function update(Request $request, $id_guru)
    {
        $guru = Guru::findOrFail($id_guru);

        $request->validate([
            'nip'       => 'required|unique:gurus,nip,' . $id_guru . ',id_guru',
            'nama_guru' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'username'  => 'required|unique:gurus,username,' . $id_guru . ',id_guru',
            'password'  => 'nullable|min:6', // Boleh kosong jika tidak ingin ganti sandi
        ]);

        $data = [
            'nip'       => $request->nip,
            'nama_guru' => $request->nama_guru,
            'jabatan' => $request->jabatan,
            'username'  => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect()->route('admin.guru')->with('success', 'Data Guru berhasil diperbarui!');
    }

    // 6. Memproses Penghapusan Data Guru
    public function destroy($id_guru)
    {
        $guru = Guru::findOrFail($id_guru);
        $guru->delete();

        return redirect()->route('admin.guru')->with('success', 'Data Guru berhasil dihapus dari sistem!');
    }
}
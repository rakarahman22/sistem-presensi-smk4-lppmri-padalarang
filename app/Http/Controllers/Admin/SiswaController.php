<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliSiswa; // FIX: Wajib import model WaliSiswa di sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    // 1. Menampilkan Halaman Utama Tabel Siswa beserta Dropdown Filter Kelas
    public function index()
    {
        // Mengambil seluruh data siswa beserta relasi kelas dan walinya, diurutkan berdasarkan nama
        // FIX: Menyertakan relasi 'wali' agar bisa dipanggil namanya di tabel utama
        $siswas = Siswa::with(['kelas', 'wali'])->orderBy('nama_siswa')->get();
        
        // Mengambil data kelas agar dropdown filter di data-siswa.blade.php tidak error
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        // Mengarah ke resources/views/admin/kelolasiswa/data-siswa.blade.php
        return view('admin.kelolasiswa.data-siswa', compact('siswas', 'kelas_list'));
    }

    // 2. Menampilkan Form Tambah Siswa
    public function create()
    {
        // Mengambil data kelas untuk pilihan select form tambah siswa
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        // FIX: Mengambil data wali siswa dari database untuk dropdown pencarian di form
        $wali_list = WaliSiswa::orderBy('nama_wali')->get();
        
        // Mengarah ke resources/views/admin/kelolasiswa/create-siswa.blade.php
        // FIX: Menyertakan 'wali_list' ke dalam compact() agar tidak undefined
        return view('admin.kelolasiswa.create-siswa', compact('kelas_list', 'wali_list'));
    }

    // 3. Memproses Data Siswa Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'nis'        => 'required|unique:siswas,nis',
            'nama_siswa' => 'required|string|max:100',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'id_wali'    => 'required|exists:wali_siswas,id_wali', // FIX: Validasi id_wali wajib ada dan sah
            'username'   => 'required|unique:siswas,username',
            'password'   => 'required|min:6',
        ]);

        Siswa::create([
            'nis'        => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'id_kelas'   => $request->id_kelas,
            'id_wali'    => $request->id_wali, // FIX: Mengambil data dari dropdown input form (bukan null lagi)
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('admin.siswa')->with('success', 'Siswa baru berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit Siswa berdasarkan ID
    public function edit($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        // FIX: Mengambil data wali untuk dropdown pencarian di form edit siswa
        $wali_list = WaliSiswa::orderBy('nama_wali')->get();
        
        // Mengarah ke resources/views/admin/kelolasiswa/edit-siswa.blade.php
        // FIX: Menyertakan 'wali_list' ke dalam compact()
        return view('admin.kelolasiswa.edit-siswa', compact('siswa', 'kelas_list', 'wali_list'));
    }

    // 5. Memproses Perubahan Data Siswa ke Database
    public function update(Request $request, $id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);

        $request->validate([
            'nis'        => 'required|unique:siswas,nis,' . $id_siswa . ',id_siswa',
            'nama_siswa' => 'required|string|max:100',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'id_wali'    => 'required|exists:wali_siswas,id_wali', // FIX: Validasi id_wali wajib ada saat edit
            'username'   => 'required|unique:siswas,username,' . $id_siswa . ',id_siswa',
            'password'   => 'nullable|min:6',
        ]);

        $data = [
            'nis'        => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'id_kelas'   => $request->id_kelas,
            'id_wali'    => $request->id_wali, // FIX: Mengupdate data id_wali terpilih
            'username'   => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // 6. Memproses Hapus Data Siswa dari Database
    public function destroy($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $siswa->delete();

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil dihapus!');
    }
}
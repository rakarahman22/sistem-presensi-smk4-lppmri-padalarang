<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru; // Tetap import model Guru
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // 1. Menampilkan Semua Data Kelas beserta Wali Kelasnya
    public function index()
    {
        $kelases = Kelas::with('waliKelas')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.kelolakelas.data-kelas', compact('kelases'));
    }

    // 2. Menampilkan Form Tambah Kelas
    public function create()
    {
        // FIX: Hanya mengambil guru yang BELUM MENJADI wali kelas di kelas mana pun
        $guru_list = Guru::whereDoesntHave('kelasDiampu')->orderBy('nama_guru')->get();
        
        return view('admin.kelolakelas.create-kelas', compact('guru_list'));
    }

    // 3. Memproses Penyimpanan Kelas Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat'    => 'required|string|max:10',
            'jurusan'    => 'required|string|max:100',
            // FIX: Tambah validasi 'unique' agar jika ada bypass form, database tetap menolak guru ganda
            'id_guru'    => 'nullable|exists:gurus,id_guru|unique:kelas,id_guru', 
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat'    => $request->tingkat,
            'jurusan'    => $request->jurusan,
            'id_guru'    => $request->id_guru,
        ]);

        return redirect()->route('admin.kelas')->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit Kelas
    public function edit($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        
        // FIX LOGIK UNTUK FORM EDIT:
        // Ambil guru yang belum mengampu kelas MANAPUN, KECUALI guru yang saat ini sedang mengampu kelas ini.
        // Jika tidak dikecualikan, nama wali kelas yang sekarang menjabat justru akan hilang dari pilihan form edit!
        $guru_list = Guru::whereDoesntHave('kelasDiampu', function($query) use ($id_kelas) {
            $query->where('id_kelas', '!=', $id_kelas);
        })->orderBy('nama_guru')->get();

        return view('admin.kelolakelas.edit-kelas', compact('kelas', 'guru_list'));
    }

    // 5. Memproses Perubahan Data Kelas
    public function update(Request $request, $id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);

        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat'    => 'required|string|max:10',
            'jurusan'    => 'required|string|max:100',
            // FIX: Validasi unique dikecualikan untuk ID Kelas yang sedang kita edit saat ini
            'id_guru'    => 'nullable|exists:gurus,id_guru|unique:kelas,id_guru,' . $id_kelas . ',id_kelas',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat'    => $request->tingkat,
            'jurusan'    => $request->jurusan,
            'id_guru'    => $request->id_guru,
        ]);

        return redirect()->route('admin.kelas')->with('success', 'Data kelas berhasil diperbarui!');
    }

    // 6. Memproses Penghapusan Kelas
    public function destroy($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $kelas->delete();

        return redirect()->route('admin.kelas')->with('success', 'Kelas berhasil dihapus!');
    }
}
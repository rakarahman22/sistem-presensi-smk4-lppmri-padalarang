<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    // Tampilkan List Data Mapel dengan Filter Jurusan
    public function index(Request $request)
    {
        $filterJurusan = $request->get('filter_jurusan');

        // AMBIL DATA JURUSAN UNIK DARI TABEL KELAS
        // pluck('jurusan') akan mengambil semua isi kolom jurusan, distinct() menjamin tidak ada yang kembar
        $daftarJurusanDatabase = \App\Models\Kelas::distinct()->pluck('jurusan')->filter()->toArray();

        $query = Mapel::query();

        if ($filterJurusan) {
            $query->where('jurusan', $filterJurusan);
        }

        $daftarMapel = $query->orderBy('jurusan', 'asc')->orderBy('nama_mapel', 'asc')->get();

        // Kirim $daftarJurusanDatabase ke compact agar bisa dibaca di Blade
        return view('admin.kelolamapel.index', compact('daftarMapel', 'filterJurusan', 'daftarJurusanDatabase'));
    }

    // Proses Simpan Mapel Baru beserta Jurusannya
    public function store(Request $request)
    {
        $request->validate([
            'jurusan'    => 'required|string|max:50',
            'nama_mapel' => 'required|string|max:100|unique:mapels,nama_mapel,NULL,id_mapel,jurusan,' . $request->jurusan,
        ], [
            'nama_mapel.unique' => '❌ Mata pelajaran ini sudah terdaftar pada jurusan yang sama.'
        ]);

        Mapel::create([
            'jurusan'    => $request->jurusan,
            'nama_mapel' => trim($request->nama_mapel)
        ]);

        return redirect()->route('admin.mapel', ['filter_jurusan' => $request->jurusan])
                         ->with('success', '✅ Mata pelajaran baru berhasil ditambahkan!');
    }

    // Proses Update Nama Mapel dan Jurusan
    public function update(Request $request, $id_mapel)
    {
        $mapel = Mapel::findOrFail($id_mapel);

        $request->validate([
            'jurusan'    => 'required|string|max:50',
            'nama_mapel' => 'required|string|max:100|unique:mapels,nama_mapel,' . $id_mapel . ',id_mapel,jurusan,' . $request->jurusan,
        ], [
            'nama_mapel.unique' => '❌ Nama mata pelajaran ini sudah digunakan di jurusan tersebut.'
        ]);

        $mapel->update([
            'jurusan'    => $request->jurusan,
            'nama_mapel' => trim($request->nama_mapel)
        ]);

        return redirect()->back()->with('success', '✅ Mata pelajaran berhasil diperbarui!');
    }

    // Proses Hapus Mapel
    public function destroy($id_mapel)
    {
        $mapel = Mapel::findOrFail($id_mapel);
        $mapel->delete();

        return redirect()->back()->with('success', '🗑️ Mata pelajaran berhasil dihapus dari sistem.');
    }
}
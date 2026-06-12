<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 25, 50, 100, 500]) ? $request->per_page : 10;

        $query = Kelas::with(['waliKelas', 'siswa'])->orderBy('tingkat')->orderBy('nama_kelas');

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        if ($request->filled('status_wali')) {
            if ($request->status_wali === 'ada') {
                $query->whereNotNull('id_guru');
            } else {
                $query->whereNull('id_guru');
            }
        }

        $kelases      = $query->paginate($perPage)->withQueryString();
        $tingkat_list = Kelas::distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusan_list = Kelas::distinct()->orderBy('jurusan')->pluck('jurusan');

        return view('admin.kelolakelas.data-kelas', compact(
            'kelases', 'tingkat_list', 'jurusan_list', 'perPage'
        ));
    }

    public function create()
    {
        $guru_list = Guru::whereDoesntHave('kelasDiampu')->orderBy('nama_guru')->get();
        return view('admin.kelolakelas.create-kelas', compact('guru_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat'    => 'required|string|max:10',
            'jurusan'    => 'required|string|max:100',
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

    public function edit($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $guru_list = Guru::whereDoesntHave('kelasDiampu', function ($q) use ($id_kelas) {
            $q->where('id_kelas', '!=', $id_kelas);
        })->orderBy('nama_guru')->get();

        return view('admin.kelolakelas.edit-kelas', compact('kelas', 'guru_list'));
    }

    public function update(Request $request, $id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);

        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat'    => 'required|string|max:10',
            'jurusan'    => 'required|string|max:100',
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

    public function destroy($id_kelas)
    {
        Kelas::findOrFail($id_kelas)->delete();
        return redirect()->route('admin.kelas')->with('success', 'Kelas berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = in_array($request->per_page, [10, 25, 50, 100, 500]) ? $request->per_page : 10;
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $query = Siswa::with(['kelas', 'wali'])->orderBy('nama_siswa');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_siswa', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('nis', 'LIKE', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('kelas', fn($q) => $q->where('tingkat', $request->tingkat));
        }

        if ($request->filled('jurusan')) {
            $query->whereHas('kelas', fn($q) => $q->where('jurusan', $request->jurusan));
        }

        $siswas         = $query->paginate($perPage)->withQueryString();
        $tingkat_list   = Kelas::distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusan_list   = Kelas::distinct()->orderBy('jurusan')->pluck('jurusan');

        return view('admin.kelolasiswa.data-siswa', compact(
            'siswas', 'kelas_list', 'tingkat_list', 'jurusan_list', 'perPage'
        ));
    }

    public function create()
    {
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $wali_list  = WaliSiswa::orderBy('nama_wali')->get();
        return view('admin.kelolasiswa.create-siswa', compact('kelas_list', 'wali_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'        => 'required|unique:siswas,nis',
            'nama_siswa' => 'required|string|max:100',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'id_wali'    => 'required|exists:wali_siswas,id_wali',
            'username'   => 'required|unique:siswas,username',
            'password'   => 'required|min:6',
        ]);

        Siswa::create([
            'nis'        => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'id_kelas'   => $request->id_kelas,
            'id_wali'    => $request->id_wali,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('admin.siswa')->with('success', 'Siswa baru berhasil ditambahkan!');
    }

    public function edit($id_siswa)
    {
        $siswa      = Siswa::findOrFail($id_siswa);
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $wali_list  = WaliSiswa::orderBy('nama_wali')->get();
        return view('admin.kelolasiswa.edit-siswa', compact('siswa', 'kelas_list', 'wali_list'));
    }

    public function update(Request $request, $id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);

        $request->validate([
            'nis'        => 'required|unique:siswas,nis,' . $id_siswa . ',id_siswa',
            'nama_siswa' => 'required|string|max:100',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'id_wali'    => 'required|exists:wali_siswas,id_wali',
            'username'   => 'required|unique:siswas,username,' . $id_siswa . ',id_siswa',
            'password'   => 'nullable|min:6',
        ]);

        $data = [
            'nis'        => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'id_kelas'   => $request->id_kelas,
            'id_wali'    => $request->id_wali,
            'username'   => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);
        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id_siswa)
    {
        Siswa::findOrFail($id_siswa)->delete();
        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil dihapus!');
    }
}
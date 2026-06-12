<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaliSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaliSiswaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 25, 50, 100, 500]) ? $request->per_page : 10;

        $query = WaliSiswa::with('siswa')->orderBy('nama_wali');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_wali', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('username', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('no_telp', 'LIKE', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('status_siswa')) {
            if ($request->status_siswa === 'punya') {
                $query->whereHas('siswa');
            } else {
                $query->whereDoesntHave('siswa');
            }
        }

        $walis = $query->paginate($perPage)->withQueryString();

        return view('admin.kelolawalisiswa.data-walisiswa', compact('walis', 'perPage'));
    }

    public function create()
    {
        return view('admin.kelolawalisiswa.create-walisiswa');
    }

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

    public function edit($id_wali)
    {
        $wali = WaliSiswa::findOrFail($id_wali);
        return view('admin.kelolawalisiswa.edit-walisiswa', compact('wali'));
    }

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

    public function destroy($id_wali)
    {
        WaliSiswa::findOrFail($id_wali)->delete();
        return redirect()->route('admin.wali')->with('success', 'Data Wali Siswa berhasil dihapus!');
    }
}
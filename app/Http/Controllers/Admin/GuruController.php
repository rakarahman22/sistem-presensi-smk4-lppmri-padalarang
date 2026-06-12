<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 25, 50, 100, 500]) ? $request->per_page : 10;

        $query = Guru::with('kelasDiampu')->orderBy('nama_guru');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_guru', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('nip', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('username', 'LIKE', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('status_wali')) {
            if ($request->status_wali === 'mengampu') {
                $query->whereHas('kelasDiampu');
            } else {
                $query->whereDoesntHave('kelasDiampu');
            }
        }

        $gurus = $query->paginate($perPage)->withQueryString();

        return view('admin.kelolaguru.data-guru', compact('gurus', 'perPage'));
    }

    public function create()
    {
        return view('admin.kelolaguru.create-guru');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'       => 'required|unique:gurus,nip',
            'nama_guru' => 'required|string|max:100',
            'jabatan'   => 'required|string|max:100',
            'username'  => 'required|unique:gurus,username',
            'password'  => 'required|min:6',
        ]);

        Guru::create([
            'nip'       => $request->nip,
            'nama_guru' => $request->nama_guru,
            'jabatan'   => $request->jabatan,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('admin.guru')->with('success', 'Data Guru baru berhasil ditambahkan!');
    }

    public function edit($id_guru)
    {
        $guru = Guru::findOrFail($id_guru);
        return view('admin.kelolaguru.edit-guru', compact('guru'));
    }

    public function update(Request $request, $id_guru)
    {
        $guru = Guru::findOrFail($id_guru);

        $request->validate([
            'nip'       => 'required|unique:gurus,nip,' . $id_guru . ',id_guru',
            'nama_guru' => 'required|string|max:100',
            'jabatan'   => 'required|string|max:100',
            'username'  => 'required|unique:gurus,username,' . $id_guru . ',id_guru',
            'password'  => 'nullable|min:6',
        ]);

        $data = [
            'nip'       => $request->nip,
            'nama_guru' => $request->nama_guru,
            'jabatan'   => $request->jabatan,
            'username'  => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);
        return redirect()->route('admin.guru')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy($id_guru)
    {
        Guru::findOrFail($id_guru)->delete();
        return redirect()->route('admin.guru')->with('success', 'Data Guru berhasil dihapus!');
    }
}
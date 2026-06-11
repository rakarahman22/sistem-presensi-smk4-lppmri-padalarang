<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        return view('siswa.profil', compact('siswa'));
    }

    public function update(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();

        $request->validate([
            'nama_siswa'    => ['required', 'string', 'max:100'],
            'username'      => [
                'required', 'string', 'max:50',
                Rule::unique('siswas', 'username')->ignore($siswa->id_siswa, 'id_siswa'),
            ],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_lama' => ['nullable', 'string'],
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $siswa->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                    ->withInput();
            }
        }

        $data = [
            'nama_siswa' => $request->nama_siswa,
            'username'   => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);

        return redirect()->route('siswa.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
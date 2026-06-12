<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruProfilController extends Controller
{
    /**
     * Tampilkan halaman profil guru
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();

        return view('guru.profil', compact('guru'));
    }

    /**
     * Update profil guru (data diri + opsional ganti password)
     */
    public function update(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $request->validate([
            'nama_guru' => 'required|string|max:100',
            'jabatan'   => 'nullable|string|max:50',
            'username'  => 'required|string|max:50|unique:gurus,username,' . $guru->id_guru . ',id_guru',

            'password_lama'              => 'nullable|required_with:password|string',
            'password'                   => 'nullable|string|min:6|confirmed',
            'password_confirmation'      => 'nullable|string',
        ], [
            'nama_guru.required' => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',

            'password_lama.required_with' => 'Password lama wajib diisi jika ingin mengganti password.',
            'password.min'                => 'Password baru minimal 6 karakter.',
            'password.confirmed'          => 'Konfirmasi password tidak cocok.',
        ]);

        // ── Update data diri ──────────────────────────────────
        $guru->nama_guru = $request->nama_guru;
        $guru->jabatan   = $request->jabatan;
        $guru->username  = $request->username;

        // ── Ganti password jika diisi ─────────────────────────
        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $guru->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                    ->withInput();
            }

            $guru->password = Hash::make($request->password);
        }

        $guru->save();

        return redirect()->route('guru.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
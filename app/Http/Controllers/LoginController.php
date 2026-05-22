<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function prosesLogin(Request $request, $type)
    {
        // 1. Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        // 2. FIX: Gunakan Guard secara dinamis berdasarkan parameter $type di URL
        // Nilai $type harus cocok dengan nama guard di config/auth.php (admin, guru, siswa, wali)
        if (Auth::guard($type)->attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Alihkan ke dashboard masing-masing setelah sukses menembus guard
            return match($type) {
                'admin' => redirect()->route('admin.dashboard'),
                'guru'  => redirect()->route('guru.dashboard'),
                'siswa' => redirect()->route('siswa.presensi'), // Mengarah ke halaman absen Geofencing kemarin
                'wali'  => redirect()->route('wali.dashboard'),
                default => redirect('/login'),
            };
        }

        // Jika gagal, kembalikan dengan pesan error
        return back()->withErrors([
            'username' => 'Kredensial login yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }
}
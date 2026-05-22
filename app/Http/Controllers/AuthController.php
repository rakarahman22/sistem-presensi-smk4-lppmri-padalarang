<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman 4 pilihan kartu login
    public function pilihLogin()
    {
        return view('login.pilih-login');
    }

    // Menampilkan form input username & password sesuai tipe yang diklik
    public function showLoginForm($type)
    {
        // Validasi tipe login agar tidak sembarang URL bisa diakses
        if (!in_array($type, ['admin', 'guru', 'wali', 'siswa'])) {
            abort(404);
        }

        return view('login.form-login', compact('type'));
    }

    // Proses Autentikasi
    public function login(Request $request, $type)
    {
        // 1. Validasi input form
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        // 2. Tentukan guard dan arah halaman sukses (redirect) secara pasti
        $guard = $type; 
        $redirectPath = '/' . $type . '/dashboard';

        // 3. Eksekusi Login menggunakan guard terpilih
        if (Auth::guard($guard)->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // FIX: Gunakan redirect langsung, JANGAN pakai intended() agar tidak kembali ke halaman /login
            return redirect($redirectPath);
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    // Proses Logout untuk semua tipe user
    public function logout(Request $request)
    {
        // Cari guard mana yang saat ini sedang aktif login
        foreach (['admin', 'guru', 'wali', 'siswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                break;
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
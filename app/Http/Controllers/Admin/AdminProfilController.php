<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminProfilController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profil', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'nama_admin' => ['required', 'string', 'max:100'],
            'username'   => [
                'required', 'string', 'max:50',
                Rule::unique('admins', 'username')->ignore($admin->id_admin, 'id_admin'),
            ],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_lama' => ['nullable', 'string'],
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $admin->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                    ->withInput();
            }
        }

        $data = [
            'nama_admin' => $request->nama_admin,
            'username'   => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
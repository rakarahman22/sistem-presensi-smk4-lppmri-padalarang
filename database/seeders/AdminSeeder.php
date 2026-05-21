<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Admin::create([
            'username' => 'admin',
            'nama_admin' => 'Admin Utama',
            'password' => Hash::make('password123'), // ← WAJIB menggunakan Hash::make
        ]);
    }
}

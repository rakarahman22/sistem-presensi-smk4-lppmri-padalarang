<?php

namespace Database\Seeders;

use App\Models\WaliSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WaliSiswaSeeder extends Seeder
{
    public function run(): void
    {
        WaliSiswa::create([
            'id_wali' => 1,
            'nama_wali' => 'Hendi Suhendar',
            'username' => 'wali',
            'password' => Hash::make('wali123'),
            'no_telp' => '081234567890',
        ]);
    }
}
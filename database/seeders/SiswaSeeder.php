<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::create([
            'id_kelas' => 1, // Berelasi dengan Kelas XII PPLG 1
            'id_wali' => 1,  // Berelasi dengan Wali Murid Pak Hendi
            'nis' => '222310155',
            'nama_siswa' => 'Rian Ardiansyah',
            'username' => 'siswa',
            'password' => Hash::make('siswa123'),
        ]);
    }
}
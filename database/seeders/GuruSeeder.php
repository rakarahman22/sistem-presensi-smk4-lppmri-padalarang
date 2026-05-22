<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat data guru dummy yang akan dijadikan Wali Kelas
        Guru::create([
            'id_guru'   => 1,
            'nip'       => '198503122010011002',
            'nama_guru' => 'Eko Prasetyo, S.Kom.',
            'jabatan' => 'Kepala Sekolah', 
            'username'  => 'guru',
            'password'  => Hash::make('password123'),
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GURU 1 - WALI KELAS PPLG
        Guru::create([
            'id_guru'   => 1,
            'nip'       => '198503122010011002',
            'nama_guru' => 'Eko Prasetyo, S.Kom.',
            'jabatan'   => 'Kepala Sekolah', 
            'username'  => 'guru', // Akun login utama Guru
            'password'  => Hash::make('password123'),
        ]);

        // 2. GURU 2 - WALI KELAS TJKT
        Guru::create([
            'id_guru'   => 2,
            'nip'       => '198907242015021005',
            'nama_guru' => 'Siti Aminah, S.T.',
            'jabatan'   => 'Guru Produktif TJKT', 
            'username'  => 'guru2',
            'password'  => Hash::make('password123'),
        ]);

        // 3. GURU 3 - WALI KELAS TBSM
        Guru::create([
            'id_guru'   => 3,
            'nip'       => '199111052018011003',
            'nama_guru' => 'Budi Santoso, S.Pd.',
            'jabatan'   => 'Guru Produktif TBSM', 
            'username'  => 'guru3',
            'password'  => Hash::make('password123'),
        ]);

        // 4. GURU 4 - WALI KELAS PERHOTELAN
        Guru::create([
            'id_guru'   => 4,
            'nip'       => '199304182020032001',
            'nama_guru' => 'Dewi Lestari, M.Par.',
            'jabatan'   => 'Guru Produktif Perhotelan', 
            'username'  => 'guru4',
            'password'  => Hash::make('password123'),
        ]);
    }
}
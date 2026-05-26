<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. JURUSAN PPLG (REKAYASA PERANGKAT LUNAK)
        Kelas::create([
            'id_kelas'   => 1,
            'id_guru'    => 1, // Hubungkan ke id_guru nomor 1 dari GuruSeeder
            'nama_kelas' => 'RPL 1',
            'tingkat'    => 'XII',
            'jurusan'    => 'Rekayasa Perangkat Lunak',
        ]);

        // 2. JURUSAN TJKT (TEKNIK JARINGAN KOMPUTER & TELEKOMUNIKASI / TKJ)
        Kelas::create([
            'id_kelas'   => 2,
            'id_guru'    => 2, // Sesuaikan dengan id_guru yang tersedia di GuruSeeder kamu
            'nama_kelas' => 'TKJ 1',
            'tingkat'    => 'XII',
            'jurusan'    => 'Teknik Komputer Jaringan',
        ]);

        // 3. JURUSAN TBSM (TEKNIK SEPEDA MOTOR)
        Kelas::create([
            'id_kelas'   => 3,
            'id_guru'    => 3,
            'nama_kelas' => 'TBSM 1',
            'tingkat'    => 'XII',
            'jurusan'    => 'Teknik Bisnis Sepeda Motor',
        ]);

        // 4. JURUSAN PERHOTELANuc
        Kelas::create([
            'id_kelas'   => 4,
            'id_guru'    => 4,
            'nama_kelas' => 'PH 1',
            'tingkat'    => 'XII',
            'jurusan'    => 'Perhotelan',
        ]);
    }
}
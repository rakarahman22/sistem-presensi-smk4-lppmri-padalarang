<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::create([
            'id_kelas'   => 1,
            'id_guru'    => 1, // ← FIX: Hubungkan ke id_guru nomor 1 dari GuruSeeder
            'nama_kelas' => 'PPLG 1',
            'tingkat'    => 'XII',
            'jurusan'    => 'Pengembangan Perangkat Lunak dan Gim (PPLG)',
        ]);
    }
}
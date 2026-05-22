<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Perhatikan urutan pemanggilan (Kelas & Wali wajib di atas Siswa)
        $this->call([
            AdminSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            WaliSiswaSeeder::class,
            SiswaSeeder::class,
        ]);
    }
}
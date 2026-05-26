<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Array nama sampel untuk dikombinasikan agar nama siswa bervariasi
        $namaDepan = ['Rian', 'Andi', 'Budi', 'Siti', 'Dewi', 'Ahmad', 'Rizky', 'Putri', 'Dika', 'Agus'];
        $namaBelakang = ['Ardiansyah', 'Saputra', 'Lestari', 'Wulandari', 'Pratama', 'Hidayat', 'Kurniawan', 'Permata', 'Santoso', 'Utami'];

        // Looping untuk membuat 30 siswa secara otomatis
        for ($i = 1; $i <= 30; $i++) {
            
            // Logika Distribusi Kelas Dinamis (Membagi rata ke id_kelas 1, 2, 3, dan 4)
            // Hasilnya: Siswa 1-8 akan menyebar rata di kelas 1 sampai 4, dan seterusnya.
            $idKelasDinamis = ($i % 4) + 1; 

            // Logika Distribusi Wali Murid Dinamis (Membagi rata ke id_wali 1, 2, 3, dan 4)
            $idWaliDinamis = ($i % 4) + 1;

            // Generate NIS unik berurutan (Contoh: 222310155, 222310156, dst)
            $nisDinamis = 222310154 + $i;

            // Kombinasi acak nama depan dan belakang agar tidak kembar
            $namaLengkap = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)] . ' ' . $i;

            Siswa::create([
                'id_kelas'   => $idKelasDinamis,
                'id_wali'    => $idWaliDinamis,
                'nis'        => (string)$nisDinamis,
                'nama_siswa' => $namaLengkap,
                'username'   => 'siswa' . $i, // Username unik untuk login: siswa1, siswa2, dst.
                'password'   => Hash::make('siswa123'), // Semua password disamakan untuk kemudahan testing
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\WaliSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WaliSiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Array nama sampel bapak/ibu untuk dikombinasikan agar nama wali bervariasi
        $namaWaliSampel = [
            'Hendi Suhendar', 'Asep Sunandar', 'Dadang Hermawan', 'Maman Suparman', 
            'Cecep Solihin', 'Sutisna', 'Ujang Setiawan', 'Iwan Ridwan', 
            'Dewi Sartika', 'Siti Fatimah', 'Sri Wahyuni', 'Neng Hasanah'
        ];

        // Looping untuk membuat 30 data Wali Siswa secara otomatis
        for ($i = 1; $i <= 30; $i++) {
            
            // Mengambil nama acak dari array sampel dan menambahkan nomor urut agar unik
            $namaWaliDinamis = $namaWaliSampel[array_rand($namaWaliSampel)] . ' (' . $i . ')';
            
            // Membuat nomor telepon dummy yang bervariasi
            $noTelpDinamis = '081234567' . str_pad($i, 3, '0', STR_PAD_LEFT);

            WaliSiswa::create([
                'id_wali'   => $i, // id_wali berjalan dari 1, 2, 3, ... sampai 30
                'nama_wali' => $namaWaliDinamis,
                'username'  => 'wali' . $i, // Akun login: wali1, wali2, dst.
                'password'  => Hash::make('wali123'), // Password seragam untuk mempermudah login testing
                'no_telp'   => $noTelpDinamis,
            ]);
        }
    }
}
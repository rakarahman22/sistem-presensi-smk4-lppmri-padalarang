<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. DATA KELAS 10 (Total 7 Kelas)
        // =========================================================================
        
        // 2 kelas Teknik Komputer dan Jaringan (TKJ)
        Kelas::create(['id_kelas' => 1, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'TKJ 1', 'jurusan' => 'TJKT']);
        Kelas::create(['id_kelas' => 2, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'TKJ 2', 'jurusan' => 'TJKT']);
        
        // 2 kelas Rekayasa Perangkat Lunak (RPL)
        Kelas::create(['id_kelas' => 3, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'RPL 1', 'jurusan' => 'PPLG']);
        Kelas::create(['id_kelas' => 4, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'RPL 2', 'jurusan' => 'PPLG']);
        
        // 2 Kelas Teknik dan Bisnis Sepeda Motor (TBSM)
        Kelas::create(['id_kelas' => 5, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'TBSM 1', 'jurusan' => 'TBSM']);
        Kelas::create(['id_kelas' => 6, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'TBSM 2', 'jurusan' => 'TBSM']);
        
        // 1 kelas Perhotelan (PH)
        Kelas::create(['id_kelas' => 7, 'id_guru' => null, 'tingkat' => 'X', 'nama_kelas' => 'PH 1', 'jurusan' => 'Perhotelan']);


        // =========================================================================
        // 2. DATA KELAS 11 (Total 7 Kelas)
        // =========================================================================
        
        // 2 kelas Teknik Komputer dan Jaringan (TKJ)
        Kelas::create(['id_kelas' => 8, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'TKJ 1', 'jurusan' => 'TJKT']);
        Kelas::create(['id_kelas' => 9, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'TKJ 2', 'jurusan' => 'TJKT']);
        
        // 2 kelas Rekayasa Perangkat Lunak (RPL)
        Kelas::create(['id_kelas' => 10, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'RPL 1', 'jurusan' => 'PPLG']);
        Kelas::create(['id_kelas' => 11, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'RPL 2', 'jurusan' => 'PPLG']);
        
        // 2 Kelas Teknik dan Bisnis Sepeda Motor (TBSM)
        Kelas::create(['id_kelas' => 12, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'TBSM 1', 'jurusan' => 'TBSM']);
        Kelas::create(['id_kelas' => 13, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'TBSM 2', 'jurusan' => 'TBSM']);
        
        // 1 kelas Perhotelan (PH)
        Kelas::create(['id_kelas' => 14, 'id_guru' => null, 'tingkat' => 'XI', 'nama_kelas' => 'PH 1', 'jurusan' => 'Perhotelan']);


        // =========================================================================
        // 3. DATA KELAS 12 (Total 8 Kelas)
        // =========================================================================
        
        // 3 kelas Teknik Komputer dan Jaringan (TKJ) - Dikunci ke id_guru 1, 2, 3 untuk Wali Kelas beneran
        Kelas::create(['id_kelas' => 15, 'id_guru' => 1, 'tingkat' => 'XII', 'nama_kelas' => 'TKJ 1', 'jurusan' => 'TJKT']);
        Kelas::create(['id_kelas' => 16, 'id_guru' => 2, 'tingkat' => 'XII', 'nama_kelas' => 'TKJ 2', 'jurusan' => 'TJKT']);
        Kelas::create(['id_kelas' => 17, 'id_guru' => null, 'tingkat' => 'XII', 'nama_kelas' => 'TKJ 3', 'jurusan' => 'TJKT']);
        
        // 2 kelas Rekayasa Perangkat Lunak (RPL) - Kelas XII RPL 1 dikunci ke Wali Kelas id_guru nomor 4
        Kelas::create(['id_kelas' => 18, 'id_guru' => 4, 'tingkat' => 'XII', 'nama_kelas' => 'RPL 1', 'jurusan' => 'PPLG']);
        Kelas::create(['id_kelas' => 19, 'id_guru' => null, 'tingkat' => 'XII', 'nama_kelas' => 'RPL 2', 'jurusan' => 'PPLG']);
        
        // 2 Kelas Teknik dan Bisnis Sepeda Motor (TBSM)
        Kelas::create(['id_kelas' => 20, 'id_guru' => null, 'tingkat' => 'XII', 'nama_kelas' => 'TBSM 1', 'jurusan' => 'TBSM']);
        Kelas::create(['id_kelas' => 21, 'id_guru' => null, 'tingkat' => 'XII', 'nama_kelas' => 'TBSM 2', 'jurusan' => 'TBSM']);
        
        // 1 kelas Perhotelan (PH)
        Kelas::create(['id_kelas' => 22, 'id_guru' => null, 'tingkat' => 'XII', 'nama_kelas' => 'PH 1', 'jurusan' => 'Perhotelan']);
    }
}
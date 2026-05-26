<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiMapel extends Model
{
    protected $primaryKey = 'id_presensi_mapel';

    protected $fillable = [
        'id_mengajar',
        'id_siswa',
        'status'
    ];

    // Relasi balik ke Induk Sesi Mengajar
    public function mengajar()
    {
        return $this->belongsTo(Mengajar::class, 'id_mengajar');
    }

    // Relasi balik ke data Siswa untuk mengambil Nama dan NIS murid di tabel
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
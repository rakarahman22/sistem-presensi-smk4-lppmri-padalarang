<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $primaryKey = 'id_presensi';

    protected $fillable = [
        'id_siswa',
        'id_guru',
        'tgl_presensi',
        'jam_masuk',
        'lat_siswa',
        'long_siswa',
        'status'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}


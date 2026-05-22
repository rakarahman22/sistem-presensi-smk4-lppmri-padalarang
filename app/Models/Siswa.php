<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use Notifiable;

    protected $table = 'siswas';
    protected $primaryKey = 'id_siswa'; // Beritahu Laravel primary key kamu

    protected $fillable = [
        'id_kelas', 'id_wali', 'nis', 'nama_siswa', 'username', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function wali()
    {
        return $this->belongsTo(WaliSiswa::class, 'id_wali');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_siswa');
    }
}



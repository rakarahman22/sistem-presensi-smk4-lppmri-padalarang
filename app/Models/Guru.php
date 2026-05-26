<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $table = 'gurus';
    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'nip',
        'nama_guru',
        'jabatan',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // 1. Hubungan One-to-One untuk Wali Kelas (Aman karena database sudah kita kunci UNIQUE)
    public function kelasDiampu()
    {
        return $this->hasOne(Kelas::class, 'id_guru', 'id_guru');
    }

    // 2. Hubungan ke tabel Plotting Mengajar buatan Admin kemarin
    public function plotMengajars()
    {
        return $this->hasMany(PlotMengajar::class, 'id_guru', 'id_guru');
    }
}
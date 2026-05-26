<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mengajar extends Model
{
    protected $primaryKey = 'id_mengajar';

    protected $fillable = [
        'id_guru',
        'id_kelas',
        'nama_mapel',
        'tgl_mengajar',
        'jam_mulai'
    ];

    // Relasi balik ke Guru (Satu sesi mengajar dimiliki oleh satu Guru)
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    // Relasi balik ke Kelas (Satu sesi mengajar dilakukan di satu Kelas)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // Relasi ke Detail Presensi Murid (One-to-Many)
    public function presensiMapels()
    {
        return $this->hasMany(PresensiMapel::class, 'id_mengajar');
    }
}
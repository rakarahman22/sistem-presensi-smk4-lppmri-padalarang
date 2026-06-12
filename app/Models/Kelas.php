<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'id_guru',
        'nama_kelas',
        'tingkat',
        'jurusan',
    ];

    /**
     * FIX: Sertakan FK dan PK secara eksplisit agar relasi tidak bergantung
     * pada konvensi nama yang bisa berbeda antar versi Laravel.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
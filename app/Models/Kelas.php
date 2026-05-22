<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    // FIX: Daftarkan id_guru di sini agar diizinkan masuk ke database!
    protected $fillable = [
        'id_guru', // ← WAJIB DITAMBAHKAN
        'nama_kelas',
        'tingkat',
        'jurusan'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }

    // Pastikan fungsi relasi ini juga sudah ada di dalam model Kelas kamu
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
<?php

namespace App\Models;

// Ganti atau tambahkan baris di bawah ini untuk kebutuhan login (Multi-Auth):
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WaliSiswa extends Authenticatable // ← Ubah dari 'extends Model' menjadi 'extends Authenticatable'
{
    use Notifiable;

    protected $table = 'wali_siswas';
    
    // Beritahu Laravel kalau primary key di tabel kamu adalah 'id_wali'
    protected $primaryKey = 'id_wali'; 

    protected $fillable = [
        'nama_wali',
        'username',
        'password',
        'no_telp',
    ];

    protected $hidden = [
        'password',
    ];

    // Opsional: Tambahkan ini jika belum mengaktifkan fitur enkripsi otomatis
    protected $casts = [
        'password' => 'hashed',
    ];
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_wali');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_wali');
    }
}
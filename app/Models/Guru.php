<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Tambahkan ini
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable // Ubah ke Authenticatable
{
    use Notifiable;

    protected $table = 'gurus';
    protected $primaryKey = 'id_guru'; // Pastikan primary key sesuai migrasi kamu

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

    // Tambahkan ini di dalam class Guru
    public function kelasDiampu()
    {
        // Guru memiliki satu kelas yang diampu
        return $this->hasOne(Kelas::class, 'id_guru', 'id_guru');
    }
}

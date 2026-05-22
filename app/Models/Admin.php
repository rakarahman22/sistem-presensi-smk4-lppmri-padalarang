<?php

namespace App\Models;

// HAPUS atau Biarkan ini: use Illuminate\Database\Eloquent\Model;
// TAMBAHKAN baris di bawah ini untuk autentikasi:
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable


{
    use Notifiable;

    protected $table = 'admins';
    
    // Beritahu Laravel kalau primary key kamu bukan 'id', melainkan 'id_admin'
    protected $primaryKey = 'id_admin'; 

    protected $fillable = [
        'username',
        'password',
        'nama_admin',
    ];

    protected $hidden = [
        'password',
    ];
}
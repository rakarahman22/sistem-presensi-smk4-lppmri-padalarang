<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturans';

    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'nama_sekolah', 'npsn', 'nama_kepsek', 'logo_sekolah', 
        'tahun_ajaran', 'jam_masuk', 'batas_terlambat', 'jam_pulang', // <-- Pastikan ada ini
        'hari_kerja', 'is_maintenance', 'lock_device'
    ];

    protected $casts = [
        'hari_kerja' => 'array',
        'is_maintenance' => 'boolean',
        'lock_device' => 'boolean',
    ];
}
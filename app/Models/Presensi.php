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
        'status',
        'keterangan',
        'status_awal',
        'dikoreksi_oleh',
        'edited_by',    // FIX: ada di DB tapi belum di fillable
        'edited_at',
    ];

    protected $casts = [
        'tgl_presensi' => 'date:Y-m-d',
        'edited_at'    => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
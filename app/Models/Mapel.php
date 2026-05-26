<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapels'; // Mempertegas nama tabel di database
    protected $primaryKey = 'id_mapel';

    // FIX: Tambahkan 'jurusan' di dalam array fillable agar bisa disimpan ke database!
    protected $fillable = [
        'jurusan', 
        'nama_mapel'
    ];

    /**
     * Relasi ke Tabel PlotMengajar (One-to-Many)
     * Tambahan opsional: Mempermudah jika suatu saat admin ingin melihat 
     * guru siapa saja yang memegang mata pelajaran ini.
     */
    public function plotMengajars()
    {
        return $this->hasMany(PlotMengajar::class, 'id_mapel', 'id_mapel');
    }
}
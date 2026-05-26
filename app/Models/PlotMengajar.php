<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotMengajar extends Model
{
    protected $primaryKey = 'id_plot';
    protected $fillable = ['id_guru', 'id_kelas', 'id_mapel'];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanGeofencing extends Model
{
    protected $table = 'pengaturan_geofencings';
    protected $primaryKey = 'id_geofence';

    protected $fillable = [
        'latitude_sekolah',
        'longitude_sekolah',
        'radius_meter',
    ];
}
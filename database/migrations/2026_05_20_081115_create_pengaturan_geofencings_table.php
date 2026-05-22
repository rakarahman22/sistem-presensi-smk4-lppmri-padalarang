<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_geofencings', function (Blueprint $table) {
            $table->id('id_geofence');
        
            $table->double('latitude_sekolah');
            $table->double('longitude_sekolah');
            $table->integer('radius_meter');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_geofencings');
    }
};

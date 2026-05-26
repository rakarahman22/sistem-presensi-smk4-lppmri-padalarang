<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_mapels', function (Blueprint $table) {
            $table->id('id_presensi_mapel'); // Primary Key Kustom
            $table->unsignedBigInteger('id_mengajar');
            $table->unsignedBigInteger('id_siswa');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir');
            $table->timestamps();

            // Deklarasi Relasi (Foreign Key) Hubungan Antar Tabel
            $table->foreign('id_mengajar')->references('id_mengajar')->on('mengajars')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_mapels');
    }
};
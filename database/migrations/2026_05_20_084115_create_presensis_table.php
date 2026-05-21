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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id('id_presensi');
        
            $table->unsignedBigInteger('id_siswa');
            
            // FIX: Tambahkan ->nullable() di ujung baris ini agar MySQL mengizinkan nilai kosong!
            $table->unsignedBigInteger('id_guru')->nullable(); 
        
            $table->date('tgl_presensi');
            $table->time('jam_masuk');
        
            $table->double('lat_siswa');
            $table->double('long_siswa');
        
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
        
            // Setup Foreign Key Relasi
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            
            // Sekarang aturan set null ini akan disetujui 100% oleh MySQL karena kolomnya sudah bersifat nullable
            $table->foreign('id_guru')->references('id_guru')->on('gurus')->onDelete('set null');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
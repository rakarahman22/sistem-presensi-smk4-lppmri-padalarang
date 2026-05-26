<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            
            // FIX: Tambahkan ->unique() agar satu id_guru tidak bisa dipakai di kelas lain!
            $table->unsignedBigInteger('id_guru')->nullable()->unique(); 
            
            $table->string('nama_kelas');
            $table->string('tingkat');
            $table->string('jurusan');
            $table->timestamps();

            // Setup hubungan langsung ke tabel guru
            $table->foreign('id_guru')->references('id_guru')->on('gurus')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
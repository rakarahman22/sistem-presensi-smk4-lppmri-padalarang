<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mengajars', function (Blueprint $table) {
            $table->id('id_mengajar'); // Primary Key Kustom
            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_kelas');
            $table->string('nama_mapel', 100);
            $table->date('tgl_mengajar');
            $table->time('jam_mulai');
            $table->timestamps();

            // Deklarasi Relasi (Foreign Key) Hubungan Antar Tabel
            $table->foreign('id_guru')->references('id_guru')->on('gurus')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mengajars');
    }
};
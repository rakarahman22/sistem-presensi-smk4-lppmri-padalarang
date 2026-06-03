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
    Schema::create('pengaturans', function (Blueprint $table) {
        $table->id('id_pengaturan');

        // Identitas sekolah
        $table->string('nama_sekolah')->nullable();
        $table->string('npsn')->nullable();
        $table->string('nama_kepsek')->nullable();
        $table->string('logo_sekolah')->nullable();
        $table->string('tahun_ajaran')->nullable();

        // Aturan presensi
        $table->time('jam_masuk')->nullable();
        $table->time('batas_terlambat')->nullable();
        $table->time('jam_pulang')->nullable();

        // Simpan array hari kerja dalam JSON
        $table->json('hari_kerja')->nullable();

        // Toggle
        $table->boolean('is_maintenance')->default(false);
        $table->boolean('lock_device')->default(true);

        $table->timestamps();
    });
}
};

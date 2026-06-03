<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id('id_presensi');

            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_guru')->nullable();

            $table->date('tgl_presensi');
            $table->time('jam_masuk');

            $table->double('lat_siswa');
            $table->double('long_siswa');

            $table->enum('status', [
                'Hadir',
                'Terlambat',
                'Izin',
                'Sakit',
                'Alpa',
            ]);

             // ── Kolom koreksi (baru) ──────────────────────────
            // Isi saat admin mengubah status. Null = belum pernah dikoreksi.
            $table->string('status_awal', 20)->nullable();
            // Nama admin / wali kelas yang melakukan koreksi
            $table->string('dikoreksi_oleh', 100)->nullable();
            // ─────────────────────────────────────────────────

            // Keterangan singkat saat admin melakukan koreksi
            // cth: "Sakit, surat ditunjukkan langsung" / "Izin lomba matematika"
            $table->string('keterangan')->nullable();

            // Audit trail: id admin yang melakukan koreksi
            $table->unsignedBigInteger('edited_by')->nullable();

            // Audit trail: waktu koreksi dilakukan
            $table->timestamp('edited_at')->nullable();

            $table->foreign('id_siswa')
                  ->references('id_siswa')
                  ->on('siswas')
                  ->onDelete('cascade');

            $table->foreign('id_guru')
                  ->references('id_guru')
                  ->on('gurus')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
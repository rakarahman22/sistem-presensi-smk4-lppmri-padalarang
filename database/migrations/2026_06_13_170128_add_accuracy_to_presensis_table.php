<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Akurasi GPS (meter) saat presensi dilakukan, untuk keperluan audit.
            // Null jika browser tidak mengirimkan data accuracy.
            $table->unsignedInteger('accuracy')->nullable()->after('long_siswa');
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn('accuracy');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            // Menambahkan kolom jurusan setelah id_mapel
            $table->string('jurusan', 50)->after('id_mapel')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropColumn('jurusan');
        });
    }
};
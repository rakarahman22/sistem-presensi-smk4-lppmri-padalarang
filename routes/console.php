<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menjalankan perintah cetak Alpa otomatis setiap hari kerja pada pukul 14:00 sore
Schedule::command('presensi:auto-alpha')
    ->dailyAt('14:00')
    ->timezone('Asia/Jakarta');
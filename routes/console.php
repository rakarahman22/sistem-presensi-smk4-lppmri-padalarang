<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Jadwal Auto-Alpha
|--------------------------------------------------------------------------
| Menjalankan perintah cetak Alpa otomatis setiap hari kerja pada jam pulang.
|
| PENTING: Schedule ini hanya berjalan jika Laravel Scheduler aktif.
| Jangan gunakan `php artisan schedule:work` untuk production karena
| akan berhenti saat terminal ditutup.
|
| Setup cron job yang benar di server (crontab -e):
|   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
|
| Atau jika pakai cPanel, tambahkan cron job:
|   * * * * * /usr/bin/php /home/username/public_html/artisan schedule:run
|
| Command `presensi:auto-alpha` sendiri sudah memiliki guard internal:
|   - Cek hari kerja aktif (dari pengaturan database)
|   - Cek apakah jam sekarang sudah >= jam_pulang (dari pengaturan database)
| Sehingga aman dijalankan setiap menit — tidak akan insert Alpa prematur.
|--------------------------------------------------------------------------
*/

// Jalankan setiap menit agar scheduler Laravel bisa dipanggil rutin oleh cron OS.
// Guard waktu (jam_pulang) ada di dalam command itu sendiri.
Schedule::command('presensi:auto-alpha')
    ->everyMinute()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
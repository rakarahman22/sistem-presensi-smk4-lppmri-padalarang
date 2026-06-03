<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Pengaturan;
use Illuminate\Support\Carbon;

class AutoAlphaCommand extends Command
{
    /**
     * Nama perintah yang akan dipanggil via terminal atau cron job
     */
    protected $signature = 'presensi:auto-alpha';

    /**
     * Deskripsi singkat perintah
     */
    protected $description = 'Otomatis mencatat Alpa bagi siswa yang tidak presensi hingga KBM selesai';

    public function handle()
    {
        $hariIni = Carbon::today()->toDateString();
        $pengaturan = Pengaturan::first();

        // 1. Cek jika pengaturan belum diatur atau sedang maintenance
        if (!$pengaturan || $pengaturan->is_maintenance) {
            $this->warn('Sistem maintenance atau pengaturan belum diset. Proses dibatalkan.');
            return Command::SUCCESS;
        }

        // 2. Cek apakah hari ini termasuk dalam Hari Kerja aktif di JSON database
        $hariSekarang = Carbon::now()->locale('id')->dayName;
        $hariAktif = $pengaturan->hari_kerja ?? [];

        if (!in_array(ucfirst($hariSekarang), $hariAktif)) {
            $this->info('Hari ini bukan hari kerja/sekolah. Tidak ada pengecekan Alpa.');
            return Command::SUCCESS;
        }

        // 3. Ambil seluruh siswa aktif
        $daftarSiswa = Siswa::all();
        $counter = 0;

        // FIX LOGIKA: Siapkan variabel waktu cadangan (fallback) sebelum looping
        // Jika kolom jam_pulang di database ternyata kosong, langsung pakai cadangan '16:00:00'
        $jamPulangSistem = !empty($pengaturan->jam_pulang) ? $pengaturan->jam_pulang : '14:00:00';

        foreach ($daftarSiswa as $siswa) {
            // Cek apakah siswa ini sudah punya rekaman presensi (Hadir/Terlambat/Izin/Sakit) hari ini
            $sudahAbsen = Presensi::where('id_siswa', $siswa->id_siswa)
                ->where('tgl_presensi', $hariIni)
                ->exists();

            // 4. Jika tidak ada rekaman sama sekali, otomatis buat baris baru dengan status 'Alpa'
            if (!$sudahAbsen) {
                Presensi::create([
                    'id_siswa'     => $siswa->id_siswa,
                    'id_guru'      => null,
                    'tgl_presensi' => $hariIni,
                    
                    // FIX UTAMA: Tidak boleh null! Gunakan variabel jam_pulang sistem yang valid
                    'jam_masuk'    => $jamPulangSistem, 
                    
                    'lat_siswa'    => 0,
                    'long_siswa'   => 0,
                    'status'       => 'Alpa', // Menyesuaikan dengan enum kapital database Anda ('Alpa')
                ]);
                $counter++;
            }
        }

        $this->info("Proses selesai. Berhasil mencatat {$counter} siswa sebagai Alpa hari ini.");
        return Command::SUCCESS;
    }
}
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
        $hariIni    = Carbon::today('Asia/Jakarta')->toDateString();
        $sekarang   = Carbon::now('Asia/Jakarta');
        $pengaturan = Pengaturan::first();

        // 1. Cek jika pengaturan belum diatur atau sedang maintenance
        if (!$pengaturan || $pengaturan->is_maintenance) {
            $this->warn('Sistem maintenance atau pengaturan belum diset. Proses dibatalkan.');
            return Command::SUCCESS;
        }

        // 2. Cek apakah hari ini termasuk dalam Hari Kerja aktif di JSON database
        $hariSekarang = $sekarang->locale('id')->dayName;
        $hariAktif    = $pengaturan->hari_kerja ?? [];

        if (!in_array(ucfirst($hariSekarang), $hariAktif)) {
            $this->info("Hari ini ({$hariSekarang}) bukan hari kerja/sekolah. Tidak ada pengecekan Alpa.");
            return Command::SUCCESS;
        }

        // 3. FIX: Guard waktu — batalkan jika KBM belum selesai
        //    Ambil jam_pulang dari pengaturan, fallback ke '14:00' jika kosong
        $jamPulangRaw = !empty($pengaturan->jam_pulang)
            ? $pengaturan->jam_pulang
            : '16:00';

        // Ambil hanya HH:MM (5 karakter) agar aman dari format 'HH:MM:SS'
        $jamPulangCarbon = Carbon::createFromFormat(
            'H:i',
            substr($jamPulangRaw, 0, 5),
            'Asia/Jakarta'
        );

        if ($sekarang->lt($jamPulangCarbon)) {
            $this->warn(
                "KBM belum selesai. Jam pulang: {$jamPulangCarbon->format('H:i')}, " .
                "sekarang: {$sekarang->format('H:i')}. Auto-Alpha dibatalkan."
            );
            return Command::SUCCESS;
        }

        // 4. Ambil seluruh siswa aktif
        $daftarSiswa = Siswa::all();
        $counter     = 0;

        foreach ($daftarSiswa as $siswa) {
            // Cek apakah siswa ini sudah punya rekaman presensi hari ini
            $sudahAbsen = Presensi::where('id_siswa', $siswa->id_siswa)
                ->where('tgl_presensi', $hariIni)
                ->exists();

            // 5. Jika tidak ada rekaman sama sekali, otomatis buat baris 'Alpa'
            if (!$sudahAbsen) {
                Presensi::create([
                    'id_siswa'     => $siswa->id_siswa,
                    'id_guru'      => null,
                    'tgl_presensi' => $hariIni,

                    // FIX: jam_masuk diisi waktu sekarang (bukan jam_pulang)
                    // Ini mencatat kapan sistem men-generate record Alpa
                    'jam_masuk'    => $sekarang->format('H:i:s'),

                    'lat_siswa'    => 0,
                    'long_siswa'   => 0,
                    'status'       => 'Alpa',
                ]);
                $counter++;
            }
        }

        $this->info(
            "✅ Proses selesai [{$hariIni}]. " .
            "Berhasil mencatat {$counter} siswa sebagai Alpa."
        );

        return Command::SUCCESS;
    }
}
<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $rowCount    = 0;
    protected int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip baris yang NIS atau nama kosong
            if (empty($row['nis']) || empty($row['nama_siswa'])) {
                $this->skippedCount++;
                continue;
            }

            $nis = trim($row['nis']);

            // Skip jika NIS sudah ada (duplikat)
            if (Siswa::where('nis', $nis)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Cari id_kelas berdasarkan nama kelas (misal: "TKJ 1") atau tingkat+nama
            $idKelas = null;
            if (!empty($row['nama_kelas'])) {
                $namaKelas = trim($row['nama_kelas']);
                $kelas = Kelas::where('nama_kelas', $namaKelas)->first()
                      ?? Kelas::whereRaw("CONCAT(tingkat, ' ', nama_kelas) = ?", [$namaKelas])->first();
                $idKelas = $kelas?->id_kelas;
            }

            // Cari id_wali berdasarkan nama wali
            $idWali = null;
            if (!empty($row['nama_wali'])) {
                $wali   = WaliSiswa::where('nama_wali', trim($row['nama_wali']))->first();
                $idWali = $wali?->id_wali;
            }

            // Username default = nis jika tidak diisi
            $username = !empty($row['username']) ? trim($row['username']) : $nis;

            // Password default = nis jika tidak diisi
            $password = !empty($row['password']) ? trim($row['password']) : $nis;

            Siswa::create([
                'nis'        => $nis,
                'nama_siswa' => trim($row['nama_siswa']),
                'username'   => $username,
                'password'   => Hash::make($password),
                'id_kelas'   => $idKelas,
                'id_wali'    => $idWali,
            ]);

            $this->rowCount++;
        }
    }

    public function getRowCount(): int    { return $this->rowCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
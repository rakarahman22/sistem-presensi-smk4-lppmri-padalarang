<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $rowCount    = 0;
    protected int $skippedCount = 0;

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = $this->normalize($row->toArray());

            if ($this->isHeaderRow($data)) {
                continue;
            }

            if (empty($data['nis']) || empty($data['nama_siswa'])) {
                $this->skippedCount++;
                continue;
            }

            $nis = trim($data['nis']);

            if (Siswa::where('nis', $nis)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $idKelas = null;
            if (!empty($data['nama_kelas'])) {
                $namaKelas = trim($data['nama_kelas']);
                $kelas = Kelas::where('nama_kelas', $namaKelas)->first()
                      ?? Kelas::whereRaw("CONCAT(tingkat, ' ', nama_kelas) = ?", [$namaKelas])->first();
                $idKelas = $kelas?->id_kelas;
            }

            $idWali = null;
            if (!empty($data['nama_wali'])) {
                $wali   = WaliSiswa::where('nama_wali', trim($data['nama_wali']))->first();
                $idWali = $wali?->id_wali;
            }

            $username = !empty($data['username']) ? trim($data['username']) : $nis;
            $password = !empty($data['password']) ? trim($data['password']) : $nis;

            Siswa::create([
                'nis'        => $nis,
                'nama_siswa' => trim($data['nama_siswa']),
                'username'   => $username,
                'password'   => Hash::make($password),
                'id_kelas'   => $idKelas,
                'id_wali'    => $idWali,
            ]);

            $this->rowCount++;
        }
    }

    private function normalize(array $row): array
    {
        $lookup = [];
        foreach ($row as $key => $value) {
            $lookup[strtolower(trim((string) $key))] = $value;
        }

        $map = [
            'nis'        => ['nis', 'nis *'],
            'nama_siswa' => ['nama_siswa', 'nama siswa', 'nama siswa *', 'nama'],
            'username'   => ['username'],
            'password'   => ['password'],
            'nama_kelas' => ['nama_kelas', 'nama kelas', 'kelas'],
            'nama_wali'  => ['nama_wali', 'nama wali', 'wali'],
        ];

        $result = [];
        foreach ($map as $standard => $aliases) {
            $result[$standard] = null;
            foreach ($aliases as $alias) {
                if (isset($lookup[$alias]) && $lookup[$alias] !== null && $lookup[$alias] !== '') {
                    $result[$standard] = $lookup[$alias];
                    break;
                }
            }
        }

        return $result;
    }

    private function isHeaderRow(array $data): bool
    {
        $val = strtolower(trim((string) ($data['nis'] ?? '')));
        return in_array($val, ['nis', 'nis *']);
    }

    public function getRowCount(): int    { return $this->rowCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
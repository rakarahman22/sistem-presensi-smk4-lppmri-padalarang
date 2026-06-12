<?php

namespace App\Imports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KelasImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private int $rowCount    = 0;
    private int $skippedCount = 0;

    /**
     * Baris heading yang dibaca = baris ke-2 (key teknis).
     * WithHeadingRow default = baris 1, kita override ke baris 2.
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function model(array $row): ?Kelas
    {
        // Kolom wajib: nama_kelas & tingkat
        $namaKelas = trim($row['nama_kelas'] ?? '');
        $tingkat   = trim($row['tingkat']    ?? '');

        if ($namaKelas === '' || $tingkat === '') {
            $this->skippedCount++;
            return null;
        }

        $jurusan = trim($row['jurusan'] ?? '');

        // Cek duplikat: kombinasi tingkat + nama_kelas + jurusan
        $exists = Kelas::where('tingkat',    $tingkat)
                       ->where('nama_kelas', $namaKelas)
                       ->when($jurusan !== '', fn($q) => $q->where('jurusan', $jurusan))
                       ->exists();

        if ($exists) {
            $this->skippedCount++;
            return null;
        }

        $this->rowCount++;

        return new Kelas([
            'nama_kelas' => $namaKelas,
            'tingkat'    => $tingkat,
            'jurusan'    => $jurusan !== '' ? $jurusan : null,
            // id_guru (wali kelas) tidak diisi via import; assign manual setelah import
        ]);
    }

    public function getRowCount(): int    { return $this->rowCount;    }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
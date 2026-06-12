<?php

namespace App\Imports;

use App\Models\WaliSiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class WaliSiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $rowCount     = 0;
    protected int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama_wali'])) {
                $this->skippedCount++;
                continue;
            }

            $username = !empty($row['username']) ? trim($row['username'])
                      : strtolower(str_replace(' ', '.', trim($row['nama_wali'])));

            // Skip duplikat username
            if (WaliSiswa::where('username', $username)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $password = !empty($row['password']) ? trim($row['password']) : 'wali1234';

            WaliSiswa::create([
                'nama_wali' => trim($row['nama_wali']),
                'username'  => $username,
                'password'  => Hash::make($password),
                'no_telp'   => !empty($row['no_telp']) ? trim($row['no_telp']) : null,
            ]);

            $this->rowCount++;
        }
    }

    public function getRowCount(): int     { return $this->rowCount; }
    public function getSkippedCount(): int  { return $this->skippedCount; }
}
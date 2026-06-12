<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class GuruImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $rowCount     = 0;
    protected int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama_guru'])) {
                $this->skippedCount++;
                continue;
            }

            $nip = !empty($row['nip']) ? trim($row['nip']) : null;

            // Skip jika NIP sudah ada
            if ($nip && Guru::where('nip', $nip)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $username = !empty($row['username']) ? trim($row['username'])
                      : ($nip ?? strtolower(str_replace(' ', '.', trim($row['nama_guru']))));

            // Skip jika username sudah ada
            if (Guru::where('username', $username)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $password = !empty($row['password']) ? trim($row['password'])
                      : ($nip ?? 'guru1234');

            Guru::create([
                'nip'      => $nip,
                'nama_guru'=> trim($row['nama_guru']),
                'jabatan'  => !empty($row['jabatan']) ? trim($row['jabatan']) : null,
                'username' => $username,
                'password' => Hash::make($password),
            ]);

            $this->rowCount++;
        }
    }

    public function getRowCount(): int     { return $this->rowCount; }
    public function getSkippedCount(): int  { return $this->skippedCount; }
}
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

            if (empty($data['nama_guru'])) {
                $this->skippedCount++;
                continue;
            }

            $nip = !empty($data['nip']) ? trim($data['nip']) : null;

            if ($nip && Guru::where('nip', $nip)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $username = !empty($data['username']) ? trim($data['username'])
                      : ($nip ?? strtolower(str_replace(' ', '.', trim($data['nama_guru']))));

            if (Guru::where('username', $username)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $password = !empty($data['password']) ? trim($data['password'])
                      : ($nip ?? 'guru1234');

            Guru::create([
                'nip'       => $nip,
                'nama_guru' => trim($data['nama_guru']),
                'jabatan'   => !empty($data['jabatan']) ? trim($data['jabatan']) : null,
                'username'  => $username,
                'password'  => Hash::make($password),
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
            'nip'      => ['nip'],
            'nama_guru'=> ['nama_guru', 'nama guru', 'nama guru *', 'nama'],
            'jabatan'  => ['jabatan'],
            'username' => ['username'],
            'password' => ['password'],
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
        $val = strtolower(trim((string) ($data['nama_guru'] ?? '')));
        return in_array($val, ['nama_guru', 'nama guru', 'nama guru *']);
    }

    public function getRowCount(): int     { return $this->rowCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
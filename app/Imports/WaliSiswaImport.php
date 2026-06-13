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

    /**
     * Baca baris ke-1 sebagai heading, lalu di collection() kita normalisasi
     * semua kemungkinan nama kolom ke key standar.
     * Baris ke-2 dari template resmi (key teknis) dideteksi & di-skip otomatis.
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Normalisasi key: terima label tampilan ATAU key teknis
            $data = $this->normalize($row->toArray());

            // Skip baris ke-2 template (berisi nama key teknis, bukan data nyata)
            if ($this->isHeaderRow($data)) {
                continue;
            }

            if (empty($data['nama_wali'])) {
                $this->skippedCount++;
                continue;
            }

            $username = !empty($data['username'])
                      ? trim($data['username'])
                      : strtolower(str_replace(' ', '.', trim($data['nama_wali'])));

            // Skip duplikat username
            if (WaliSiswa::where('username', $username)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $password = !empty($data['password']) ? trim($data['password']) : 'wali1234';

            WaliSiswa::create([
                'nama_wali' => trim($data['nama_wali']),
                'username'  => $username,
                'password'  => Hash::make($password),
                'no_telp'   => $this->formatNoTelp($data['no_telp'] ?? null) ?? '',
            ]);

            $this->rowCount++;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map semua kemungkinan nama kolom → key standar.
     */
    private function normalize(array $row): array
    {
        $lookup = [];
        foreach ($row as $key => $value) {
            $lookup[strtolower(trim((string) $key))] = $value;
        }

        $map = [
            'nama_wali' => ['nama_wali', 'nama wali', 'nama wali *', 'nama'],
            'username'  => ['username'],
            'password'  => ['password'],
            'no_telp'   => ['no_telp', 'no. telepon', 'no telepon', 'no.telepon',
                            'nomor telepon', 'telepon', 'hp', 'no hp', 'no. hp'],
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

    /**
     * Deteksi baris ke-2 template resmi (isinya nama key teknis, bukan data).
     */
    private function isHeaderRow(array $data): bool
    {
        $val = strtolower(trim((string) ($data['nama_wali'] ?? '')));
        return in_array($val, ['nama_wali', 'nama wali', 'nama wali *']);
    }

    /**
     * Normalisasi nomor telepon:
     * - Tangani format scientific Excel: 8.84357E+11 → 0884357000000
     * - Hapus karakter non-digit
     * - Sesuaikan awalan (62x / +62x / 8x → 08x)
     */
    private function formatNoTelp(mixed $raw): ?string
    {
        if ($raw === null || $raw === '') return null;

        $raw = trim((string) $raw);

        // Tangani notasi scientific dari Excel (misal: 8.84357E+11)
        if (preg_match('/^[\d,]+\.?\d*[Ee][+\-]?\d+$/', str_replace(',', '', $raw))) {
            $raw = number_format((float) str_replace(',', '', $raw), 0, '.', '');
        }

        // Hapus semua bukan digit dan +
        $clean = preg_replace('/[^\d+]/', '', $raw);

        if (empty($clean)) return null;

        if (str_starts_with($clean, '+62')) {
            $clean = '0' . substr($clean, 3);
        } elseif (str_starts_with($clean, '62') && strlen($clean) > 10) {
            $clean = '0' . substr($clean, 2);
        } elseif (!str_starts_with($clean, '0')) {
            $clean = '0' . $clean;
        }

        return $clean;
    }

    public function getRowCount(): int     { return $this->rowCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
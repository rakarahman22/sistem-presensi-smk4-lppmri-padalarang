<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KelasImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $rowCount     = 0;
    protected int $skippedCount = 0;

    /** @var array<int, array{row:int, reason:string, data:array}> */
    protected array $skippedDetails = [];

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // index dari Maatwebsite/Excel mulai dari 0, baris fisik di excel = index + 2 (heading di baris 1)
            $excelRowNumber = $index + 2;

            $data = $this->normalize($row->toArray());

            // Lewati baris heading teknis (misal baris ke-2 pada template yang berisi key nama_kelas, tingkat, dst)
            if ($this->isHeaderRow($data)) {
                continue;
            }

            // Validasi field wajib
            if (empty($data['nama_kelas']) || empty($data['tingkat'])) {
                $this->markSkipped($excelRowNumber, 'Nama Kelas atau Tingkat kosong', $data);
                continue;
            }

            $namaKelas = trim((string) $data['nama_kelas']);
            $tingkat   = trim((string) $data['tingkat']);

            // Cek duplikat (tingkat + nama_kelas)
            if (Kelas::where('tingkat', $tingkat)->where('nama_kelas', $namaKelas)->exists()) {
                $this->markSkipped($excelRowNumber, "Duplikat: kelas {$tingkat} {$namaKelas} sudah ada", $data);
                continue;
            }

            // Resolve wali kelas (id_guru) — hati-hati dengan constraint unique
            $idGuru   = null;
            $waliNote = null;

            if (!empty($data['nama_wali_kelas'])) {
                $namaWali = trim((string) $data['nama_wali_kelas']);
                $guru     = Guru::where('nama_guru', $namaWali)->first();

                if (!$guru) {
                    // Guru tidak ditemukan di sistem -> kelas tetap dibuat tanpa wali
                    $waliNote = "Guru '{$namaWali}' tidak ditemukan, wali kelas dikosongkan";
                } else {
                    // Karena id_guru unique di tabel kelas, satu guru hanya bisa
                    // menjadi wali untuk SATU kelas. Cek apakah guru ini sudah
                    // dipakai oleh kelas lain.
                    $sudahDipakai = Kelas::where('id_guru', $guru->id_guru)->exists();

                    if ($sudahDipakai) {
                        $waliNote = "Guru '{$namaWali}' sudah menjadi wali kelas lain, wali kelas dikosongkan";
                    } else {
                        $idGuru = $guru->id_guru;
                    }
                }
            }

            // Insert per-baris dibungkus try/catch agar 1 error tidak
            // menggagalkan seluruh proses import tanpa rollback.
            try {
                DB::transaction(function () use ($namaKelas, $tingkat, $data, $idGuru) {
                    Kelas::create([
                        'nama_kelas' => $namaKelas,
                        'tingkat'    => $tingkat,
                        'jurusan'    => !empty($data['jurusan']) ? trim((string) $data['jurusan']) : null,
                        'id_guru'    => $idGuru,
                    ]);
                });

                $this->rowCount++;

                if ($waliNote) {
                    // Kelas berhasil dibuat tapi wali tidak ter-assign — catat sebagai info, bukan skip
                    $this->skippedDetails[] = [
                        'row'    => $excelRowNumber,
                        'reason' => "Kelas {$tingkat} {$namaKelas} dibuat. {$waliNote}",
                        'data'   => $data,
                        'type'   => 'warning',
                    ];
                }

            } catch (\Throwable $e) {
                $this->markSkipped(
                    $excelRowNumber,
                    "Gagal menyimpan kelas {$tingkat} {$namaKelas}: " . $e->getMessage(),
                    $data
                );
            }
        }
    }

    private function markSkipped(int $excelRow, string $reason, array $data): void
    {
        $this->skippedCount++;
        $this->skippedDetails[] = [
            'row'    => $excelRow,
            'reason' => $reason,
            'data'   => $data,
            'type'   => 'skip',
        ];
    }

    private function normalize(array $row): array
    {
        $lookup = [];
        foreach ($row as $key => $value) {
            $lookup[strtolower(trim((string) $key))] = $value;
        }

        $map = [
            'nama_kelas'      => ['nama_kelas', 'nama kelas', 'nama kelas *', 'kelas'],
            'tingkat'         => ['tingkat', 'tingkat *'],
            'jurusan'         => ['jurusan'],
            'nama_wali_kelas' => ['nama_wali_kelas', 'nama wali kelas', 'wali kelas'],
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
        $val = strtolower(trim((string) ($data['nama_kelas'] ?? '')));
        return in_array($val, ['nama_kelas', 'nama kelas', 'nama kelas *']);
    }

    public function getRowCount(): int     { return $this->rowCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }

    /** @return array<int, array{row:int, reason:string, data:array, type:string}> */
    public function getSkippedDetails(): array { return $this->skippedDetails; }
}
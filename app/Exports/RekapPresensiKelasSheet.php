<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Presensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RekapPresensiKelasSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected Kelas $kelas;
    protected string $tglDari;
    protected string $tglSampai;
    protected array $tanggalList;

    protected array $statusMap = [
        'hadir'     => 'H',
        'terlambat' => 'T',
        'sakit'     => 'S',
        'izin'      => 'I',
        'alpa'      => 'A',
        'alpha'     => 'A',
        'absen'     => 'A',
    ];

    public function __construct(Kelas $kelas, string $tglDari, string $tglSampai)
    {
        $this->kelas     = $kelas;
        $this->tglDari   = $tglDari;
        $this->tglSampai = $tglSampai;

        $this->tanggalList = [];
        $cursor = Carbon::parse($tglDari)->startOfDay();
        $akhir  = Carbon::parse($tglSampai)->startOfDay();

        while ($cursor->lte($akhir)) {
            $this->tanggalList[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }
    }

    public function title(): string
    {
        $nama = trim(($this->kelas->tingkat ?? '') . ' ' . ($this->kelas->nama_kelas ?? ''));
        $nama = preg_replace('/[\\\\\/\?\*\[\]\:]/', '-', $nama);
        return mb_substr($nama ?: 'Sheet', 0, 31);
    }

    public function headings(): array
    {
        $headings = ['No', 'NIS', 'Nama Siswa'];

        foreach ($this->tanggalList as $tgl) {
            $headings[] = Carbon::parse($tgl)->format('d/m');
        }

        $headings[] = 'H';
        $headings[] = 'T';
        $headings[] = 'S';
        $headings[] = 'I';
        $headings[] = 'A';

        return $headings;
    }

    public function array(): array
    {
        $siswaList = $this->kelas
            ->siswa()
            ->orderBy('nama_siswa')
            ->get();

        if ($siswaList->isEmpty()) {
            return [];
        }

        $idSiswaList = $siswaList->pluck('id_siswa')->filter()->values();

        $presensiRaw = Presensi::whereIn('id_siswa', $idSiswaList)
            ->whereDate('tgl_presensi', '>=', $this->tglDari)
            ->whereDate('tgl_presensi', '<=', $this->tglSampai)
            ->get();

        $presensiAll = $presensiRaw->groupBy(fn($p) => (int) $p->id_siswa);

        $rows = [];
        $no   = 1;

        foreach ($siswaList as $siswa) {
            $row = [
                $no++,
                $siswa->nis ?? '-',
                $siswa->nama_siswa ?? '-',
            ];

            $presensiSiswa = $presensiAll
                ->get((int) $siswa->id_siswa, collect())
                ->keyBy(fn($p) => Carbon::parse($p->tgl_presensi)->format('Y-m-d'));

            $countH = $countT = $countS = $countI = $countA = 0;

            foreach ($this->tanggalList as $tgl) {
                $presensi = $presensiSiswa->get($tgl);

                if ($presensi) {
                    $kode = $this->statusMap[strtolower(trim($presensi->status))] ?? '?';

                    match ($kode) {
                        'H' => $countH++,
                        'T' => $countT++,
                        'S' => $countS++,
                        'I' => $countI++,
                        'A' => $countA++,
                        default => null,
                    };
                } else {
                    $kode = '-';
                }

                $row[] = $kode;
            }

            $row[] = $countH;
            $row[] = $countT;
            $row[] = $countS;
            $row[] = $countI;
            $row[] = $countA;

            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $totalKolom = 3 + count($this->tanggalList) + 5;
        $lastCol    = Coordinate::stringFromColumnIndex($totalKolom);
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($highestRow >= 2) {
            $sheet->getStyle('D2:' . $lastCol . $highestRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        return [];
    }
}
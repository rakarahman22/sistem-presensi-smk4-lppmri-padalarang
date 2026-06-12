<?php

namespace App\Exports;

use App\Exports\RekapPresensiKelasSheet;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapPresensiExport implements WithMultipleSheets
{
    protected $tglDari;
    protected $tglSampai;

    public function __construct(string $tglDari, string $tglSampai)
    {
        $this->tglDari   = $tglDari;
        $this->tglSampai = $tglSampai;
    }

    public function sheets(): array
    {
        $sheets = [];

        $kelasList = Kelas::orderBy('tingkat')
            ->orderBy('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        foreach ($kelasList as $kelas) {
            $sheets[] = new RekapPresensiKelasSheet($kelas, $this->tglDari, $this->tglSampai);
        }

        return $sheets;
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GuruImportController extends Controller
{
    /**
     * POST /admin/guru/import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_import.required' => 'File wajib dipilih.',
            'file_import.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = new GuruImport();
            Excel::import($import, $request->file('file_import'));

            $imported = $import->getRowCount();
            $skipped  = $import->getSkippedCount();

            $msg = "Import selesai: {$imported} guru berhasil diimpor";
            if ($skipped > 0) {
                $msg .= ", {$skipped} baris dilewati (NIP/username duplikat atau data tidak lengkap).";
            } else {
                $msg .= '.';
            }

            return redirect()->route('admin.guru')->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->route('admin.guru')
                ->with('import_error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/guru/template
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Guru');

        // ── Definisi kolom ────────────────────────────────────────────────────
        $columns = [
            ['key' => 'nip',       'label' => 'NIP',          'width' => 22,  'required' => false],
            ['key' => 'nama_guru', 'label' => 'Nama Guru *',  'width' => 32,  'required' => true],
            ['key' => 'jabatan',   'label' => 'Jabatan',      'width' => 28,  'required' => false],
            ['key' => 'username',  'label' => 'Username',     'width' => 22,  'required' => false],
            ['key' => 'password',  'label' => 'Password',     'width' => 22,  'required' => false],
        ];

        $colCount = count($columns);

        // ── Baris 1: Label ramah pengguna ─────────────────────────────────────
        foreach ($columns as $ci => $col) {
            $colNum = $ci + 1;
            $sheet->setCellValueByColumnAndRow($colNum, 1, $col['label']);
            $sheet->getStyleByColumnAndRow($colNum, 1)->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 11,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $col['required'] ? 'FF15803D' : 'FF166534'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF14532D'],
                    ],
                ],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(24);
        }

        // ── Baris 2: Key teknis (heading row yang dibaca GuruImport) ─────────
        foreach ($columns as $ci => $col) {
            $colNum = $ci + 1;
            $sheet->setCellValueByColumnAndRow($colNum, 2, $col['key']);
            $sheet->getStyleByColumnAndRow($colNum, 2)->applyFromArray([
                'font' => [
                    'bold'   => true,
                    'size'   => 9,
                    'italic' => true,
                    'color'  => ['argb' => 'FF166534'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD1FAE5'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF86EFAC'],
                    ],
                ],
            ]);
            $sheet->getRowDimension(2)->setRowHeight(16);
        }

        // ── Baris 3–5: Contoh data ────────────────────────────────────────────
        $examples = [
            ['198501012010011001', 'Dr. Budi Setiawan, M.Pd', 'Guru Matematika',        'budi.setiawan',  'pass123'],
            ['199203152015022002', 'Siti Aminah, S.Pd',       'Guru Bahasa Indonesia',  '',               ''],
            ['',                   'Ahmad Fauzan',             'Guru TIK',               'ahmad.fauzan',   ''],
        ];

        foreach ($examples as $ri => $row) {
            $rowNum = $ri + 3;
            foreach ($row as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 1, $rowNum, $val);
                $sheet->getStyleByColumnAndRow($ci + 1, $rowNum)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $ri % 2 === 0 ? 'FFFFFFFF' : 'FFF8FFF9'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFE2E8F0'],
                        ],
                    ],
                ]);
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(18);
        }

        // ── Baris kosong untuk isian user (baris 6–20) ───────────────────────
        for ($rowNum = 6; $rowNum <= 20; $rowNum++) {
            for ($ci = 1; $ci <= $colCount; $ci++) {
                $sheet->getStyleByColumnAndRow($ci, $rowNum)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFE2E8F0'],
                        ],
                    ],
                ]);
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(18);
        }

        // ── Catatan di bawah ─────────────────────────────────────────────────
        $noteRow = 22;
        $notes = [
            ['⚠️  Jangan hapus atau ubah baris ke-2 (key teknis berwarna hijau muda) — baris ini wajib ada.',  'FF92400E'],
            ['✅  Kolom bertanda * wajib diisi. Kolom lain boleh dikosongkan.',                                  'FF166534'],
            ['🔁  Username default = NIP (jika ada) atau nama.lowercase jika NIP kosong.',                      'FF1E40AF'],
            ['🔐  Password default = NIP (jika ada) atau "guru1234" jika NIP kosong.',                          'FF1E40AF'],
            ['🚫  Baris dengan NIP atau username yang sudah ada di sistem akan dilewati otomatis.',              'FF6B7280'],
        ];

        $sheet->setCellValueByColumnAndRow(1, $noteRow, 'CATATAN PENGISIAN:');
        $sheet->getStyleByColumnAndRow(1, $noteRow)->getFont()
            ->setBold(true)->setSize(10)
            ->setColor(new Color('FF374151'));
        $sheet->mergeCellsByColumnAndRow(1, $noteRow, $colCount, $noteRow);

        foreach ($notes as $ni => [$text, $colorArgb]) {
            $r = $noteRow + 1 + $ni;
            $sheet->setCellValueByColumnAndRow(1, $r, '  ' . $text);
            $sheet->getStyleByColumnAndRow(1, $r)->getFont()
                ->setSize(9)
                ->setColor(new Color($colorArgb));
            $sheet->mergeCellsByColumnAndRow(1, $r, $colCount, $r);
            $sheet->getRowDimension($r)->setRowHeight(16);
        }

        // ── Lebar kolom & freeze ─────────────────────────────────────────────
        foreach ($columns as $ci => $col) {
            $sheet->getColumnDimensionByColumn($ci + 1)->setWidth($col['width']);
        }
        $sheet->freezePane('A3');

        // ── Download ─────────────────────────────────────────────────────────
        $writer  = new Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'tpl_guru_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'template_import_guru.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
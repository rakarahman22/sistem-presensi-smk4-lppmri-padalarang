<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\KelasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KelasImportController extends Controller
{
    /**
     * POST /admin/kelas/import
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
            $import = new KelasImport();
            Excel::import($import, $request->file('file_import'));

            $imported = $import->getRowCount();
            $skipped  = $import->getSkippedCount();

            $msg = "Import selesai: {$imported} kelas berhasil diimpor";
            if ($skipped > 0) {
                $msg .= ", {$skipped} baris dilewati (duplikat atau data tidak lengkap).";
            } else {
                $msg .= '.';
            }

            return redirect()->route('admin.kelas')->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->route('admin.kelas')
                ->with('import_error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/kelas/template
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Kelas');

        // ── Definisi kolom ────────────────────────────────────────────────────
        $columns = [
            ['key' => 'nama_kelas', 'label' => 'Nama Kelas *',  'width' => 22, 'required' => true],
            ['key' => 'tingkat',    'label' => 'Tingkat *',      'width' => 16, 'required' => true],
            ['key' => 'jurusan',    'label' => 'Jurusan',        'width' => 30, 'required' => false],
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

        // ── Baris 2: Key teknis (heading row yang dibaca KelasImport) ─────────
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
            ['A',  '10', 'Teknik Komputer dan Jaringan'],
            ['B',  '11', 'Rekayasa Perangkat Lunak'],
            ['C',  '12', ''],
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
            ['⚠️  Jangan hapus atau ubah baris ke-2 (key teknis berwarna hijau muda) — baris ini wajib ada.',      'FF92400E'],
            ['✅  Kolom bertanda * wajib diisi. Kolom lain boleh dikosongkan.',                                     'FF166534'],
            ['🔢  Tingkat diisi angka, cth: 10, 11, atau 12.',                                                     'FF1E40AF'],
            ['🏫  Nama Kelas diisi huruf/angka kelas, cth: A, B, 1, 2, TKJ-1.',                                   'FF1E40AF'],
            ['📚  Jurusan boleh dikosongkan. Cth: Teknik Komputer dan Jaringan.',                                   'FF374151'],
            ['🚫  Kombinasi Nama Kelas + Tingkat + Jurusan yang sudah ada akan dilewati otomatis.',                 'FF6B7280'],
            ['👤  Wali kelas tidak bisa diimpor via Excel — assign wali kelas secara manual setelah import.',       'FF6B7280'],
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
        $tmpFile = tempnam(sys_get_temp_dir(), 'tpl_kelas_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'template_import_kelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
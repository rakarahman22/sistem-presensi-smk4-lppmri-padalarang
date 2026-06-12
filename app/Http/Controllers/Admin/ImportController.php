<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Imports\KelasImport;
use App\Imports\SiswaImport;
use App\Imports\WaliSiswaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ImportController extends Controller
{
    // ─────────────────────────────────────────────
    //  IMPORT HANDLERS
    // ─────────────────────────────────────────────

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_import.required' => 'File wajib dipilih.',
            'file_import.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new SiswaImport();
        Excel::import($import, $request->file('file_import'));

        $imported = $import->getRowCount();
        $skipped  = $import->getSkippedCount();

        return redirect()->route('admin.siswa')
            ->with('success', "Import selesai: {$imported} siswa berhasil diimpor" .
                ($skipped > 0 ? ", {$skipped} baris dilewati (duplikat/tidak valid)." : '.'));
    }

    public function importGuru(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_import.required' => 'File wajib dipilih.',
            'file_import.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new GuruImport();
        Excel::import($import, $request->file('file_import'));

        $imported = $import->getRowCount();
        $skipped  = $import->getSkippedCount();

        return redirect()->route('admin.guru')
            ->with('success', "Import selesai: {$imported} guru berhasil diimpor" .
                ($skipped > 0 ? ", {$skipped} baris dilewati (duplikat/tidak valid)." : '.'));
    }

    public function importKelas(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_import.required' => 'File wajib dipilih.',
            'file_import.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new KelasImport();
        Excel::import($import, $request->file('file_import'));

        $imported = $import->getRowCount();
        $skipped  = $import->getSkippedCount();

        return redirect()->route('admin.kelas')
            ->with('success', "Import selesai: {$imported} kelas berhasil diimpor" .
                ($skipped > 0 ? ", {$skipped} baris dilewati (duplikat/tidak valid)." : '.'));
    }

    public function importWali(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_import.required' => 'File wajib dipilih.',
            'file_import.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new WaliSiswaImport();
        Excel::import($import, $request->file('file_import'));

        $imported = $import->getRowCount();
        $skipped  = $import->getSkippedCount();

        return redirect()->route('admin.wali')
            ->with('success', "Import selesai: {$imported} wali siswa berhasil diimpor" .
                ($skipped > 0 ? ", {$skipped} baris dilewati (duplikat/tidak valid)." : '.'));
    }

    // ─────────────────────────────────────────────
    //  TEMPLATE DOWNLOAD HANDLERS
    // ─────────────────────────────────────────────

    public function templateSiswa()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Header
        $headers = ['nis', 'nama_siswa', 'username', 'password', 'nama_kelas', 'nama_wali'];
        $labels  = ['NIS *', 'Nama Siswa *', 'Username', 'Password', 'Nama Kelas', 'Nama Wali'];

        $this->styleHeader($sheet, $headers, $labels, count($headers));

        // Contoh data
        $examples = [
            ['2024001', 'Budi Santoso', 'budi.santoso', 'password123', 'TKJ 1', 'Santoso'],
            ['2024002', 'Siti Rahayu', 'siti.rahayu', '', 'RPL 2', 'Rahayu'],
            ['2024003', 'Ahmad Fauzi', '', '', '', ''],
        ];
        foreach ($examples as $ri => $row) {
            foreach ($row as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 1, $ri + 3, $val);
            }
        }

        // Catatan
        $this->addNotes($sheet, count($headers), [
            '* NIS dan Nama Siswa wajib diisi.',
            '  Username default = NIS jika kosong.',
            '  Password default = NIS jika kosong.',
            '  Nama Kelas: isi dengan nama kelas (cth: "TKJ 1") atau tingkat+nama (cth: "X TKJ 1").',
            '  Nama Wali: isi dengan nama wali yang sudah terdaftar di sistem.',
            '  Baris dengan NIS duplikat akan dilewati otomatis.',
        ]);

        $this->autoWidth($sheet, count($headers));

        return $this->downloadExcel($spreadsheet, 'template_import_siswa.xlsx');
    }

    public function templateGuru()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $headers = ['nip', 'nama_guru', 'jabatan', 'username', 'password'];
        $labels  = ['NIP', 'Nama Guru *', 'Jabatan', 'Username', 'Password'];

        $this->styleHeader($sheet, $headers, $labels, count($headers));

        $examples = [
            ['198501012010011001', 'Dr. Budi Setiawan', 'Guru Matematika', 'budi.setiawan', 'pass123'],
            ['199203152015022002', 'Siti Aminah, S.Pd', 'Guru Bahasa Indonesia', '', ''],
            ['', 'Ahmad Fauzan', 'Guru TIK', 'ahmad.fauzan', ''],
        ];
        foreach ($examples as $ri => $row) {
            foreach ($row as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 1, $ri + 3, $val);
            }
        }

        $this->addNotes($sheet, count($headers), [
            '* Nama Guru wajib diisi.',
            '  NIP boleh kosong jika tidak ada.',
            '  Username default = NIP (jika ada) atau nama.lowercase jika NIP kosong.',
            '  Password default = NIP (jika ada) atau "guru1234".',
            '  Baris dengan NIP/username duplikat akan dilewati.',
        ]);

        $this->autoWidth($sheet, count($headers));

        return $this->downloadExcel($spreadsheet, 'template_import_guru.xlsx');
    }

    public function templateKelas()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelas');

        $headers = ['nama_kelas', 'tingkat', 'jurusan', 'nama_wali_kelas'];
        $labels  = ['Nama Kelas *', 'Tingkat *', 'Jurusan', 'Nama Wali Kelas'];

        $this->styleHeader($sheet, $headers, $labels, count($headers));

        $examples = [
            ['TKJ 1', 'X', 'Teknik Komputer dan Jaringan', 'Dr. Budi Setiawan'],
            ['RPL 1', 'X', 'Rekayasa Perangkat Lunak', ''],
            ['TKJ 2', 'XI', 'Teknik Komputer dan Jaringan', 'Siti Aminah, S.Pd'],
        ];
        foreach ($examples as $ri => $row) {
            foreach ($row as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 1, $ri + 3, $val);
            }
        }

        $this->addNotes($sheet, count($headers), [
            '* Nama Kelas dan Tingkat wajib diisi.',
            '  Tingkat: X, XI, atau XII.',
            '  Nama Wali Kelas: isi dengan nama guru yang sudah terdaftar.',
            '  Kombinasi Tingkat + Nama Kelas yang sama akan dilewati (duplikat).',
        ]);

        $this->autoWidth($sheet, count($headers));

        return $this->downloadExcel($spreadsheet, 'template_import_kelas.xlsx');
    }

    public function templateWali()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Wali Siswa');

        $headers = ['nama_wali', 'username', 'password', 'no_telp'];
        $labels  = ['Nama Wali *', 'Username', 'Password', 'No. Telepon'];

        $this->styleHeader($sheet, $headers, $labels, count($headers));

        $examples = [
            ['Budi Santoso', 'budi.santoso', 'pass123', '081234567890'],
            ['Sri Wahyuni', '', '', '082345678901'],
            ['Hendra Gunawan', 'hendra.g', 'wali1234', ''],
        ];
        foreach ($examples as $ri => $row) {
            foreach ($row as $ci => $val) {
                $sheet->setCellValueByColumnAndRow($ci + 1, $ri + 3, $val);
            }
        }

        $this->addNotes($sheet, count($headers), [
            '* Nama Wali wajib diisi.',
            '  Username default = nama.lowercase (cth: "budi.santoso") jika kosong.',
            '  Password default = "wali1234" jika kosong.',
            '  No. Telepon boleh kosong.',
            '  Baris dengan username duplikat akan dilewati.',
        ]);

        $this->autoWidth($sheet, count($headers));

        return $this->downloadExcel($spreadsheet, 'template_import_wali_siswa.xlsx');
    }

    // ─────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────

    private function styleHeader($sheet, array $headers, array $labels, int $count): void
    {
        // Row 1: label (nama kolom ramah pengguna)
        // Row 2: key (nama kolom teknis — heading row yang dibaca importer)
        $headerBg   = 'FF15803D'; // hijau
        $subheadBg  = 'FFD1FAE5'; // hijau muda
        $exampleBg  = 'FFFAFAFA';

        for ($i = 0; $i < $count; $i++) {
            $col = $i + 1;

            // Baris 1: Label
            $sheet->setCellValueByColumnAndRow($col, 1, $labels[$i]);
            $sheet->getStyleByColumnAndRow($col, 1)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF166534']]],
            ]);

            // Baris 2: Key (heading row)
            $sheet->setCellValueByColumnAndRow($col, 2, $headers[$i]);
            $sheet->getStyleByColumnAndRow($col, 2)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF166534'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $subheadBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF86EFAC']]],
            ]);
        }

        // Style baris contoh (3-5)
        for ($row = 3; $row <= 5; $row++) {
            for ($col = 1; $col <= $count; $col++) {
                $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $exampleBg]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                ]);
            }
        }

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Freeze baris 1-2
        $sheet->freezePane('A3');
    }

    private function addNotes($sheet, int $colCount, array $notes): void
    {
        $noteStartRow = 7;
        $sheet->setCellValueByColumnAndRow(1, $noteStartRow, '📋 CATATAN:');
        $sheet->getStyleByColumnAndRow(1, $noteStartRow)->getFont()->setBold(true)->setColor(
            (new \PhpOffice\PhpSpreadsheet\Style\Color('FF92400E'))
        );

        foreach ($notes as $ni => $note) {
            $sheet->setCellValueByColumnAndRow(1, $noteStartRow + 1 + $ni, $note);
            $sheet->getStyleByColumnAndRow(1, $noteStartRow + 1 + $ni)
                ->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF6B7280'));
            $sheet->mergeCellsByColumnAndRow(1, $noteStartRow + 1 + $ni, $colCount, $noteStartRow + 1 + $ni);
        }
    }

    private function autoWidth($sheet, int $colCount): void
    {
        for ($i = 1; $i <= $colCount; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
    }

    private function downloadExcel(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'import_template_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
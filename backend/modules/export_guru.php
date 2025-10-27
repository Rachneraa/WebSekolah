<?php
require_once dirname(__DIR__) . '../../config/koneksi.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Ambil data guru dari database
$query = "SELECT g.nip, g.nama, g.jenis_kelamin, g.alamat, g.no_telp, m.nama as nama_mapel 
            FROM guru g 
            LEFT JOIN mapel m ON g.mapel_id = m.id";
$result = $db->query($query);

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$header = ["NIP", "Nama", "Jenis Kelamin", "Alamat", "No. Telepon", "Mata Pelajaran"];
$sheet->fromArray($header, NULL, 'A1');

// Styling header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(28);

// Data
$rowNum = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $row['nip']);
    $sheet->setCellValue('B' . $rowNum, $row['nama']);
    $sheet->setCellValue('C' . $rowNum, $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
    $sheet->setCellValue('D' . $rowNum, $row['alamat']);
    $sheet->setCellValue('E' . $rowNum, $row['no_telp']);
    $sheet->setCellValue('F' . $rowNum, $row['nama_mapel']);
    $rowNum++;
}

// Styling data: border semua, rata tengah untuk NIP & Jenis Kelamin, rata kiri untuk lainnya
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'AAAAAA']
        ]
    ]
];
$sheet->getStyle('A2:F' . ($rowNum - 1))->applyFromArray($dataStyle);

// Rata tengah NIP & Jenis Kelamin
$sheet->getStyle('A2:A' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('C2:C' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Rata kiri kolom lain
$sheet->getStyle('B2:B' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('D2:F' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Auto width kolom
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set header untuk download file Excel
$filename = "data_guru_" . date("YmdHis") . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
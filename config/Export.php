<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function toExcel($tabel, $where = '')
    {
        // Query data
        $query = "SELECT * FROM $tabel " . ($where ? "WHERE $where" : '');
        $result = $this->db->query($query);

        if (!$result) {
            throw new Exception("Error querying data: " . $this->db->error);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $columns = [];
        $col = 'A';
        $field_info = $result->fetch_fields();
        foreach ($field_info as $field) {
            $sheet->setCellValue($col . '1', ucfirst($field->name));
            $columns[] = $field->name;
            $col++;
        }

        // Isi data
        $row = 2;
        while ($data = $result->fetch_assoc()) {
            $col = 'A';
            foreach ($columns as $column) {
                $sheet->setCellValue($col . $row, $data[$column]);
                $col++;
            }
            $row++;
        }

        // Output file
        $filename = $tabel . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function toCSV($tabel, $where = '')
    {
        $query = "SELECT * FROM $tabel " . ($where ? "WHERE $where" : '');
        $result = $this->db->query($query);

        if (!$result) {
            throw new Exception("Error querying data: " . $this->db->error);
        }

        // Set header untuk download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $tabel . '_' . date('Y-m-d') . '.csv"');

        // Buat file handler
        $output = fopen('php://output', 'w');

        // Tulis header kolom
        $fields = mysqli_fetch_fields($result);
        $headers = array();
        foreach ($fields as $field) {
            $headers[] = $field->name;
        }
        fputcsv($output, $headers);

        // Tulis data
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class Import
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function fromExcel($file, $tabel)
    {
        // Validasi file
        $allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipe file tidak valid. Gunakan file Excel (.xlsx atau .xls)');
        }

        // Load spreadsheet
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Ambil header (baris pertama)
        $headers = array_shift($rows);
        $headers = array_map('strtolower', $headers);

        // Siapkan query
        $columns = implode(',', $headers);
        $placeholders = rtrim(str_repeat('?,', count($headers)), ',');
        $query = "INSERT INTO $tabel ($columns) VALUES ($placeholders)";

        // Insert data
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->db->error);
        }

        $success = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            try {
                $types = str_repeat('s', count($headers));
                $stmt->bind_param($types, ...$row);

                if ($stmt->execute()) {
                    $success++;
                } else {
                    $errors[] = "Baris " . ($i + 2) . ": " . $stmt->error;
                }
            } catch (Exception $e) {
                $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'errors' => $errors,
            'total' => count($rows)
        ];
    }

    public function fromCSV($file, $tabel)
    {
        if ($file['type'] != 'text/csv') {
            throw new Exception('File harus berformat CSV');
        }

        $handle = fopen($file['tmp_name'], 'r');

        // Baca header
        $headers = fgetcsv($handle);
        $headers = array_map('strtolower', $headers);

        // Siapkan query
        $columns = implode(',', $headers);
        $placeholders = rtrim(str_repeat('?,', count($headers)), ',');
        $query = "INSERT INTO $tabel ($columns) VALUES ($placeholders)";

        $stmt = $this->db->prepare($query);

        $success = 0;
        $errors = [];

        // Baca dan insert data
        while (($data = fgetcsv($handle)) !== FALSE) {
            try {
                $types = str_repeat('s', count($headers));
                $stmt->bind_param($types, ...$data);

                if ($stmt->execute()) {
                    $success++;
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }
}
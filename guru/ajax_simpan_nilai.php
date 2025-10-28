<?php
session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

$siswa = intval($_POST['siswa'] ?? 0);
$kelas = intval($_POST['kelas'] ?? 0);
$mapel = intval($_POST['mapel'] ?? 0);
$tugas = intval($_POST['tugas'] ?? 0);
$nilai = isset($_POST['nilai']) ? intval($_POST['nilai']) : null;

if (!$siswa || !$kelas || !$mapel || !$tugas || $nilai === null) {
    echo json_encode(['success'=>false]);
    exit;
}

// Simpan/update nilai
$stmt = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE nilai=VALUES(nilai)");
$stmt->bind_param("iiiii", $siswa, $kelas, $mapel, $tugas, $nilai);
$stmt->execute();
$stmt->close();

echo json_encode(['success'=>true]);
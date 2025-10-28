<?php

session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

$kelas = intval($_POST['kelas'] ?? 0);
$mapel = intval($_POST['mapel'] ?? 0);

if (!$kelas || !$mapel) {
    echo json_encode(['success'=>false]);
    exit;
}

// Cari tugas_ke terbesar
$stmt = $db->prepare("SELECT MAX(tugas_ke) FROM nilai_tugas WHERE kelas_id=? AND mapel_id=?");
$stmt->bind_param("ii", $kelas, $mapel);
$stmt->execute();
$stmt->bind_result($max_tugas);
$stmt->fetch();
$stmt->close();

$next_tugas = $max_tugas ? $max_tugas+1 : 1;

// Tambahkan baris dummy untuk setiap siswa di kelas (nilai kosong)
$siswa = [];
$stmt = $db->prepare("SELECT siswa_id FROM siswa WHERE kelas_id=?");
$stmt->bind_param("i", $kelas);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $siswa[] = $row['siswa_id'];
$stmt->close();

foreach ($siswa as $siswa_id) {
    $stmt = $db->prepare("INSERT IGNORE INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, NULL)");
    $stmt->bind_param("iiii", $siswa_id, $kelas, $mapel, $next_tugas);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success'=>true]);
<?php

session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

$kelas = intval($_POST['kelas'] ?? 0);
$mapel = intval($_POST['mapel'] ?? 0);
$tugas = intval($_POST['tugas'] ?? 0);

if (!$kelas || !$mapel || !$tugas) {
    echo json_encode(['success'=>false]);
    exit;
}

// Hapus semua nilai tugas_ke ini di kelas & mapel tsb
$stmt = $db->prepare("DELETE FROM nilai_tugas WHERE kelas_id=? AND mapel_id=? AND tugas_ke=?");
$stmt->bind_param("iii", $kelas, $mapel, $tugas);
$stmt->execute();
$stmt->close();

echo json_encode(['success'=>true]);
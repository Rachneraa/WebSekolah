<?php

header('Content-Type: application/json; charset=utf-8');

// path ke koneksi (project root = dirname(__DIR__, 2))
require_once dirname(__DIR__, 2) . '/config/koneksi.php';

$siswa_id = intval($_POST['siswa_id']);
$kelas_id = intval($_POST['kelas_id']);
$tanggal = $_POST['tanggal'];
$status = $_POST['status'];
$alasan = $_POST['alasan'];
$dibuat_oleh = 'admin'; // atau ambil dari session jika ada

try {
    // Cari absensi_id untuk kelas dan tanggal ini
    $stmt = $db->prepare("SELECT absensi_id FROM absensi WHERE kelas_id = ? AND tanggal = ?");
    $stmt->bind_param("is", $kelas_id, $tanggal);
    $stmt->execute();
    $stmt->bind_result($absensi_id);
    $stmt->fetch();
    $stmt->close();

    if (!$absensi_id) {
        echo json_encode(['success' => false, 'message' => 'Absensi belum dibuat untuk kelas dan tanggal ini']);
        exit;
    }

    // Cek apakah sudah ada alasan untuk siswa ini
    $check = $db->prepare("SELECT id FROM absensi_alasan WHERE siswa_id = ? AND absensi_id = ?");
    $check->bind_param("ii", $siswa_id, $absensi_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->bind_result($existing_id);
        $check->fetch();
        $update = $db->prepare("UPDATE absensi_alasan SET alasan = ?, status = ?, kelas_id = ?, tanggal = ? WHERE id = ?");
        $update->bind_param("ssisi", $alasan, $status, $kelas_id, $tanggal, $existing_id);
        $update->execute();
        echo json_encode(['success' => true]);
    } else {
        $insert = $db->prepare("INSERT INTO absensi_alasan (absensi_id, siswa_id, kelas_id, tanggal, alasan, status, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("iiissss", $absensi_id, $siswa_id, $kelas_id, $tanggal, $alasan, $status, $dibuat_oleh);
        $insert->execute();
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    // untuk debugging, kirim pesan error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>
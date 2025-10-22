<?php

session_start();
require_once '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

try {
    // Parse QR content
    $url = parse_url($data['qr_content']);
    parse_str($url['query'], $params);

    // Validate kelas
    if ($params['kelas'] != $_SESSION['kelas_id']) {
        echo json_encode(['success' => false, 'message' => 'Anda salah kelas! Silakan scan QR code kelas Anda.']);
        exit;
    }

    // Check if already attended today
    $check = $db->prepare("SELECT id FROM absensi WHERE siswa_id = ? AND DATE(tanggal) = CURDATE()");
    $check->bind_param("i", $_SESSION['user_id']);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Anda sudah absen hari ini']);
        exit;
    }

    // Record attendance
    $stmt = $db->prepare("INSERT INTO absensi (siswa_id, kelas_id, status) VALUES (?, ?, 'Hadir')");
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['kelas_id']);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Absensi berhasil']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
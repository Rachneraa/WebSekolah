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

    $kelas_id = $_SESSION['kelas_id'];
    $siswa_id = $_SESSION['user_id']; // harus sama dengan siswa_id di tabel siswa
    $tanggal = date('Y-m-d');

    // 1. Cek absensi harian kelas
    $absensi_q = $db->prepare("SELECT absensi_id FROM absensi WHERE kelas_id = ? AND tanggal = ?");
    $absensi_q->bind_param("is", $kelas_id, $tanggal);
    $absensi_q->execute();
    $absensi_r = $absensi_q->get_result();

    if ($absensi_r->num_rows > 0) {
        $absensi_id = $absensi_r->fetch_assoc()['absensi_id'];
    } else {
        // Buat absensi harian baru
        $insert_absensi = $db->prepare("INSERT INTO absensi (tanggal, kelas_id, total_siswa, hadir, sakit, izin, alpha) VALUES (?, ?, 0, 0, 0, 0, 0)");
        $insert_absensi->bind_param("si", $tanggal, $kelas_id);
        $insert_absensi->execute();
        $absensi_id = $db->insert_id;
    }

    // 2. Cek apakah siswa sudah absen hari ini
    $cek_q = $db->prepare("SELECT id FROM absensi_detail WHERE absensi_id = ? AND siswa_id = ?");
    $cek_q->bind_param("ii", $absensi_id, $siswa_id);
    $cek_q->execute();
    $cek_r = $cek_q->get_result();

    if ($cek_r->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Anda sudah absen hari ini']);
        exit;
    }

    // 3. Insert absensi detail
    $waktu_absen = date('H:i:s');
    $nama_siswa = $_SESSION['nama'] ?? '';
    $save_q = $db->prepare("INSERT INTO absensi_detail (absensi_id, siswa_id, status, kelas_id, tanggal, waktu_absen, nama_siswa) VALUES (?, ?, 'Hadir', ?, ?, ?, ?)");
    $save_q->bind_param("iiisss", $absensi_id, $siswa_id, $kelas_id, $tanggal, $waktu_absen, $nama_siswa);
    $save_q->execute();

    echo json_encode(['success' => true, 'message' => 'Absensi berhasil']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
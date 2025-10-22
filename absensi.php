<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_GET['kelas'])) {
    die('Invalid QR Code');
}

$kelas_id = $_GET['kelas'];
$siswa_id = $_SESSION['user_id']; // Asumsi siswa sudah login

// Cek apakah siswa terdaftar di kelas tersebut
$stmt = $db->prepare("SELECT id FROM siswa WHERE id = ? AND kelas_id = ?");
$stmt->bind_param("ii", $siswa_id, $kelas_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Anda tidak terdaftar di kelas ini!');
}

// Catat absensi
$stmt = $db->prepare("INSERT INTO absensi (siswa_id, kelas_id, tanggal) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $siswa_id, $kelas_id);
$stmt->execute();

echo "Absensi berhasil dicatat!";
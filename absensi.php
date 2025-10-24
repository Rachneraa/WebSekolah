<?php
ob_start();
session_start();
require_once 'config/koneksi.php';

if (!isset($_GET['kelas'])) {
    die('Invalid QR Code');
}

$kelas_id = $_GET['kelas'];
$siswa_id = $_SESSION['user_id']; // Asumsi siswa sudah login

// Cek apakah siswa terdaftar di kelas tersebut
$stmt = $db->prepare("SELECT id FROM siswa WHERE siswa_id = ? AND kelas_id = ?");
$stmt->bind_param("ii", $siswa_id, $kelas_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Anda tidak terdaftar di kelas ini!');
}

// Ambil nama siswa
$stmt = $db->prepare("SELECT nama FROM siswa WHERE siswa_id = ?");
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$nama_siswa = $row['nama'];

// Catat absensi ke absensi_detail
$stmt = $db->prepare("INSERT INTO absensi_detail (siswa_id, kelas_id, tanggal, waktu_absen, nama_siswa) VALUES (?, ?, CURDATE(), CURTIME(), ?)");
$stmt->bind_param("iis", $siswa_id, $kelas_id, $nama_siswa);
$stmt->execute();

echo "Absensi berhasil dicatat!";
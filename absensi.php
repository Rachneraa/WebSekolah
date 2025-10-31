<?php
ob_start();
session_start();

// ### AWAL PERBAIKAN PATH ###
// Kita tidak bisa menggunakan 'config/koneksi.php'.
// Kita harus menggunakan path lengkap (BASE_PATH) seperti file 'absensi.php' Anda.

// Tentukan BASE_PATH jika belum ada
if (!defined('BASE_PATH')) {
    // Asumsi file ini ada di /backend/modules/ (atau 3 level dari root)
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Panggil koneksi menggunakan BASE_PATH
// Ini mengasumsikan koneksi.php ada di /config/koneksi.php (dihitung dari root)
require_once BASE_PATH . '/config/koneksi.php'; 
// ### AKHIR PERBAIKAN PATH ###


// 1. DEBUG: Cek apakah fungsi ada SETELAH require
if (!function_exists('updateAbsensiSummary')) {
    // Jika Anda melihat ini, berarti path BASE_PATH di atas salah
    die('ERROR FATAL: Fungsi updateAbsensiSummary() tidak ditemukan. 
    Path require_once MUNGKIN MASIH SALAH. 
    BASE_PATH saat ini: ' . BASE_PATH .
    ' | Mencari file di: ' . BASE_PATH . '/config/koneksi.php');
}

// Atur timezone
date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['kelas'])) {
    die('Invalid QR Code');
}

$kelas_id = $_GET['kelas'];
$siswa_id = $_SESSION['user_id']; // Asumsi siswa sudah login
$tanggal = date('Y-m-d'); // Tentukan tanggal

// Cek apakah siswa terdaftar di kelas tersebut
$stmt = $db->prepare("SELECT id FROM siswa WHERE siswa_id = ? AND kelas_id = ?");
if (!$stmt) die('Prepare 1 gagal: ' . $db->error); // Tambahan debug
$stmt->bind_param("ii", $siswa_id, $kelas_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Anda tidak terdaftar di kelas ini!');
}

// Ambil nama siswa
$stmt_nama = $db->prepare("SELECT nama FROM siswa WHERE siswa_id = ?");
if (!$stmt_nama) die('Prepare 2 gagal: ' . $db->error); // Tambahan debug
$stmt_nama->bind_param("i", $siswa_id);
$stmt_nama->execute();
$nama_siswa = $stmt_nama->get_result()->fetch_assoc()['nama'];
$stmt_nama->close();

// Catat absensi ke absensi_detail
$stmt_detail = $db->prepare("
    INSERT INTO absensi_detail (siswa_id, kelas_id, tanggal, waktu_absen, nama_siswa, status) 
    VALUES (?, ?, ?, CURTIME(), ?, 'Hadir')
    ON DUPLICATE KEY UPDATE
        waktu_absen = VALUES(waktu_absen),
        status = VALUES(status)
");
if (!$stmt_detail) die('Prepare 3 gagal: ' . $db->error); // Tambahan debug
$stmt_detail->bind_param("iiss", $siswa_id, $kelas_id, $tanggal, $nama_siswa);

if (!$stmt_detail->execute()) {
    die('Execute 3 (insert detail) gagal: ' . $stmt_detail->error); // Tambahan debug
}
$stmt_detail->close();

// -------------------------------------------------------------------
// 3. PANGGIL DAN CEK FUNGSI UPDATE RINGKASAN
// -------------------------------------------------------------------
$update_sukses = updateAbsensiSummary($db, $kelas_id, $tanggal);

if ($update_sukses) {
    // Jika berhasil
    echo "Absensi berhasil dicatat DAN ringkasan diupdate!";
} else {
    // Jika fungsi mengembalikan false, tampilkan error
    die("Absensi detail berhasil dicatat, TAPI GAGAL update ringkasan. 
    Ini berarti ada error di dalam fungsi updateAbsensiSummary() di koneksi.php. 
    Periksa query di dalam fungsi.");
}

ob_end_flush();
?>


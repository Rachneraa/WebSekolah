<?php

$server = "localhost"; // Ganti dengan MySQL Hostname Anda
$user = "root";     // Ganti dengan MySQL Username Anda
$password = "";        // Ganti dengan Password Akun InfinityFree Anda
$nama_database = "sekolah_db"; // Ganti dengan MySQL Database Name

$db = mysqli_connect($server, $user, $password, $nama_database);
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set UTF-8 encoding
mysqli_set_charset($db, "utf8mb4");

// Enable error reporting for development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

mysqli_set_charset($db, "utf8mb4");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('Asia/Jakarta');

function updateAbsensiSummary($db, $kelas_id, $tanggal)
{
    // 1. Hitung total Hadir dari 'absensi_detail'
    $stmt_hadir = $db->prepare("SELECT COUNT(DISTINCT siswa_id) FROM absensi_detail WHERE kelas_id = ? AND tanggal = ? AND status = 'Hadir'");
    if (!$stmt_hadir) { echo "Error prepare (hadir): " . $db->error; return false; }
    $stmt_hadir->bind_param("is", $kelas_id, $tanggal);
    $stmt_hadir->execute();
    $total_hadir = $stmt_hadir->get_result()->fetch_row()[0] ?? 0;
    $stmt_hadir->close();

    // 2. Hitung total Sakit dari 'absensi_alasan'
    $stmt_sakit = $db->prepare("SELECT COUNT(DISTINCT siswa_id) FROM absensi_alasan WHERE kelas_id = ? AND tanggal = ? AND status = 'Sakit'");
    if (!$stmt_sakit) { echo "Error prepare (sakit): " . $db->error; return false; }
    $stmt_sakit->bind_param("is", $kelas_id, $tanggal);
    $stmt_sakit->execute();
    $total_sakit = $stmt_sakit->get_result()->fetch_row()[0] ?? 0;
    $stmt_sakit->close();

    // 3. Hitung total Izin dari 'absensi_alasan'
    $stmt_izin = $db->prepare("SELECT COUNT(DISTINCT siswa_id) FROM absensi_alasan WHERE kelas_id = ? AND tanggal = ? AND status = 'Izin'");
    if (!$stmt_izin) { echo "Error prepare (izin): " . $db->error; return false; }
    $stmt_izin->bind_param("is", $kelas_id, $tanggal);
    $stmt_izin->execute();
    $total_izin = $stmt_izin->get_result()->fetch_row()[0] ?? 0;
    $stmt_izin->close();

    // 4. Hitung total Alpha dari 'absensi_alasan'
    $stmt_alpha = $db->prepare("SELECT COUNT(DISTINCT siswa_id) FROM absensi_alasan WHERE kelas_id = ? AND tanggal = ? AND status = 'Alpha'");
    if (!$stmt_alpha) { echo "Error prepare (alpha): " . $db->error; return false; }
    $stmt_alpha->bind_param("is", $kelas_id, $tanggal);
    $stmt_alpha->execute();
    $total_alpha = $stmt_alpha->get_result()->fetch_row()[0] ?? 0;
    $stmt_alpha->close();

    // 5. Update atau Insert ke tabel ringkasan 'absensi'
    // Menggunakan ON DUPLICATE KEY UPDATE agar aman (bisa INSERT jika belum ada, atau UPDATE jika sudah ada)
    $stmt_summary = $db->prepare("
        INSERT INTO absensi (kelas_id, tanggal, hadir, sakit, izin, alpha)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            hadir = VALUES(hadir), 
            sakit = VALUES(sakit), 
            izin = VALUES(izin), 
            alpha = VALUES(alpha)
    ");
    
    if (!$stmt_summary) { echo "Error prepare (summary): " . $db->error; return false; }
    $stmt_summary->bind_param("isiiii", $kelas_id, $tanggal, $total_hadir, $total_sakit, $total_izin, $total_alpha);
    
    if (!$stmt_summary->execute()) {
        echo "Error execute (summary): " . $stmt_summary->error;
        $stmt_summary->close();
        return false;
    }
    
    $stmt_summary->close();
    
    return true;
}
?>
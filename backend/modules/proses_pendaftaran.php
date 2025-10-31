<?php
session_start();
include '../../config/koneksi.php';

// Periksa apakah metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $jenis_kelamin = mysqli_real_escape_string($db, $_POST['jenis_kelamin']);
    $agama = mysqli_real_escape_string($db, $_POST['agama']);
    $tempat_lahir = mysqli_real_escape_string($db, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tahun'] . '-' . $_POST['bulan'] . '-' . $_POST['tanggal'];
    $nisn = mysqli_real_escape_string($db, $_POST['nisn']);
    $alamat_email = mysqli_real_escape_string($db, $_POST['alamat_email']);
    $no_hp = mysqli_real_escape_string($db, $_POST['no_hp']);
    $nama_sekolah = mysqli_real_escape_string($db, $_POST['nama_sekolah']);
    $jurusan = mysqli_real_escape_string($db, $_POST['jurusan']);
    $status = 'proses';

    // === CEK NISN SUDAH ADA ATAU BELUM ===
    $check_nisn_query = "SELECT id FROM ppdb_pendaftar WHERE nisn = ?";
    if ($check_stmt = mysqli_prepare($db, $check_nisn_query)) {
        mysqli_stmt_bind_param($check_stmt, "s", $nisn);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        // Jika NISN sudah terdaftar
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            mysqli_stmt_close($check_stmt);
            header("Location: ../../pendaftaran.php?error=nisn_duplicate");
            exit();
        }
        mysqli_stmt_close($check_stmt);
    }

    // Query INSERT ke tabel 'pendaftaran'
    $query = "INSERT INTO ppdb_pendaftar (nama_lengkap, jenis_kelamin, agama, tempat_lahir, tanggal_lahir, nisn, alamat_email, no_hp, nama_sekolah, jurusan, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssss",
            $nama_lengkap,
            $jenis_kelamin,
            $agama,
            $tempat_lahir,
            $tanggal_lahir,
            $nisn,
            $alamat_email,
            $no_hp,
            $nama_sekolah,
            $jurusan,
            $status
        );

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // === BERHASIL ===
            header("Location: ../../pendaftaran.php?status=success");
            exit();
        } else {
            // === GAGAL INSERT ===
            header("Location: ../../pendaftaran.php?error=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        // === GAGAL PREPARE STATEMENT ===
        header("Location: ../../pendaftaran.php?error=1");
        exit();
    }
    mysqli_close($db);

} else {
    // Jika bukan metode POST, redirect ke halaman pendaftaran
    header("Location: ../../pendaftaran.php");
    exit();
}
?>
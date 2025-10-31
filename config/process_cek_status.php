<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form cek status
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $nisn = mysqli_real_escape_string($db, $_POST['nisn']); // NISN adalah 'password'

    $query = "SELECT * FROM ppdb_pendaftar WHERE nama_lengkap = ? AND nisn = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nama_lengkap, $nisn); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($pendaftar = mysqli_fetch_assoc($result)) {
        // --- BERHASIL ---
        // Simpan data pendaftar ke session
        $_SESSION['pendaftar_id'] = $pendaftar['id'];
        $_SESSION['nama_pendaftar'] = $pendaftar['nama_lengkap'];
        $_SESSION['pendaftar_nisn'] = $pendaftar['nisn']; // Simpan NISN
        $_SESSION['level'] = 'pendaftar';
        
        // Arahkan ke halaman detail status
        header("Location: ../detail_status.php"); 
        exit();
    } else {
        // --- GAGAL ---
        // Set error khusus untuk modal status
        $_SESSION['status_error'] = "Nama Lengkap atau NISN tidak ditemukan.";
        // Kembali ke halaman index (modal akan terbuka otomatis)
        header("Location: ../ppdb.php"); 
    }
} else {
    // Jika diakses tanpa submit form POST
    header("Location: ../ppdb.php");
}
?>
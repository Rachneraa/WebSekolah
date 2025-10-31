<?php
// File: backend/get_siswa.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Path ini sudah benar jika 'get_siswa.php' ada di 'backend/'
require_once '../config/koneksi.php'; 

header('Content-Type: application/json');

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // === PERBAIKAN: Gunakan 'siswa_id' bukan 'id' ===
    $stmt = $db->prepare("SELECT siswa_id, nama, username, kelas_id FROM siswa WHERE siswa_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if ($data) {
        echo json_encode($data); // Kirim data sebagai JSON
    } else {
        // Kirim error jika ID tidak ditemukan
        echo json_encode(['error' => 'Siswa not found']);
    }
} else {
    // Kirim error jika tidak ada ID
    echo json_encode(['error' => 'No ID provided']);
}
?>
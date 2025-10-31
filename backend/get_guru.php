<?php
// File: backend/modules/get_guru.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Path dari 'modules/' ke 'config/koneksi.php'
require_once dirname(__DIR__) . '/config/koneksi.php'; 

header('Content-Type: application/json');

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Ambil data guru. Kolom 'id' adalah primary key di tabel 'guru'.
    $stmt = $db->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if ($data) {
        // Kirim data sebagai JSON
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Guru not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
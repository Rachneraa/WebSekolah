<?php
session_start();
// Path ke koneksi.php
require_once __DIR__ . '/../../config/koneksi.php'; 

header('Content-Type: application/json');

// Keamanan: Hanya admin yang boleh
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Ambil data JSON dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// === INI ADALAH LOGIKA PENTINGNYA ===
// Kondisi 1: status = 'ditolak'
// Kondisi 2: status = 'diterima' DAN status_akun = 1
$where_sql = "WHERE (status = 'ditolak' OR (status = 'diterima' AND status_akun = 1))";
// === AKHIR LOGIKA ===

try {
    if ($action === 'check') {
        // Aksi 1: Cek/hitung berapa data yang akan dihapus
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM ppdb_pendaftar $where_sql");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['total'];
        $stmt->close();
        
        echo json_encode(['success' => true, 'count' => $count]);

    } else if ($action === 'delete') {
        // Aksi 2: Jalankan penghapusan data
        $stmt = $db->prepare("DELETE FROM ppdb_pendaftar $where_sql");
        $stmt->execute();
        
        // Ambil info berapa baris yang terhapus
        $deleted_rows = $stmt->affected_rows;
        $stmt->close();
        
        echo json_encode(['success' => true, 'deleted_rows' => $deleted_rows]);

    } else {
        throw new Exception('Aksi tidak valid.');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$db->close();
?>
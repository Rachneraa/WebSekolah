<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/koneksi.php';

// Kita belum perlu Guzzle jika hanya tes, jadi ini bisa di-comment juga
// require_once __DIR__ . '/../../vendor/autoload.php'; 

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

$wa_response = null;
$wa_error = null;
$wa_sent = false;
$success = false; // Inisialisasi

if ($id && in_array($status, ['proses', 'diterima', 'ditolak'])) {
    // Pastikan $db ada dan bukan null
    if (!isset($db) || $db->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Koneksi database gagal.']);
        exit();
    }

    $stmt = $db->prepare("UPDATE ppdb_pendaftar SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $success = $stmt->execute();

    // =========================================================
    // SEMUA LOGIKA WA KITA NONAKTIFKAN SEMENTARA UNTUK TES
    // =========================================================
    /*
    if ($success && $status === 'diterima') {
        // ... (semua kode select nama, no_hp, dll) ...
        
        // $client = new \GuzzleHttp\Client(['verify' => false]); // <--- INI JUGA DI-COMMENT

        // ... (semua block try...catch Fonnte) ...
    }
    */
    // =========================================================
    // AKHIR BLOK TES
    // =========================================================

    // Cek jika statement update berhasil
    if ($success) {
        echo json_encode([
            'success' => true,
            'wa_sent' => false, // Kita set false karena WA dimatikan
            'wa_error' => 'Fitur WA dinonaktifkan untuk tes',
            'wa_response' => null
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Gagal update database: ' . $stmt->error
        ]);
    }
    $stmt->close();

} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid input'
    ]);
}
?>
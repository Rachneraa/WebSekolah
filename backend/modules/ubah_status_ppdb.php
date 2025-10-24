<?php
// filepath: c:\laragon\www\Web-Sekolah\backend\modules\ubah_status_ppdb.php
require_once __DIR__ . '/../../config/koneksi.php';

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

if ($id && in_array($status, ['proses', 'diterima', 'ditolak'])) {
    $stmt = $db->prepare("UPDATE ppdb_pendaftar SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}
?>
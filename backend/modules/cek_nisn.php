<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/koneksi.php';

$nisn = isset($_POST['nisn']) ? trim($_POST['nisn']) : '';

if ($nisn == '') {
    echo json_encode(['status' => 'empty']);
    exit;
}

// Debug: cek koneksi
if (!$db) {
    echo json_encode(['status' => 'db_error']);
    exit;
}

// Debug: cek query
$query = mysqli_query($db, "SELECT COUNT(*) as total FROM ppdb_pendaftar WHERE nisn='$nisn'");
if (!$query) {
    echo json_encode(['status' => 'query_error', 'error' => mysqli_error($db)]);
    exit;
}

$data = mysqli_fetch_assoc($query);

if ($data['total'] > 0) {
    echo json_encode(['status' => 'exists']);
} else {
    echo json_encode(['status' => 'ok']);
}
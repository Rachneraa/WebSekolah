
<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $kelas = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode($kelas);
}
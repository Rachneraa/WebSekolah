<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    die('Unauthorized');
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $query = "SELECT * FROM berita WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($row);
}
?>
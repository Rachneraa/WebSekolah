<?php

session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($db, $_POST['nama']);
    $password = $_POST['password'];

    $query = "SELECT * FROM guru WHERE nama = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($guru = $result->fetch_assoc()) {
        if (password_verify($password, $guru['password'])) {
            $_SESSION['user_id'] = $guru['id'];
            $_SESSION['nama_guru'] = $guru['nama'];
            $_SESSION['level'] = 'guru';
            header("Location: dashboard_guru.php");
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = "Nama guru tidak ditemukan!";
    }
    header("Location: login_guru.php");
    exit();
}
?>
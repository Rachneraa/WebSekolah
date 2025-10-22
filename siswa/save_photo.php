<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    die('Unauthorized');
}

if (isset($_POST['photo'])) {
    $img = $_POST['photo'];
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);

    $filename = 'login_' . $_SESSION['user_id'] . '_' . date('Ymd_His') . '.jpg';
    $file = '../uploads/login_photos/' . $filename;

    if (file_put_contents($file, $data)) {
        // Update login history with photo
        $stmt = $db->prepare("UPDATE login_history SET login_photo = ? WHERE siswa_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("si", $filename, $_SESSION['user_id']);
        $stmt->execute();

        // Update siswa table
        $stmt = $db->prepare("UPDATE siswa SET login_photo = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $_SESSION['user_id']);
        $stmt->execute();

        header('Location: dashboard.php');
        exit();
    }
}

header('Location: capture.php');
exit();
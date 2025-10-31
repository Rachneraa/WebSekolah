<?php

session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Accept either 'nip' or 'nama' as identifier from the login form
    $identifier = '';
    if (!empty($_POST['nip'])) {
        $identifier = trim($_POST['nip']);
        $by = 'nip';
    } else {
        $identifier = trim($_POST['nama'] ?? '');
        $by = 'nama';
    }

    $password = $_POST['password'] ?? '';

    if ($identifier === '') {
        $_SESSION['error'] = 'Identifier kosong.';
        header("Location: login_guru.php");
        exit();
    }

    // Prepare query depending on identifier type
    if ($by === 'nip') {
        $query = "SELECT * FROM guru WHERE nip = ? LIMIT 1";
    } else {
        $query = "SELECT * FROM guru WHERE nama = ? LIMIT 1";
    }

    $stmt = $db->prepare($query);
    if (!$stmt) {
        $_SESSION['error'] = 'Query prepare gagal: ' . $db->error;
        header("Location: login_guru.php");
        exit();
    }

    $stmt->bind_param('s', $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($guru = $result->fetch_assoc()) {
        $stored = $guru['password'];
        $ok = false;

        // 1) Try password_verify (bcrypt/argon2)
        if (!empty($password) && password_verify($password, $stored)) {
            $ok = true;
        }

        // 2) Fallback: compare md5 (if old system used md5)
        if (!$ok && md5($password) === $stored) {
            $ok = true;
        }

        // 3) Fallback: plaintext compare (not recommended)
        if (!$ok && $password === $stored) {
            $ok = true;
        }

        if ($ok) {
            $_SESSION['user_id'] = $guru['id'];
            $_SESSION['nama_guru'] = $guru['nama'];
            $_SESSION['level'] = 'guru';
            header("Location: dashboard_guru.php");
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = ($by === 'nip') ? "NIP tidak ditemukan!" : "Nama guru tidak ditemukan!";
    }

    header("Location: login_guru.php");
    exit();
}
?>
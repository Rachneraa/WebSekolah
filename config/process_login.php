<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    // 1. Cek login admin/guru di tabel users
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'];

            if ($user['level'] == 'admin') {
                header("Location: ../backend/admin.php");
                exit();
            } elseif ($user['level'] == 'guru') {
                header("Location: ../guru/guru.php");
                exit();
            }
        }
    }

    // 2. Jika tidak ditemukan di users, cek ke tabel siswa
    $query = "SELECT * FROM siswa WHERE username = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($siswa = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $siswa['password'])) {
            $_SESSION['user_id'] = $siswa['siswa_id'];
            $_SESSION['level'] = 'siswa';
            $_SESSION['kelas_id'] = $siswa['kelas_id'];

            // Update login time and status in siswa table
            $update = "UPDATE siswa SET login_time = NOW(), login_status = 'success' WHERE siswa_id = ?";
            $stmt2 = mysqli_prepare($db, $update);
            mysqli_stmt_bind_param($stmt2, "i", $siswa['siswa_id']);
            mysqli_stmt_execute($stmt2);

            // Insert into login history
            $insert = "INSERT INTO login_history (siswa_id, login_time, status) VALUES (?, NOW(), 'Hadir')";
            $stmt3 = mysqli_prepare($db, $insert);
            mysqli_stmt_bind_param($stmt3, "i", $siswa['siswa_id']);
            $stmt3->execute();

            header("Location: ../siswa/dashboard.php");
            exit();
        }
    }

    // 3. Jika tidak ditemukan di users/siswa, cek ke tabel guru
    $query = "SELECT * FROM guru WHERE nama = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($guru = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $guru['password'])) {
            $_SESSION['user_id'] = $guru['id'];
            $_SESSION['nama_guru'] = $guru['nama'];
            $_SESSION['level'] = 'guru';
            header("Location: ../guru/guru.php");
            exit();
        } else {
            $_SESSION['error'] = "Password guru salah!";
        }
    } else {
        $_SESSION['error'] = "Nama guru tidak ditemukan!";
    }

    header("Location: ../index.php");
    exit();
}
mysqli_close($db);
?>
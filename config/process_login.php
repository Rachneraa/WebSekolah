<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    // Check student login
    $query = "SELECT * FROM siswa WHERE username = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $siswa = mysqli_fetch_assoc($result);
        if (password_verify($password, $siswa['password'])) {
            // Set session
            $_SESSION['user_id'] = $siswa['id'];
            $_SESSION['level'] = 'siswa';
            $_SESSION['kelas_id'] = $siswa['kelas_id'];

            // Update login time and status in siswa table
            $update = "UPDATE siswa SET login_time = NOW(), login_status = 'success' WHERE id = ?";
            $stmt = mysqli_prepare($db, $update);
            mysqli_stmt_bind_param($stmt, "i", $siswa['id']);
            mysqli_stmt_execute($stmt);

            // Insert into login history with correct column name (id_siswa)
            $insert = "INSERT INTO login_history (id_siswa, login_time, status) VALUES (?, NOW(), 'Hadir')";
            $stmt = mysqli_prepare($db, $insert);
            mysqli_stmt_bind_param($stmt, "i", $siswa['id']);
            $stmt->execute();

            header("Location: ../siswa/dashboard.php");
            exit();
        }
    }

    // Login failed
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../index.php");
    exit();
}
mysqli_close($db);
?>
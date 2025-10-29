<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'koneksi.php'; // pastikan path benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];
    $found = false;

    // =====================================================
    // 1. CEK LOGIN ADMIN ATAU GURU (dari tabel users)
    // =====================================================
    $query_user = "SELECT * FROM users WHERE username = ?";
    if ($stmt_user = mysqli_prepare($db, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $username);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);

        if ($user = mysqli_fetch_assoc($result_user)) {
            $found = true;

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['level'] = $user['level'];

                // Redirect berdasarkan level
                if ($user['level'] == 'admin') {
                    header("Location: ../backend/admin.php");
                    exit();
                } elseif ($user['level'] == 'guru') {
                    header("Location: ../guru/guru.php");
                    exit();
                } else {
                    // Jika level tidak dikenal
                    $_SESSION['error'] = "Level user tidak dikenali.";
                    header("Location: ../index.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Password salah!";
                header("Location: ../index.php");
                exit();
            }
        }
        mysqli_stmt_close($stmt_user);
    }

    // =====================================================
    // 2. CEK LOGIN SISWA (dari tabel siswa)
    // =====================================================
    if ($found === false) {
        $query_siswa = "SELECT * FROM siswa WHERE username = ?";
        if ($stmt_siswa = mysqli_prepare($db, $query_siswa)) {
            mysqli_stmt_bind_param($stmt_siswa, "s", $username);
            mysqli_stmt_execute($stmt_siswa);
            $result_siswa = mysqli_stmt_get_result($stmt_siswa);

            if ($siswa = mysqli_fetch_assoc($result_siswa)) {
                $found = true;

                if (password_verify($password, $siswa['password'])) {
                    $_SESSION['user_id'] = $siswa['siswa_id'];
                    $_SESSION['nama_siswa'] = $siswa['nama'];
                    $_SESSION['level'] = 'siswa';

                    // Update login time
                    $update_siswa = "UPDATE siswa SET login_time = NOW(), login_status = 'success' WHERE siswa_id = ?";
                    if ($stmt_update = mysqli_prepare($db, $update_siswa)) {
                        mysqli_stmt_bind_param($stmt_update, "i", $siswa['siswa_id']);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);
                    }

                    // Tambah riwayat login
                    $insert_history = "INSERT INTO login_history (siswa_id, login_time, status) VALUES (?, NOW(), 'Hadir')";
                    if ($stmt_history = mysqli_prepare($db, $insert_history)) {
                        mysqli_stmt_bind_param($stmt_history, "i", $siswa['siswa_id']);
                        mysqli_stmt_execute($stmt_history);
                        mysqli_stmt_close($stmt_history);
                    }

                    header("Location: ../siswa/dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Password siswa salah!";
                    header("Location: ../index.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt_siswa);
        }
    }

    // =====================================================
    // 3. JIKA TIDAK DITEMUKAN DI SEMUA TABEL
    // =====================================================
    if ($found === false) {
        $_SESSION['error'] = "Username atau Password salah.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

ob_end_flush();
?>

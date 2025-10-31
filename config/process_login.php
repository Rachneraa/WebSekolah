<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'koneksi.php'; // pastikan path benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi Sederhana (tambahan): Cek input kosong di sisi server
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['error'] = "Username dan Password tidak boleh kosong.";
        header("Location: ../index.php"); // Sesuaikan path jika perlu
        exit();
    }

    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];
    $found = false;

    // =====================================================
    // 1. CEK LOGIN ADMIN ATAU GURU (dari tabel users)
    // =====================================================
    $query_user = "SELECT id, username, password, level, guru_id FROM users WHERE username = ?";

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

                if ($user['level'] == 'guru') {
                    if (is_null($user['guru_id'])) {
                        $_SESSION['error'] = "Login Gagal! Akun guru Anda belum terhubung ke biodata. Harap hubungi Admin.";
                        header("Location: ../index.php"); // Sesuaikan path jika perlu
                        exit();
                    }
                    $_SESSION['guru_id'] = $user['guru_id'];
                }

                // ===============================================
                // == TAMBAHAN UNTUK "INGAT SAYA" ==
                // ===============================================
                if (isset($_POST['rememberMe'])) {
                    // Jika dicentang, perpanjang umur session cookie ke 30 hari
                    $cookie_lifetime = time() + (86400 * 30); // 86400 = 1 hari
                    setcookie(session_name(), session_id(), $cookie_lifetime, "/");
                }
                // ===============================================
                // == AKHIR TAMBAHAN ==
                // ===============================================

                // Redirect berdasarkan level
                if ($user['level'] == 'admin') {
                    header("Location: ../backend/admin.php"); // Sesuaikan path jika perlu
                    exit();
                } elseif ($user['level'] == 'guru') {
                    header("Location: ../guru/guru.php"); // Sesuaikan path jika perlu
                    exit();
                } else {
                    $_SESSION['error'] = "Level user tidak dikenali.";
                    header("Location: ../index.php"); // Sesuaikan path jika perlu
                    exit();
                }
            } else {
                $_SESSION['error'] = "Password salah!";
                header("Location: ../index.php"); // Sesuaikan path jika perlu
                exit();
            }
        }
        mysqli_stmt_close($stmt_user);
    }

    // =====================================================
    // 1b. FALLBACK: CEK LANGSUNG DI TABEL 'guru' BERDASARKAN NIP
    // =====================================================
    if ($found === false) {
        // If username looks like NIP (numeric or contains digits), try guru.nip
        $query_guru = "SELECT id, nama, nip, password FROM guru WHERE nip = ? LIMIT 1";
        if ($stmt_guru = mysqli_prepare($db, $query_guru)) {
            mysqli_stmt_bind_param($stmt_guru, "s", $username);
            mysqli_stmt_execute($stmt_guru);
            $result_guru = mysqli_stmt_get_result($stmt_guru);

            if ($guru = mysqli_fetch_assoc($result_guru)) {
                $found = true;
                // verify password (support bcrypt, md5 fallback, plain fallback)
                $stored = $guru['password'];
                $ok = false;
                if (!empty($password) && password_verify($password, $stored)) {
                    $ok = true;
                }
                if (!$ok && md5($password) === $stored) {
                    $ok = true;
                }
                if (!$ok && $password === $stored) {
                    $ok = true;
                }

                if ($ok) {
                    // Set session similar to users->guru case
                    $_SESSION['user_id'] = $guru['id'];
                    $_SESSION['username'] = $guru['nip'];
                    $_SESSION['level'] = 'guru';
                    $_SESSION['guru_id'] = $guru['id'];

                    if (isset($_POST['rememberMe'])) {
                        $cookie_lifetime = time() + (86400 * 30);
                        setcookie(session_name(), session_id(), $cookie_lifetime, "/");
                    }

                    header("Location: ../guru/guru.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Password salah!";
                    header("Location: ../index.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt_guru);
        }
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
                    $_SESSION['kelas_id'] = $siswa['kelas_id'];

                    // ===============================================
                    // == TAMBAHAN UNTUK "INGAT SAYA" ==
                    // ===============================================
                    if (isset($_POST['rememberMe'])) {
                        // Jika dicentang, perpanjang umur session cookie ke 30 hari
                        $cookie_lifetime = time() + (86400 * 30); // 86400 = 1 hari
                        setcookie(session_name(), session_id(), $cookie_lifetime, "/");
                    }
                    // ===============================================
                    // == AKHIR TAMBAHAN ==
                    // ===============================================

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

                    header("Location: ../siswa/dashboard.php"); // Sesuaikan path jika perlu
                    exit();
                } else {
                    $_SESSION['error'] = "Password siswa salah!";
                    header("Location: ../index.php"); // Sesuaikan path jika perlu
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
        header("Location: ../index.php"); // Sesuaikan path jika perlu
        exit();
    }
} else {
    header("Location: ../index.php"); // Sesuaikan path jika perlu
    exit();
}

ob_end_flush();
?>
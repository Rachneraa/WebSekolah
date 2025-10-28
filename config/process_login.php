<?php
session_start();
require_once 'koneksi.php'; // Pastikan nama file ini benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];
    $found = false;

    // 1. Cek login Admin di tabel users
    $query_user = "SELECT * FROM users WHERE username = ?";
    if ($stmt_user = mysqli_prepare($db, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $username);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);

        if ($user = mysqli_fetch_assoc($result_user)) {
            $found = true;
            if (password_verify($password, $user['password'])) {
                // Login Admin Berhasil
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['level'] = $user['level'];

                if ($user['level'] == 'admin') {
                    header("Location: ../backend/admin.php");
                    exit();
                }
                // Tambahkan redirect untuk level lain di tabel users jika ada
            } else {
                // Password Admin Salah
                $_SESSION['error'] = "Password Admin salah!";
                header("Location: ../index.php");
                exit();
            }
        }
        mysqli_stmt_close($stmt_user);
    }

    // 2. Cek login Siswa di tabel siswa (Hanya jika belum ditemukan di users)
    if ($found === false) {
        $query_siswa = "SELECT * FROM siswa WHERE username = ?";
         if ($stmt_siswa = mysqli_prepare($db, $query_siswa)) {
            mysqli_stmt_bind_param($stmt_siswa, "s", $username);
            mysqli_stmt_execute($stmt_siswa);
            $result_siswa = mysqli_stmt_get_result($stmt_siswa);

            if ($siswa = mysqli_fetch_assoc($result_siswa)) {
                $found = true;
                // Asumsikan password siswa di-hash
                if (password_verify($password, $siswa['password'])) {
                    // Login Siswa Berhasil
                    $_SESSION['user_id'] = $siswa['siswa_id'];
                    $_SESSION['nama_siswa'] = $siswa['nama']; // Atau kolom nama yang sesuai
                    $_SESSION['level'] = 'siswa';

                    // Update login time & status (jika perlu)
                    $update_siswa = "UPDATE siswa SET login_time = NOW(), login_status = 'success' WHERE siswa_id = ?";
                    if($stmt_update = mysqli_prepare($db, $update_siswa)){
                        mysqli_stmt_bind_param($stmt_update, "i", $siswa['siswa_id']);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);
                    }


                    // Insert into login history (Pastikan kolom ID di login_history AUTO_INCREMENT)
                     $insert_history = "INSERT INTO login_history (siswa_id, login_time, status) VALUES (?, NOW(), 'Hadir')";
                     if($stmt_history = mysqli_prepare($db, $insert_history)){
                         mysqli_stmt_bind_param($stmt_history, "i", $siswa['siswa_id']);
                         mysqli_stmt_execute($stmt_history);
                         mysqli_stmt_close($stmt_history);
                     }


                    header("Location: ../siswa/dashboard.php"); // Sesuaikan path dashboard siswa
                    exit();
                } else {
                    // Password Siswa Salah
                    $_SESSION['error'] = "Password Siswa salah!";
                    header("Location: ../index.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt_siswa);
        }
    }

    // 3. Cek login Guru di tabel guru (Hanya jika belum ditemukan)
    if ($found === false) {
        // Asumsi username guru ada di kolom 'nama' atau 'username_guru'
        $query_guru = "SELECT * FROM guru WHERE nama = ?"; // Ganti 'nama' jika kolomnya beda
        if($stmt_guru = mysqli_prepare($db, $query_guru)){
            mysqli_stmt_bind_param($stmt_guru, "s", $username);
            mysqli_stmt_execute($stmt_guru);
            $result_guru = mysqli_stmt_get_result($stmt_guru);

            if ($guru = mysqli_fetch_assoc($result_guru)) {
                $found = true;
                 // Asumsikan password guru di-hash
                if (password_verify($password, $guru['password'])) {
                    // Login Guru Berhasil
                    $_SESSION['user_id'] = $guru['id']; // Atau kolom ID guru yang sesuai
                    $_SESSION['nama_guru'] = $guru['nama'];
                    $_SESSION['level'] = 'guru';
                    header("Location: ../guru/guru.php"); // Sesuaikan path dashboard guru
                    exit();
                } else {
                    // Password Guru Salah
                    $_SESSION['error'] = "Password Guru salah!";
                    header("Location: ../index.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt_guru);
        }
    }

    // --- BAGIAN CEK PENDAFTAR SUDAH DIHAPUS DARI SINI ---

    // 4. PENANGANAN KEGAGALAN AKHIR (Jika BUKAN Admin, Siswa, atau Guru)
    if ($found === false) {
        $_SESSION['error'] = "Username atau Password salah."; // Pesan error umum
        header("Location: ../index.php");
        exit();
    }
} else {
    // Jika diakses tanpa submit form POST
    header("Location: ../index.php");
    exit();
}
?>
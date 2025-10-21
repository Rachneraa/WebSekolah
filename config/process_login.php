<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level'];

            // Set cookie jika remember me dicentang
            if (isset($_POST['remember'])) {
                setcookie('user_login', $row['username'], time() + (86400 * 30), "/");
            }

            // Redirect berdasarkan level user
            if ($row['level'] == 'admin') {
                header("Location: ../backend/admin.php");
            } else {
                header("Location: ../backend/guru.php");
            }
            exit();
        } else {
            header("Location: ../index.php?error=invalid");
        }
    } else {
        header("Location: ../index.php?error=invalid");
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: ../index.php");
}
mysqli_close($db);
?>
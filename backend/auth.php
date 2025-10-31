<?php

session_start();
require_once "../config/koneksi.php";

// Login handler
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($db, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level'];
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Username atau password salah']);
}

// Logout handler
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success']);
}
?>
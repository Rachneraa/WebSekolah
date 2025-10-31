<?php

require_once "config/koneksi.php";

if ($db) {
    echo "Koneksi database berhasil<br>";

    // Test query users
    $query = "SELECT * FROM users";
    $result = mysqli_query($db, $query);

    if ($result) {
        echo "Data users:<br>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "Username: " . $row['username'] . " Level: " . $row['level'] . "<br>";
        }
    } else {
        echo "Error query users: " . mysqli_error($db);
    }
} else {
    echo "Koneksi database gagal: " . mysqli_connect_error();
}
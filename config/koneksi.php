<?php

$server = "localhost";
$user = "root";
$password = "";
$nama_database = "sekolah_db";

$db = mysqli_connect($server, $user, $password, $nama_database);
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set UTF-8 encoding
mysqli_set_charset($db, "utf8mb4");

// Enable error reporting for development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
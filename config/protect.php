<?php

if (!isset($_SESSION)) {
    session_start();
}

function check_access($allowed_levels)
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
        header("Location: ../index.php?error=login");
        exit();
    }

    if (!in_array($_SESSION['level'], $allowed_levels)) {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }
}
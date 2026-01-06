<?php
//includes/auth_check.php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['role'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
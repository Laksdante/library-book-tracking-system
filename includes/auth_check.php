<?php
//includes/auth_check.php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<?php
// db_connect.php

$host = "localhost";
$user = "root";      // Default for XAMPP
$pass = "";          // Leave it empty unless you've set a password
$db   = "librarytest_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: change_password.php");
    exit();
}

$id = $_SESSION['staff_id'];
$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    header("Location: change_password.php?error=Passwords do not match");
    exit();
}

$result = $conn->query("SELECT password FROM staff WHERE id = $id");
$user = $result->fetch_assoc();

if (!password_verify($current, $user['password'])) {
    header("Location: change_password.php?error=Current password is incorrect");
    exit();
}

$hashed = password_hash($new, PASSWORD_DEFAULT);
$conn->query("UPDATE staff SET password = '$hashed' WHERE id = $id");

header("Location: change_password.php?success=1");
exit();

<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');

// Admin only
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_staff.php");
    exit();
}

$id = intval($_GET['id']);

// Prevent disabling yourself
if ($id === $_SESSION['staff_id']) {
    header("Location: view_staff.php?error=self");
    exit();
}

$staff = $conn->query("SELECT status FROM staff WHERE id = $id")->fetch_assoc();

$newStatus = ($staff['status'] === 'active') ? 'disabled' : 'active';

$conn->query("UPDATE staff SET status = '$newStatus' WHERE id = $id");

header("Location: view_staff.php");
exit();

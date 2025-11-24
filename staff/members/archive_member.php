<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');

if (!isset($_GET['id'])) {
    header("Location: view_members.php");
    exit();
}

$id = intval($_GET['id']);

$conn->query("UPDATE members SET status='archived' WHERE id=$id");

echo "<script>alert('✔ Member archived successfully'); 
window.location='view_members.php';</script>";

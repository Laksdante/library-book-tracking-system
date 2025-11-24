<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');

$id = intval($_GET['id']);

$conn->query(
    "UPDATE fines SET status='paid' WHERE id=$id"
);

echo "<script>alert('Fine marked as paid.') window.location='view_fines.php';</script>";

?>
<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');

$id = intval($_GET['id']);

$conn->query("DELETE FROM fines WHERE id=$id");

echo "<script>alert('Fine deleted.');
window.location='view_fines.php';</script>";


?>
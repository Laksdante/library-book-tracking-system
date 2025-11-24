<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');
include('../../includes/crsf.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    header("Location: view_books.php");
    exit();
}

if(!verify_csrf_token($_POST['csrf_token'])){
    die("Invalid CSRF token.");
}

$book_id = intval($_POST['id']);

// safety check: Prevents deleting books that are currently borrowed
$check = $conn->query("SELECT * FROM borrow_records WHERE book_id - $book_id AND returned_at IS NULL");

if($check->num_rows > 0){
    echo "<script>alert('⚠ Cannot delete book. It is currently borrowed.'); window.location.href='view_books.php';</script>";
    exit();
}

// Proceed to delete
$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);

if($stmt->execute()){
      echo "<script>alert('✔ Book deleted successfully.'); 
          window.location='view_books.php';</script>";
} else {
    echo "<script>alert('❌ Error deleting book.'); 
          window.location='view_books.php';</script>";
}

?>
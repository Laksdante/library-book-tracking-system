<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/csrf.php');


if(!verify_csrf_token($_POST['csrf_token'])){
    die("Invalid CSRF token.");
}

$member_id = intval($_POST['member_id']);
$book_id = intval($_POST['book_id']);
$due_at = $_POST['due_at'];
$borrowed_at = date('Y-m-d H:i:s');

// Check book availability
$book = $conn->query("SELECT copies_available FROM books WHERE id = $book_id")->fetch_assoc();

if ($book['copies_available'] <= 0) {
    echo "<script>alert('❌ This book is no longer available.'); window.location='borrow_book.php';</script>";
    exit();
}

// Insert borrow record
$stmt = $conn->prepare("
    INSERT INTO borrow_records (member_id, book_id, borrowed_at, due_at)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiss", $member_id, $book_id, $borrowed_at, $due_at);

if ($stmt->execute()) {
    // Reduce available copies
    $conn->query("UPDATE books SET copies_available = copies_available - 1 WHERE id = $book_id");

    echo "<script>alert('✔ Book borrowed successfully!'); window.location='view_borrowed.php';</script>";
} else {
    echo "<script>alert('❌ Error borrowing book.'); window.location='borrow_book.php';</script>";
}
?>
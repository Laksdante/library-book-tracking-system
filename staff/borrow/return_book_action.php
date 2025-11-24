<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/csrf.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_borrowed.php");
    exit();
}

if (!verify_csrf_token($_POST['csrf_token'])) {
    die("Invalid CSRF token.");
}

$borrow_id = intval($_POST['borrow_id']);
$return_date = date("Y-m-d");

// Fetch borrow details
$fetch_query = "
SELECT br.*, b.id AS book_id, b.copies_available
FROM borrow_records br
JOIN books b ON br.book_id = b.id
WHERE br.id = $borrow_id
";

$fetch = $conn->query($fetch_query)->fetch_assoc();

if (!$fetch) {
    die("Borrow record not found.");
}

$due        = $fetch['due_at'];
$book_id    = $fetch['book_id'];
$member_id  = $fetch['member_id'];

// Calculate fine logic is here 
$daysLate = max(0, floor((strtotime($return_date) - strtotime($due)) / 86400));
$fine_amount = $daysLate * 500;

// UPDATE borrow record
$conn->query("
    UPDATE borrow_records
    SET returned_at = '$return_date'
    WHERE id = $borrow_id
");

// ADD fine if needed
if ($fine_amount > 0) {
    $stmt = $conn->prepare("
        INSERT INTO fines (member_id, book_id, amount, borrow_id, status)
        VALUES (?, ?, ?, ?, 'unpaid')
    ");
    $stmt->bind_param("iiii", $member_id, $book_id, $fine_amount, $borrow_id);
    $stmt->execute();
}

// Increase book copies
$conn->query("
    UPDATE books
    SET copies_available = copies_available + 1
    WHERE id = $book_id
");

echo "<script>
alert('Book returned successfully!');
window.location='view_borrowed.php';
</script>";
?>

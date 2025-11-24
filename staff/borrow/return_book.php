<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');  // IMPORTANT
include('../../includes/csrf.php');

if (!isset($_GET['id'])) {
    header("Location: view_borrowed.php");
    exit();
}

$borrow_id = intval($_GET['id']);
$token = generate_csrf_token();

// Fetch borrow data safely
$query = "
SELECT br.*, b.title, m.full_name 
FROM borrow_records br
JOIN books b ON br.book_id = b.id
JOIN members m ON br.member_id = m.id
WHERE br.id = $borrow_id
";

$result = $conn->query($query);
$data = $result ? $result->fetch_assoc() : null;

// If record missing
if (!$data) {
    echo "<script>alert('Borrow record not found.'); window.location='view_borrowed.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <div class="form-container">

        <h2>Return Book</h2>

        <p><strong>Member:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
        <p><strong>Book:</strong> <?= htmlspecialchars($data['title']); ?></p>
        <p><strong>Borrowed At:</strong> <?= $data['borrowed_at']; ?></p>
        <p><strong>Due Date:</strong> <?= $data['due_at']; ?></p>

        <form method="POST" action="return_book_action.php" style="margin-top:15px;">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <input type="hidden" name="borrow_id" value="<?= $borrow_id ?>">
            <button type="submit">Confirm Return</button>
        </form>

        <a href="view_borrowed.php" class="back-link">⬅️ Back</a>

    </div>

</div>

</body>
</html>

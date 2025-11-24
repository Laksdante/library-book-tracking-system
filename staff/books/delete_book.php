<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/csrf.php');
include('../../includes/path_helper.php');

if (!isset($_GET['id'])) {
    header("Location: view_books.php");
    exit();
}

$book_id = intval($_GET['id']);
$token = generate_csrf_token();

// Get book data
$book = $conn->query("SELECT * FROM books WHERE id=$book_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Book</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <div class="form-container">
        <h2>Delete Book</h2>

        <div class="warning">
            ⚠️ Are you sure you want to delete:
            <strong><?= $book['title'] ?></strong>?
        </div>

        <form action="delete_book_action.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <input type="hidden" name="id" value="<?= $book_id ?>">
            <button class="delete-btn" type="submit">Yes Delete</button>
        </form>

        <a href="view_books.php" class="back-link">← Cancel</a>
    </div>

</div>

</body>
</html>

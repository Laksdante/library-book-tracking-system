<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');
include('../../includes/csrf.php');

$message = "";
$token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {

        $isbn = trim($_POST['isbn']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $copies_total = intval($_POST['copies_total']);

        $stmt = $conn->prepare("
            INSERT INTO books (isbn, title, author, category, copies_total, copies_available)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssii", $isbn, $title, $author, $category, $copies_total, $copies_total);

        if ($stmt->execute()) {
            $message = "<div class='success'>✔ Book added successfully.</div>";
        } else {
            $message = "<div class='error'>❌ Error adding book.</div>";
        }

    } else {
        $message = "<div class='error'>⚠ Invalid CSRF Token.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <h2>Add New Book</h2>
    <?= $message ?>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <label>ISBN</label>
            <input type="text" name="isbn" required>

            <label>Title</label>
            <input type="text" name="title" required>

            <label>Author</label>
            <input type="text" name="author" required>

            <label>Category</label>
            <input type="text" name="category" required>

            <label>Total Copies</label>
            <input type="number" name="copies_total" min="1" required>

            <button type="submit">Add Book</button>
        </form>

        <a href="view_books.php" class="back-link">← Back to Books</a>
    </div>

</div>

</body>
</html>

<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');
include('../../includes/csrf.php');

$message = "";

if (!isset($_GET['id'])) {
    header("Location: view_books.php");
    exit();
}

$book_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

$token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {

        $isbn = trim($_POST['isbn']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $copies_total = intval($_POST['copies_total']);
        $copies_available = $book['copies_available'] + ($copies_total - $book['copies_total']);

        if ($copies_available < 0) $copies_available = 0;

        $update = $conn->prepare("
            UPDATE books 
            SET isbn=?, title=?, author=?, category=?, copies_total=?, copies_available=?
            WHERE id=?
        ");
        $update->bind_param("ssssiii", $isbn, $title, $author, $category, $copies_total, $copies_available, $book_id);

        if ($update->execute()) {
            $message = "<div class='success'>✔ Book updated successfully.</div>";
        } else {
            $message = "<div class='error'>❌ Error updating book.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">
    
    <h2>Edit Book</h2>
    <?= $message ?>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <label>ISBN</label>
            <input type="text" name="isbn" value="<?= $book['isbn'] ?>">

            <label>Title</label>
            <input type="text" name="title" value="<?= $book['title'] ?>">

            <label>Author</label>
            <input type="text" name="author" value="<?= $book['author'] ?>">

            <label>Category</label>
            <input type="text" name="category" value="<?= $book['category'] ?>">

            <label>Total Copies</label>
            <input type="number" name="copies_total" min="1" value="<?= $book['copies_total'] ?>">

            <button type="submit">Update Book</button>
        </form>

        <a href="view_books.php" class="back-link">← Back to Books</a>
    </div>

</div>

</body>
</html>

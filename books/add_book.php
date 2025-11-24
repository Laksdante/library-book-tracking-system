<?php
include('../includes/auth_check.php');
include('../db_connect.php');
include('../includes/path_helper.php');
include('../includes/crsf.php');

$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (verify_csrf_token($_POST['csrf_token'])) {
        $isbn = trim($_POST['isbn']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $copies_total = intval($_POST['copies_total']);
        $copies_available = $copies_total;

        //validation
        if(empty($isbn) || empty($title) ||  empty($author) || empty($category) || $copies_total <= 0){
            $message = "<div class='error'> Please fill in all fields correctly. </div>";
        } else{
            // insert into database 
            $stmt = $conn-> prepare("INSERT INTO books (isbn, title, author, category, copies_total, copies_available) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $isbn, $title, $author, $category, $copies_total, $copies_available);
            
            if($stmt->execute()){
                $message = "<div class='success'> Book added successfully. </div>";
            } else{
                $message = "<div class='error'> Error adding book: " . htmlspecialchars($stmt->error) . " </div>";
            }
            $stmt->close();
        }
    } else{
        $message = "<div class='error'> Invalid CSRF token. </div>";
    }
}

$token = generate_csrf_token();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book | Library System</title>
     <link rel="stylesheet" href="<?php echo base_url('assets/css/forms.css'); ?>">
</head>
<body>
    <div class="form-container">
        <h2>Add New Book</h2>
        <?= $message ?>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= $token?>">

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>

            <label for="title">Title:</label>
            <input type="text" name="title" required>

            <label for="author">Author:</label>
            <input type="text" name="author" required>

            <label for="category">Category:</label>
            <input type="text" name="category" required>

            <label for="copies_total">Total Copies:</label>
            <input type="number" name="copies_total" min="1" required>

            <button type="submit">Add Book</button>
        </form>

        <a href="view_books.php" class="back-link">Back to Book List</a>
    </div>
</body>
</html>
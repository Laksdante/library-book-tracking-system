<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');
include('../../includes/csrf.php');

$token = generate_csrf_token();

// Fetch Data
$members = $conn->query("SELECT id, full_name, reg_no FROM members WHERE status='active'");
$books = $conn->query("SELECT id, title, author, copies_available FROM books WHERE copies_available > 0 ORDER BY title ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow Book</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css'); ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <div class="page-header">
        <h2>Borrow Book</h2>
    </div>

    <div class="form-container">
        <form method="POST" action="borrow_book_action.php">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <label>Select Member</label>
            <select name="member_id" required>
                <option value="">-- Choose Member --</option>
                <?php while($m = $members->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>">
                    <?= $m['full_name'] ?> (<?= $m['reg_no'] ?>)
                </option>
                <?php endwhile; ?>
            </select>

            <label>Select Book</label>
            <select name="book_id" required>
                <option value="">-- Choose Book --</option>
                <?php while($b = $books->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>">
                    <?= $b['title'] ?> - <?= $b['author'] ?> 
                    (Available: <?= $b['copies_available'] ?>)
                </option>
                <?php endwhile; ?>
            </select>

            <label>Due Date</label>
            <input type="date" name="due_at" 
                   value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>

            <button type="submit">Borrow Book</button>
        </form>
    </div>

</div>

</body>
</html>

<?php
include('../includes/member_auth.php'); // Protect this page
include('../db_connect.php');
include('../includes/path_helper.php');

$member_id = $_SESSION['member_id'];
$username = $_SESSION['username'];

// Fetch member details
$member_query = $conn->query("SELECT full_name, email FROM members WHERE id = $member_id");
$member = $member_query->fetch_assoc();

// Fetch borrowed books for this member
$borrow_query = "
SELECT b.title, b.author, br.borrowed_at, br.due_at, br.returned_at,
       IFNULL(f.amount, 0) AS fine
FROM borrow_records br
JOIN books b ON br.book_id = b.id
LEFT JOIN fines f ON f.borrow_id = br.id
WHERE br.member_id = $member_id
ORDER BY br.borrowed_at DESC
";
$borrowed_books = $conn->query($borrow_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard | Cavendish Library</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/member_dashboard.css'); ?>">
</head>
<body>

<!---sidebar layout -->
<div class="sidebar">
    <h2>Member Panel</h2>
    <ul>
        <li><a class="active" href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="book_list.php">📚 Browse Books</a></li>
        <li><a href="my_borrowed.php">📖 My Borrowed Books</a></li>
        <li><a href="my_fines.php">💰 My Fines</a></li>
    </ul>
</div>

<!--Top Navigation Bar -->
<div class="topnav">
    <span class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</span>
    <h1>Cavendish Library Dashboard</h1>

    <div class="user-right">
        <span class="user-label">👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Main content-->

<div class="main-content">
    <div class="cards">
        <div class="card blue">
            <h3>Name</h3>
            <p><?= $member['full_name'] ?></p>
        </div>
        <div class="card sky">
            <h3>Email</h3>
            <p><?= $member['email'] ?></p>
        </div>
    </div>
    <h2 class="section-heading">Your borrowed Books</h2>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Borrowed At</th>
                <th>Due Date</th>
                <th>Returned</th>
                <th>Fine</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($borrowed_books->num_rows == 0): ?>
                <tr><td colspan="5" class="empty">No borrowed books yet.</td></tr>
            <?php endif; ?>

            <?php while ($row = $borrowed_books->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['borrowed_at'] ?></td>
                    <td><?= $row['due_at'] ?></td>
                    <td><?= $row['returned_at'] ? '✅ Yes' : '❌ No' ?></td>
                    <td><?= number_format($row['fine']) ?> UGX</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

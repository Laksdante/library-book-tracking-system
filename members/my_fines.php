<?php 
include('../includes/member_auth.php');
include('../db_connect.php');
include('../includes/path_helper.php');
// protecting user sessions
$member_id = $_SESSION['member_id'];
$username = $_SESSION['username'];

// Fetch fines for this member
$query = "
SELECT b.title, br.borrowed_at, br.due_at, br.returned_at, f.amount
FROM fines f
JOIN borrow_records br ON f.borrow_id = br.id
JOIN books b ON br.book_id = b.id
WHERE br.member_id = $member_id
ORDER BY f.issued_at DESC
";
$fines = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Fines | Member</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/member_dashboard.css'); ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Member Panel</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="book_list.php">📚 Browse Books</a></li>
        <li><a href="my_borrowed.php">📖 My Borrowed Books</a></li>
        <li><a class="active" href="my_fines.php">💰 My Fines</a></li>
    </ul>
</div>

<!-- Top Navigation Bar -->
<div class="topnav">
    <h1>💰 My Fines</h1>
    <div class="topnav">
        <span class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</span>
        <h1>Cavendish Library Dashboard</h1>

        <div class="user-right">
        <span class="user-label">👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">

    <table class="styled-table">
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Borrowed At</th>
                <th>Due Date</th>
                <th>Returned At</th>
                <th>Fine (UGX)</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($fines->num_rows === 0): ?>
            <tr><td colspan="5" class="empty">You have no fines.</td></tr>
            <?php endif; ?>

            <?php while ($row = $fines->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= $row['borrowed_at']; ?></td>
                <td><?= $row['due_at']; ?></td>
                <td><?= $row['returned_at'] ?: "❌ Not Returned"; ?></td>
                <td><strong><?= number_format($row['amount']); ?></strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
<?php
include('../includes/member_auth.php');
include('../db_connect.php');
include('../includes/path_helper.php');

$member_id = $_SESSION['member_id'];

// Fetch borrowed books
$query = "
SELECT b.title, b.author, br.borrowed_at, br.due_at, br.returned_at,
       IFNULL(f.amount, 0) AS fine
FROM borrow_records br
JOIN books b ON br.book_id = b.id
LEFT JOIN fines f ON f.borrow_id = br.id
WHERE br.member_id = $member_id
ORDER BY br.borrowed_at DESC
";
$records = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books | Member</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/member_dashboard.css'); ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Member Panel</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="book_list.php">📚 Browse Books</a></li>
        <li><a class="active" href="my_borrowed.php">📖 My Borrowed Books</a></li>
        <li><a href="my_fines.php">💰 My Fines</a></li>
    </ul>
</div>

<!-- Top Navigation Bar -->
<div class="topnav">
    <span class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</span>

    <h1>📖 My Borrowed Books</h1>

    <div class="user-right">
        <span class="user-label">👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Borrowed At</th>
                    <th>Due Date</th>
                    <th>Returned</th>
                    <th>Fine (UGX)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="empty">No borrowed books found.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($row = $records->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= $row['borrowed_at']; ?></td>
                        <td><?= $row['due_at']; ?></td>
                        <td>
                            <?= $row['returned_at']
                                ? "<span style='color:green;font-weight:bold;'>✔ Returned</span>"
                                : "<span style='color:red;font-weight:bold;'>❌ Not Returned</span>"; ?>
                        </td>
                        <td><?= number_format($row['fine']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

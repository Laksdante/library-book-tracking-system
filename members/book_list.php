<?php
include('../includes/member_auth.php');
include('../db_connect.php');
include('../includes/path_helper.php');

// Pagination settings following the question
$limit = 10; // books per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// SEARCH LOGIC
$search = "";
$search_sql = "";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_sql = "WHERE title LIKE '%$search%' 
                   OR author LIKE '%$search%' 
                   OR category LIKE '%$search%'";
}

// Count total books for pagination
$countRes = $conn->query("SELECT COUNT(*) AS total FROM books $search_sql");
$totalBooks = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $limit);

// Fetch paginated books
$query = "SELECT * FROM books $search_sql ORDER BY title ASC LIMIT $start, $limit";
$books = $conn->query($query);

$username = $_SESSION['username'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Books | Member Portal</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/member_dashboard.css'); ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Member Panel</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a class="active" href="book_list.php">📚 Browse Books</a></li>
        <li><a href="my_borrowed.php">📖 My Borrowed Books</a></li>
        <li><a href="my_fines.php">💰 My Fines</a></li>
    </ul>
</div>

<!-- Top Navigation Bar -->
<div class="topnav">
    <h1>📚 Available Books</h1>
    <div class="user-right">
        <span class="user-label">👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by title, author, category..."
               value="<?= htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Available Copies</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($books->num_rows == 0): ?>
                <tr><td colspan="5" class="empty">No books found.</td></tr>
            <?php endif; ?>

            <?php while ($row = $books->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['isbn']); ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['author']); ?></td>
                    <td><?= htmlspecialchars($row['category']); ?></td>
                    <td>
                        <?php if ($row['copies_available'] > 0): ?>
                            <span style="color: green; font-weight:bold;">
                                <?= $row['copies_available']; ?>
                            </span>
                        <?php else: ?>
                            <span style="color: red; font-weight:bold;">
                                Unavailable
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&search=<?= htmlspecialchars($search) ?>">« Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($i == $page ? 'active-page' : '') ?>"
               href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&search=<?= htmlspecialchars($search) ?>">Next »</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

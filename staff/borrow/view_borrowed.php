<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = "";
$search_sql = "";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_sql = "WHERE b.title LIKE '%$search%' OR m.full_name LIKE '%$search%'";
}

// Count
$countQuery = "
SELECT COUNT(*) AS total
FROM borrow_records br
JOIN members m ON br.member_id = m.id
JOIN books b ON br.book_id = b.id
$search_sql
";
$totalRecords = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch data
$query = "
SELECT br.id, b.title, m.full_name, br.borrowed_at, br.due_at, br.returned_at
FROM borrow_records br
JOIN members m ON br.member_id = m.id
JOIN books b ON br.book_id = b.id
$search_sql
ORDER BY br.borrowed_at DESC
LIMIT $start, $limit
";
$records = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowed Books</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <h2>Borrowed Books</h2>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search member or title..."
               value="<?= htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Book Title</th>
                <th>Borrowed</th>
                <th>Due</th>
                <th>Status</th>
                <th>Return</th>
            </tr>
        </thead>
        <tbody>

        <?php if ($records->num_rows == 0): ?>
            <tr><td colspan="6" class="empty">No borrowed books found.</td></tr>
        <?php endif; ?>

        <?php while ($row = $records->fetch_assoc()): ?>
            <tr>
                <td><?= $row['full_name'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['borrowed_at'] ?></td>
                <td><?= $row['due_at'] ?></td>
                <td><?= $row['returned_at'] ? "✔ Returned" : "❌ Not Returned" ?></td>
                <td>
                    <?php if (!$row['returned_at']): ?>
                        <a href="return_book.php?id=<?= $row['id'] ?>" class="btn-blue">Return</a>
                    <?php else: ?>
                        <span style="color: green;">✔ Done</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>">« Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= $search ?>" 
               class="<?= ($i == $page ? 'active-page' : '') ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>">Next »</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

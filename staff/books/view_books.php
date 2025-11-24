<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');


// Pagination settings
$limit = 10; // books per page
$page  = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search handling
$search = "";
$search_sql = "";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_sql = "
        WHERE title   LIKE '%$search%'
           OR author  LIKE '%$search%'
           OR isbn    LIKE '%$search%'
           OR category LIKE '%$search%'
    ";
}

// Count total books for pagination
$countQuery  = "SELECT COUNT(*) AS total FROM books $search_sql";
$totalResult = $conn->query($countQuery);
$totalBooks  = $totalResult->fetch_assoc()['total'];
$totalPages  = $totalBooks > 0 ? ceil($totalBooks / $limit) : 1;

// Fetch paginated books
$query = "
    SELECT * 
    FROM books 
    $search_sql
    ORDER BY title ASC
    LIMIT $start, $limit
";
$books = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Books | Staff Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css'); ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <div class="page-header">
        <h2>Manage Books</h2>
        <a href="add_book.php" class="btn-blue add-btn">+ Add Book</a>
    </div>

    <form method="GET" class="search-box">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by title, author, ISBN, category..." 
            value="<?= htmlspecialchars($search); ?>"
        >
        <button type="submit">Search</button>
    </form>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Total Copies</th>
                    <th>Available</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books->num_rows == 0): ?>
                    <tr>
                        <td colspan="7" class="empty">No books found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = $books->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['isbn']); ?></td>
                            <td><?= htmlspecialchars($row['title']); ?></td>
                            <td><?= htmlspecialchars($row['author']); ?></td>
                            <td><?= htmlspecialchars($row['category']); ?></td>
                            <td><?= (int)$row['copies_total']; ?></td>
                            <td>
                                <?php if ($row['copies_available'] > 0): ?>
                                    <span style="color:green;font-weight:bold;">
                                        <?= (int)$row['copies_available']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:red;font-weight:bold;">0 (Unavailable)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a 
                                    href="edit_book.php?id=<?= $row['id']; ?>" 
                                    class="btn-small-blue"
                                >
                                    Edit
                                </a>
                                <a 
                                    href="delete_book.php?id=<?= $row['id']; ?>" 
                                    class="btn-small-red"
                                    onclick="return confirm('Are you sure you want to delete this book?');"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1; ?>&search=<?= htmlspecialchars($search); ?>">« Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a 
                href="?page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>" 
                class="<?= $i == $page ? 'active-page' : ''; ?>"
            >
                <?= $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search=<?= htmlspecialchars($search); ?>">Next »</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>


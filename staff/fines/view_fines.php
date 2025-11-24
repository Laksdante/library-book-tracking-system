<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

//pagnation 

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search 

$search = "";
$search_sql = "";

if(!empty($_GET['search'])){
    $search = trim($_GET['search']);
    $search_sql = "WHERE m.full_name LIKE '%$search%'
        OR b.title LIKE '%$search%'
        OR f.amount LIKE '%$search%'";
}

// Count fines 
$countQuery = "
SELECT COUNT(*) AS total
FROM fines f
JOIN members m ON f.member_id = m.id
JOIN books b ON f.book_id = b.id
$search_sql
";
$total = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

//featch the fines 
$sql = "
SELECT f.*, m.full_name, b.title
FROM fines f
JOIN members m ON f.member_id = m.id
JOIN books b ON f.book_id = b.id
$search_sql
ORDER BY f.issued_at DESC
LIMIT $start, $limit
";
$fines = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fines | Staff Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
    <?php include('../layout/sidebar.php'); ?>
    <?php include('../layout/topnav.php'); ?>

    <div class="main-content">
        <div class="page-header">
        <h2>📌 Library Fines</h2>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search"
               placeholder="Search member, book, amount..."
               value="<?= htmlspecialchars($search) ?>">
        <button>Search</button>
    </form>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Fine (UGX)</th>
                    <th>Issued</th>
                    <th>Status</th>
                    <th width="140">Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($fines->num_rows == 0): ?>
                <tr><td colspan="6" class="empty">No fines found.</td></tr>
            <?php endif; ?>

            <?php while($f = $fines->fetch_assoc()): ?>
                <tr>
                    <td><?= $f['full_name'] ?></td>
                    <td><?= $f['title'] ?></td>
                    <td><strong><?= number_format($f['amount']) ?></strong></td>
                    <td><?= $f['issued_at'] ?></td>
                    <td>
                        <?php if ($f['status'] == "paid"): ?>
                            <span class="badge active">Paid</span>
                        <?php else: ?>
                            <span class="badge archived">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($f['status'] == "unpaid"): ?>
                            <a href="mark_paid.php?id=<?= $f['id'] ?>"
                               class="btn-small-blue">
                               Mark Paid
                            </a>
                        <?php endif; ?>
                        <a onclick="return confirm('Delete this fine?')"
                           href="delete_fine.php?id=<?= $f['id'] ?>"
                           class="btn-small-red">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
    

    <!--Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>">« Prev</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($i==$page?'active-page':'') ?>"
               href="?page=<?= $i ?>&search=<?= $search ?>">
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
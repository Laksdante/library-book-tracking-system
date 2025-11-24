<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = "";
$search_sql = "";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_sql = "
        WHERE full_name LIKE '%$search%'
        OR reg_no LIKE '%$search%'
        OR email LIKE '%$search%'
    ";
}

// Count total members
$countQuery = "
SELECT COUNT(*) AS total 
FROM members
$search_sql
";
$totalMembers = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($totalMembers / $limit);

// Fetch members
$query = "
SELECT * FROM members
$search_sql
ORDER BY full_name ASC
LIMIT $start, $limit
";
$members = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members | Staff</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <div class="page-header">
        <h2>Manage Members</h2>
        <a href="add_member.php" class="btn-blue add-btn">+ Add Member</a>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search"
               placeholder="Search member by name, reg no, or email..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Reg No.</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th width="170px">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if ($members->num_rows == 0): ?>
                <tr>
                    <td colspan="6" class="empty">No members found.</td>
                </tr>
                <?php endif; ?>

                <?php while($m = $members->fetch_assoc()): ?>
                <tr>
                    <td><?= $m['reg_no'] ?></td>
                    <td><?= $m['full_name'] ?></td>
                    <td><?= $m['email'] ?></td>
                    <td><?= $m['phone'] ?></td>
                    <td>
                        <?php if ($m['status'] == 'active'): ?>
                            <span class="badge active">Active</span>
                        <?php else: ?>
                            <span class="badge archived">Archived</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_member.php?id=<?= $m['id'] ?>" class="btn-small-blue">
                            Edit
                        </a>

                        <?php if ($m['status'] == 'active'): ?>
                            <a href="archive_member.php?id=<?= $m['id'] ?>"
                               class="btn-small-red"
                               onclick="return confirm('Archive this member?');">
                                Archive
                            </a>
                        <?php else: ?>
                            <span class="btn-small-gray">Archived</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&search=<?= $search ?>">« Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($i == $page ? 'active-page' : '') ?>"
               href="?page=<?= $i ?>&search=<?= $search ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&search=<?= $search ?>">Next »</a>
        <?php endif; ?>
    </div>

</div>


</body>
</html>

<?php
include('../../includes/auth_check.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

// Allow Only admin

if ($_SESSION['role'] !== 'admin') {
    header("location: ../dashboard.php");
    exit();
}

$result = $conn->query("SELECT id, full_name, username, email, role, status, created_at FROM staff ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Staff Management</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css'); ?>">
    </head>
<body>
    <?php include('../layout/sidebar.php'); ?>
    <?php include('../layout/topnav.php'); ?>

    <div class="main-content">

    <div class="page-header">
        <h2>👤 Staff Management</h2>
        <a href="add_staff.php" class="btn-primary">➕ Add Staff</a>
    </div>

    <div class="table-responsive">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="8" class="empty">No staff found.</td>
                </tr>
            <?php endif; ?>

            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['full_name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                    <td>
                        <span class="badge <?= $row['role'] === 'admin' ? 'badge-admin' : 'badge-staff'; ?>">
                            <?= ucfirst($row['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $row['status'] === 'active' ? 'badge-active' : 'badge-disabled'; ?>">
                            <?= ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td><?= date('Y-m-d', strtotime($row['created_at'])); ?></td>
                    <td>
                        <?php if ($row['id'] != $_SESSION['staff_id']): ?>
                            <a href="toggle_status.php?id=<?= $row['id']; ?>"
                               class="btn-sm <?= $row['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>"
                               onclick="return confirm('Are you sure?');">
                                <?= $row['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                            </a>
                        <?php else: ?>
                            <em>You</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
<div class="sidebar">
    <h2>Staff Panel</h2>
    <ul>
        <li><a href="../dashboard.php">🏠 Dashboard</a></li>
        <li><a href="../books/view_books.php">📚 Books</a></li>
        <li><a href="../borrow/view_borrowed.php">🔄 Borrow/Return</a></li>
        <li><a href="../members/view_members.php">👥 Members</a></li>
        <li><a href="../fines/view_fines.php">💰 Fines</a></li>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li><a href="<?= base_url('staff/staff/view_staff.php'); ?>">👤 Staff Management</a></li>
        <li><a href="<?= base_url('staff/reports/index.php'); ?>">📊 Reports</a></li>
    <?php endif; ?>
        <li>
            <a href="<?= base_url('staff/profile/change_password.php'); ?>">🔐 Change Password</a>
        </li>

    </ul>
</div>

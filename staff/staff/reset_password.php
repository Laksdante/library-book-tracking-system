<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

// Admin only
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_staff.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $conn->query("UPDATE staff SET password = '$newPassword' WHERE id = $id");
    header("Location: view_staff.php?reset=1");
    exit();
}

$user = $conn->query("SELECT username FROM staff WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css'); ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">
    <div class="form-container">
        <h2>Reset Password</h2>
        <p>Reset password for: <strong><?= htmlspecialchars($user['username']); ?></strong></p>

        <form method="POST">
            <label>New Password</label>
            <input type="password" name="new_password" required minlength="6">
            <button type="submit">Reset Password</button>
        </form>
    </div>
</div>

</body>
</html>

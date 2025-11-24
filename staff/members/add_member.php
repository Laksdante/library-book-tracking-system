<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/csrf.php');
include('../../includes/path_helper.php');

$token = generate_csrf_token();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {

        $reg_no = trim($_POST['reg_no']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $insert = $conn->prepare("
            INSERT INTO members (reg_no, full_name, email, phone, username, password, status, role)
            VALUES (?, ?, ?, ?, ?, ?, 'active', 'member')
        ");
        $insert->bind_param("ssssss", $reg_no, $full_name, $email, $phone, $username, $password);

        if ($insert->execute()) {
            $message = "<div class='success'>✔ Member added successfully.</div>";
        } else {
            $message = "<div class='error'>❌ Error adding member.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <h2>Add New Member</h2>
    <?= $message ?>

    <div class="form-container">

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <label>Registration Number</label>
            <input type="text" name="reg_no" required>

            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Add Member</button>
        </form>

        <a href="view_members.php" class="back-link">← Back</a>

    </div>

</div>

</body>
</html>

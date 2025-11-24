<?php
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/csrf.php');
include('../../includes/path_helper.php');

if (!isset($_GET['id'])) {
    header("Location: view_members.php");
    exit();
}

$id = intval($_GET['id']);
$token = generate_csrf_token();

$query = $conn->query("SELECT * FROM members WHERE id = $id");
$member = $query->fetch_assoc();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {

        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        $update = $conn->prepare("
            UPDATE members SET full_name=?, email=?, phone=? WHERE id=?
        ");
        $update->bind_param("sssi", $full_name, $email, $phone, $id);

        if ($update->execute()) {
            $message = "<div class='success'>✔ Member updated.</div>";
        } else {
            $message = "<div class='error'>❌ Update failed.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Member</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">

    <h2>Edit Member</h2>
    <?= $message ?>

    <div class="form-container">

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= $member['full_name'] ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?= $member['email'] ?>">

            <label>Phone</label>
            <input type="text" name="phone" value="<?= $member['phone'] ?>">

            <button type="submit">Update Member</button>
        </form>

        <a href="view_members.php" class="back-link">← Back</a>

    </div>

</div>

</body>
</html>

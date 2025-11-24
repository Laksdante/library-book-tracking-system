<?php
session_start();
include('db_connect.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {

        // ============================
        // STAFF LOGIN CHECK
        // ============================
        $stmt = $conn->prepare("SELECT * FROM staff WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $staffResult = $stmt->get_result();

        if ($staffResult->num_rows === 1) {
            $staff = $staffResult->fetch_assoc();

            if (password_verify($password, $staff['password'])) {
                // Successful staff login
                $_SESSION['staff_id'] = $staff['id'];
                $_SESSION['username'] = $staff['username'];
                $_SESSION['role'] = 'staff';

                header("Location: staff/dashboard.php");
                exit();
            } else {
                // Staff password incorrect
                $error = "Invalid password.";
            }

        } else {
            // No staff with that username → TRY MEMBER LOGIN
            // ============================
            // MEMBER LOGIN CHECK
            // ============================
            $stmt2 = $conn->prepare("SELECT * FROM members WHERE username = ?");
            $stmt2->bind_param("s", $username);
            $stmt2->execute();
            $memberResult = $stmt2->get_result();

            if ($memberResult->num_rows === 1) {
                $member = $memberResult->fetch_assoc();

                if (password_verify($password, $member['password'])) {
                    // Successful member login
                    $_SESSION['member_id'] = $member['id'];
                    $_SESSION['username'] = $member['username'];
                    $_SESSION['role'] = 'member';

                    header("Location: members/dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No user found with that username.";
            }
            $stmt2->close();
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Login | Cavendish University</title>
    <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body>
    <div class="login-container">
        <!---University Logo -->
        <div class="logo-area">
            <img src="assets/images/cuulogo.png" alt="Cavendish University Logo">
        </div>

        <h2>Library System Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username">

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password">

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

<?php
include('../includes/auth_check.php');
include('../db_connect.php');
include('../includes/path_helper.php');

// fetch data counts for dashboard overview
$books_count = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$members_count = $conn->query("SELECT COUNT(*) AS total FROM members Where status = 'active'")->fetch_assoc()['total'];
$borrowed_count = $conn->query("SELECT COUNT(*) AS total FROM borrow_records WHERE returned_at IS NULL")->fetch_assoc()['total']; 
$fines_count = $conn->query("SELECT COUNT(*) AS total FROM fines")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Library System</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <h2>Admin Panel</h2>
  <ul>
    <li><a href="../staff/dashboard.php">🏠 Dashboard</a></li>
    <li><a href="../staff/books/view_books.php">📚 Books</a></li>
    <li><a href="../staff/members/view_members.php">👥 Members</a></li>
    <li><a href="../staff/borrow/borrow_book.php">🔄 Borrow/Return</a></li>
    <li><a href="../staff/fines/view_fines.php">💰 Fines</a></li>
    <li><a href="../staff/reports/index.php">📊 Reports</a></li>
  </ul>
</div>

<!-- Top Navigation Bar -->
<div class="topnav">
  <div class="left">
    <h1>Cavendish Library Dashboard</h1>
  </div>
  <div class="right">
    <span class="user"><?= htmlspecialchars($_SESSION['username']); ?></span>
    <a href="../logout.php" class="logout-btn">Logout</a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="cards">
    <div class="card blue">
      <h3>Total Books</h3>
      <p><?= $books_count ?></p>
    </div>
    <div class="card navy">
      <h3>Active Members</h3>
      <p><?= $members_count ?></p>
    </div>
    <div class="card sky">
      <h3>Borrowed Books</h3>
      <p><?= $borrowed_count ?></p>
    </div>
    <div class="card lightblue">
      <h3>Total Fines</h3>
      <p><?= $fines_count ?></p>
    </div>
  </div>
</div>

</body>
</html>
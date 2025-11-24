<div class="topnav">
    <span class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</span>
    <h1>Cavendish Library System</h1>

    <div class="user-right">
        <span class="user-label">👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../../logout.php" class="logout-btn">Logout</a>
    </div>
</div>

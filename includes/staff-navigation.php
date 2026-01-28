<?php
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../staff/login.php");
    exit;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">
            <?= htmlspecialchars($_SESSION['staff_name']); ?>
            <small class="text-muted">(<?= ucfirst($_SESSION['staff_role']); ?>)</small>
        </span>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="students.php">Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

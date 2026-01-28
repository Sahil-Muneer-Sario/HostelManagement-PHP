<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard | Hostel Management System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">

    <!-- CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">
</head>

    <style>
        .topbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0;
        }
        .navbar-brand {
            font-weight: bold;
            color: #333;
        }
        .logo-icon img {
            height: 40px;
            margin-right: 10px;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-card h5 {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .stat-card h2 {
            margin: 0;
            font-weight: bold;
        }
    </style>
<body>

<!-- Preloader -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<!-- Main Wrapper -->
<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed"
     data-header-position="fixed" data-boxed-layout="full">

<!-- Topbar -->
<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">

        <!-- Logo -->
        <div class="navbar-header">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                
                <!-- Logo Icon -->
                <b class="logo-icon">
                    <img src="../assets/images/logo-icon.png"
                         alt="Staff Panel"
                         class="dark-logo"
                         style="height:40px;">
                </b>

                <!-- Logo Text -->
                <span class="logo-text ml-2">Staff Panel</span>
            </a>
        </div>

        <!-- Navbar Content -->
        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ml-auto align-items-center">

                <!-- Staff Name + Role -->
                <li class="nav-item">
                    <span class="nav-link text-dark">
                        Welcome,
                        <strong>
                            <?= htmlspecialchars($_SESSION['staff_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </strong>
                        <small class="text-muted">
                            (<?= htmlspecialchars($_SESSION['staff_role'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)
                        </small>
                    </span>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i data-feather="log-out"></i> Logout
                    </a>
                </li>

            </ul>
        </div>

    </nav>
</header>

<!-- Sidebar -->
<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar">

        <nav class="sidebar-nav">
            <ul id="sidebarnav">

                <li class="sidebar-item">
                    <a class="sidebar-link" href="dashboard.php">
                        <i data-feather="home"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="student.php">
                        <i data-feather="users"></i>
                        <span class="hide-menu">Students</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="attendance.php">
                        <i data-feather="check-square"></i>
                        <span class="hide-menu">Attendance</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="fees-entry.php">
                        <i data-feather="dollar-sign"></i>
                        <span class="hide-menu">Fees Entry</span>
                    </a>
                </li>


                <li class="sidebar-item">
                    <a class="sidebar-link" href="complaints.php">
                        <i data-feather="message-circle"></i>
                        <span class="hide-menu">Complaints</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="logout.php">
                        <i data-feather="log-out"></i>
                        <span class="hide-menu">Logout</span>
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</aside>

<!-- Page Wrapper -->
<div class="page-wrapper">

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 align-self-center">
            <h4 class="page-title text-dark font-weight-medium mb-1">
                Staff Dashboard
            </h4>
        </div>
    </div>
</div>

<!-- Container -->
<div class="container-fluid">

<!-- Cards -->
<div class="card-group">

    <div class="card border-right">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="text-dark mb-1 font-weight-medium">Students</h2>
                    <h6 class="text-muted font-weight-normal mb-0">Manage Students</h6>
                </div>
                <div class="ml-auto">
                    <i data-feather="users" class="text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-right">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="text-dark mb-1 font-weight-medium">Attendance</h2>
                    <h6 class="text-muted font-weight-normal mb-0">Mark Attendance</h6>
                </div>
                <div class="ml-auto">
                    <i data-feather="check-square" class="text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="text-dark mb-1 font-weight-medium">Complaints</h2>
                    <h6 class="text-muted font-weight-normal mb-0">View Complaints</h6>
                </div>
                <div class="ml-auto">
                    <i data-feather="message-circle" class="text-muted"></i>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

<!-- Footer -->
<?php include '../includes/footer.php'; ?>

</div>
</div>

<!-- JS -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="../dist/js/app-style-switcher.js"></script>
<script src="../dist/js/feather.min.js"></script>
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="../dist/js/sidebarmenu.js"></script>
<script src="../dist/js/custom.min.js"></script>

</body>
</html>

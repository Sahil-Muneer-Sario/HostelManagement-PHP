<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/dbconn.php');

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is Warden - if not, show access denied page
$access_denied = false;
if ($_SESSION['staff_role'] != 'Warden') {
    $access_denied = true;
}

// Get statistics (only if access is allowed)
if (!$access_denied) {
    $totalQuery = "SELECT COUNT(*) as total FROM attendance";
    $totalResult = $mysqli->query($totalQuery);
    $totalCount = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

    $presentQuery = "SELECT COUNT(*) as present FROM attendance WHERE status='Present' AND attendancedate=CURDATE()";
    $presentResult = $mysqli->query($presentQuery);
    $presentCount = $presentResult ? $presentResult->fetch_assoc()['present'] : 0;

    $absentQuery = "SELECT COUNT(*) as absent FROM attendance WHERE status='Absent' AND attendancedate=CURDATE()";
    $absentResult = $mysqli->query($absentQuery);
    $absentCount = $absentResult ? $absentResult->fetch_assoc()['absent'] : 0;

    $todayTotal = $presentCount + $absentCount;
    $rate = $todayTotal > 0 ? round(($presentCount / $todayTotal) * 100, 2) : 0;
} else {
    // Set default values if access denied
    $totalCount = 0;
    $presentCount = 0;
    $absentCount = 0;
    $todayTotal = 0;
    $rate = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS from CDN -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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
</head>

<body>

<!-- Topbar -->
<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        
        <!-- Logo -->
        <div class="navbar-header">
            <a class="navbar-brand" href="dashboard.php">
                <b class="logo-icon">
                    <img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" width="40" height="40" />
                </b>
                <span class="logo-text">Staff Panel</span>
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
                            <?= htmlspecialchars($_SESSION['staff_name']); ?>
                        </strong>
                        <small class="text-muted">
                            (<?= htmlspecialchars($_SESSION['staff_role']); ?>)
                        </small>
                    </span>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>

            </ul>
        </div>

    </nav>
</header>


<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            
            <?php if ($access_denied): ?>
            <!-- Access Denied Message -->
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-lock text-danger" style="font-size: 80px; margin-bottom: 20px;"></i>
                            <h2 class="text-danger mb-3">Access Denied</h2>
                            <p class="lead mb-4">Sorry, this feature is restricted to <strong>Wardens Only</strong>.</p>
                            <p class="text-muted mb-4">
                                You are currently logged in as <strong><?= htmlspecialchars($_SESSION['staff_role']) ?></strong>.<br>
                                Only staff members with the Warden role can access the attendance management system.
                            </p>
                            <a href="dashboard.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Normal Attendance Content -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Student Attendance Records</h2>
                <div>
                    <span class="text-muted mr-3">Today: <?= date('M d, Y') ?></span>
                    <a href="mark-attendance.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Mark Attendance
                    </a>
                </div>
            </div>
            
            <?php if ($presentCount == 0 && $absentCount == 0 && $totalCount > 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> No attendance records found for today (<?= date('M d, Y') ?>). 
                All records shown below are from previous dates. <a href="mark-attendance.php" class="alert-link">Mark attendance for today</a>.
            </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white stat-card">
                        <div class="card-body">
                            <h5><i class="fas fa-clipboard-list"></i> Total Records</h5>
                            <h2><?= $totalCount ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white stat-card">
                        <div class="card-body">
                            <h5><i class="fas fa-check-circle"></i> Present Today</h5>
                            <h2><?= $presentCount ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white stat-card">
                        <div class="card-body">
                            <h5><i class="fas fa-times-circle"></i> Absent Today</h5>
                            <h2><?= $absentCount ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white stat-card">
                        <div class="card-body">
                            <h5><i class="fas fa-percentage"></i> Attendance Rate</h5>
                            <h2><?= $rate ?>%</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Reg No</th>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Room No</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$sql = "SELECT a.id, a.regno, a.course, a.roomno, a.status, a.attendancedate, a.attendancetime,
               u.firstName, u.middleName, u.lastName
        FROM attendance a
        LEFT JOIN userregistration u ON a.regno = u.regNo
        ORDER BY a.attendancedate DESC, a.attendancetime DESC";

$result = $mysqli->query($sql);

if (!$result) {
    echo "<tr><td colspan='8' class='text-center text-danger'>Error: " . $mysqli->error . "</td></tr>";
} elseif ($result->num_rows > 0) {
    $cnt = 1;
    while ($row = $result->fetch_assoc()) {
        $statusClass = ($row['status'] == 'Present') ? 'badge-success' : 'badge-danger';
        $statusIcon = ($row['status'] == 'Present') ? 'fa-check' : 'fa-times';
        
        echo "<tr>";
        echo "<td>" . $cnt++ . "</td>";
        echo "<td>" . htmlspecialchars($row['regno']) . "</td>";
        
        // Handle student name
        $studentName = trim($row['firstName'].' '.$row['middleName'].' '.$row['lastName']);
        if (empty($studentName)) {
            $studentName = "<span class='text-muted'>Not Found</span>";
        } else {
            $studentName = htmlspecialchars($studentName);
        }
        echo "<td>" . $studentName . "</td>";
        
        echo "<td>" . htmlspecialchars($row['course']) . "</td>";
        echo "<td>" . htmlspecialchars($row['roomno']) . "</td>";
        echo "<td><span class='badge " . $statusClass . "'><i class='fas " . $statusIcon . "'></i> " . htmlspecialchars($row['status']) . "</span></td>";
        echo "<td>" . date('M d, Y', strtotime($row['attendancedate'])) . "</td>";
        echo "<td>" . date('h:i A', strtotime($row['attendancetime'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-muted'>No attendance records found</td></tr>";
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS from CDN -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        "pageLength": 10,
        "order": [[6, "desc"]],
        "language": {
            "search": "Search records:",
            "lengthMenu": "Show _MENU_ records per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "emptyTable": "No attendance records available"
        }
    });
});
</script>

</body>
</html>
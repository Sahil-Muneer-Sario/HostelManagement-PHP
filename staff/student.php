<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/dbconn.php');

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Registered Students</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS from CDN -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                        <i data-feather="log-out"></i> Logout
                    </a>
                </li>

            </ul>
        </div>

    </nav>
</header>


<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Registered Students</h2>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Reg No</th>
                                    <th>Full Name</th>
                                    <th>Gender</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Registered On</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$sql = "SELECT regNo, firstName, middleName, lastName, gender, contactNo, email, regDate
        FROM userregistration
        ORDER BY regDate DESC";

$result = $mysqli->query($sql);

if (!$result) {
    echo "<tr><td colspan='7' class='text-center text-danger'>Error: " . $mysqli->error . "</td></tr>";
} elseif ($result->num_rows > 0) {
    $cnt = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $cnt++ . "</td>";
        echo "<td>" . htmlspecialchars($row['regNo']) . "</td>";
        echo "<td>" . htmlspecialchars(trim($row['firstName'].' '.$row['middleName'].' '.$row['lastName'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contactNo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['regDate']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center text-danger'>No students found</td></tr>";
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    $('#studentsTable').DataTable({
        "pageLength": 10,
        "order": [[6, "desc"]]
    });
});
</script>

</body>
</html>
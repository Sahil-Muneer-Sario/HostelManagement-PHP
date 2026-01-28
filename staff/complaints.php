<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/dbconn.php');

// Staff/Warden Login Protection
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

$wardenId = $_SESSION['staff_id'];
$successMsg = '';
$errorMsg = '';

// Handle status update and remark
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_complaint'])) {
    $complaintId = intval($_POST['complaint_id']);
    $status = $_POST['status'];
    $adminRemark = trim($_POST['admin_remark']);
    
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ?, adminRemark = ?, updatedAt = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $status, $adminRemark, $complaintId);
    
    if ($stmt->execute()) {
        $successMsg = "Complaint updated successfully!";
    } else {
        $errorMsg = "Error updating complaint: " . $stmt->error;
    }
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = '';

if ($filter != 'all') {
    $whereClause = "WHERE status = '" . $mysqli->real_escape_string($filter) . "'";
}

$sql = "SELECT * FROM complaints $whereClause ORDER BY createdAt DESC";
$result = $mysqli->query($sql);

// Get complaint statistics
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as inprogress,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
    FROM complaints";
$statsResult = $mysqli->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Complaints - Warden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card h3 { font-size: 36px; font-weight: bold; margin: 0; }
        .stat-card p { margin: 0; font-size: 14px; }
        .bg-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-pending { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-inprogress { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-resolved { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .bg-rejected { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-inprogress { background-color: #17a2b8; color: #fff; }
        .status-resolved { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
    
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
                        <i data-feather="log-out"></i> Logout
                    </a>
                </li>

            </ul>
        </div>

    </nav>
</header>

<div class="container-fluid mt-4">
    
    <h2 class="mb-4">Manage Student Complaints</h2>
    
    <?php if ($successMsg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $successMsg ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $errorMsg ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="stat-card bg-total">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Complaints</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card bg-pending">
                <h3><?= $stats['pending'] ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-inprogress">
                <h3><?= $stats['inprogress'] ?></h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card bg-resolved">
                <h3><?= $stats['resolved'] ?></h3>
                <p>Resolved</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card bg-rejected">
                <h3><?= $stats['rejected'] ?></h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>
    
    <!-- Filter Buttons -->
    <div class="mb-3">
        <a href="?filter=all" class="btn btn-outline-secondary <?= $filter == 'all' ? 'active' : '' ?>">All</a>
        <a href="?filter=Pending" class="btn btn-outline-warning <?= $filter == 'Pending' ? 'active' : '' ?>">Pending</a>
        <a href="?filter=In Progress" class="btn btn-outline-info <?= $filter == 'In Progress' ? 'active' : '' ?>">In Progress</a>
        <a href="?filter=Resolved" class="btn btn-outline-success <?= $filter == 'Resolved' ? 'active' : '' ?>">Resolved</a>
        <a href="?filter=Rejected" class="btn btn-outline-danger <?= $filter == 'Rejected' ? 'active' : '' ?>">Rejected</a>
    </div>
    
    <!-- Complaints Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="complaintsTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Complaint Type</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cnt = 1;
                        if ($result && $result->num_rows > 0):
                            while ($complaint = $result->fetch_assoc()): 
                                $statusClass = 'status-' . strtolower(str_replace(' ', '', $complaint['status']));
                        ?>
                            <tr>
                                <td><?= $cnt++ ?></td>
                                <td><?= htmlspecialchars($complaint['studentRegNo']) ?></td>
                                <td><?= htmlspecialchars($complaint['studentName']) ?></td>
                                <td><strong><?= htmlspecialchars($complaint['complaintType']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($complaint['complaintDetails'], 0, 50)) ?>...</td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($complaint['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y, h:i A', strtotime($complaint['createdAt'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" 
                                            data-target="#complaintModal<?= $complaint['id'] ?>">
                                        View/Update
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal for each complaint -->
                            <div class="modal fade" id="complaintModal<?= $complaint['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Complaint Details</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="POST" action="">
                                            <div class="modal-body">
                                                <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Student:</strong> <?= htmlspecialchars($complaint['studentName']) ?></p>
                                                        <p><strong>Reg No:</strong> <?= htmlspecialchars($complaint['studentRegNo']) ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Type:</strong> <?= htmlspecialchars($complaint['complaintType']) ?></p>
                                                        <p><strong>Submitted:</strong> <?= date('d M Y, h:i A', strtotime($complaint['createdAt'])) ?></p>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="form-group">
                                                    <label><strong>Complaint Details:</strong></label>
                                                    <p class="form-control-plaintext border p-2 bg-light">
                                                        <?= nl2br(htmlspecialchars($complaint['complaintDetails'])) ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="status">Update Status:</label>
                                                    <select class="form-control" name="status" required>
                                                        <option value="Pending" <?= $complaint['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                        <option value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                        <option value="Rejected" <?= $complaint['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                    </select>
                                                </div>
                                                
                                              <div class="form-group">
                                                <label for="admin_remark_<?= $complaint['id'] ?>">Admin Remark:</label>

                                                <textarea
                                                    class="form-control"
                                                    id="admin_remark_<?= $complaint['id'] ?>"
                                                    name="admin_remark"
                                                    rows="3"
                                                    placeholder="Enter your remarks here..."
                                                ><?= htmlspecialchars($complaint['adminRemark'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </div>


                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" name="update_complaint" class="btn btn-primary">Update Complaint</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No complaints found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#complaintsTable').DataTable({
        "pageLength": 25,
        "order": [[6, "desc"]]
    });
});
</script>

</body>
</html>
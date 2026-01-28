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

$success_msg = '';
$error_msg = '';

// Handle form submission (only if access allowed)
if (!$access_denied && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_single'])) {
        // Mark single student attendance
        $regno = $_POST['regno'];
        $course = $_POST['course'];
        $roomno = $_POST['roomno'];
        $status = $_POST['status'];
        $attendancedate = $_POST['attendancedate'];
        $attendancetime = date('H:i:s');
        
        // Check if attendance already marked for today
        $checkQuery = "SELECT id FROM attendance WHERE regno=? AND attendancedate=?";
        $checkStmt = $mysqli->prepare($checkQuery);
        $checkStmt->bind_param("ss", $regno, $attendancedate);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $error_msg = "Attendance already marked for this student today!";
        } else {
            $insertQuery = "INSERT INTO attendance (regno, course, roomno, status, attendancedate, attendancetime) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("ssssss", $regno, $course, $roomno, $status, $attendancedate, $attendancetime);
            
            if ($stmt->execute()) {
                $success_msg = "Attendance marked successfully!";
            } else {
                $error_msg = "Error marking attendance: " . $mysqli->error;
            }
        }
    } elseif (isset($_POST['mark_bulk'])) {
        // Mark bulk attendance
        $attendancedate = $_POST['bulk_date'];
        $attendancetime = date('H:i:s');
        $marked_count = 0;
        $error_count = 0;
        
        foreach ($_POST['students'] as $regno => $data) {
            if (isset($data['mark'])) {
                $status = $data['status'];
                $course = $data['course'];
                $roomno = $data['roomno'];
                
                // Check if attendance already marked
                $checkQuery = "SELECT id FROM attendance WHERE regno=? AND attendancedate=?";
                $checkStmt = $mysqli->prepare($checkQuery);
                $checkStmt->bind_param("ss", $regno, $attendancedate);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows == 0) {
                    $insertQuery = "INSERT INTO attendance (regno, course, roomno, status, attendancedate, attendancetime) 
                                   VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($insertQuery);
                    $stmt->bind_param("ssssss", $regno, $course, $roomno, $status, $attendancedate, $attendancetime);
                    
                    if ($stmt->execute()) {
                        $marked_count++;
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++;
                }
            }
        }
        
        if ($marked_count > 0) {
            $success_msg = "Attendance marked for $marked_count student(s) successfully!";
        }
        if ($error_count > 0) {
            $error_msg .= " $error_count student(s) already have attendance marked or failed.";
        }
    }
}

// Get all registered students (only if access allowed)
if (!$access_denied) {
    $studentsQuery = "SELECT regNo, firstName, middleName, lastName, contactNo, email FROM userregistration ORDER BY firstName";
    $studentsResult = $mysqli->query($studentsQuery);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mark Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
        .tab-content {
            padding: 20px 0;
        }
        .student-row {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .student-row:hover {
            background: #f8f9fa;
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
                    <img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
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
                                Only staff members with the Warden role can mark attendance.
                            </p>
                            <div>
                                <a href="dashboard.php" class="btn btn-primary btn-lg mr-2">
                                    <i class="fas fa-home"></i> Return to Dashboard
                                </a>
                                <a href="attendance.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Back to Attendance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Normal Mark Attendance Content -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Mark Attendance</h2>
                <a href="attendance.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Records
                </a>
            </div>
            
            <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $success_msg ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?= $error_msg ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#single">
                        <i class="fas fa-user"></i> Single Student
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#bulk">
                        <i class="fas fa-users"></i> Bulk Attendance
                    </a>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Single Student Tab -->
                <div id="single" class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mark Single Student Attendance</h5>
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Student Registration No <span class="text-danger">*</span></label>
                                            <select name="regno" class="form-control" id="studentSelect" required>
                                                <option value="">Select Student</option>
                                                <?php
                                                $studentsResult->data_seek(0);
                                                while ($student = $studentsResult->fetch_assoc()) {
                                                    $fullName = trim($student['firstName'].' '.$student['middleName'].' '.$student['lastName']);
                                                    echo "<option value='" . htmlspecialchars($student['regNo']) . "'>" 
                                                         . htmlspecialchars($student['regNo']) . " - " 
                                                         . htmlspecialchars($fullName) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Course <span class="text-danger">*</span></label>
                                            <input type="text" name="course" class="form-control" placeholder="Enter course name" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Room No <span class="text-danger">*</span></label>
                                            <input type="text" name="roomno" class="form-control" placeholder="Enter room number" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option value="Present">Present</option>
                                                <option value="Absent">Absent</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date <span class="text-danger">*</span></label>
                                            <input type="date" name="attendancedate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" name="mark_single" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Mark Attendance
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Bulk Attendance Tab -->
                <div id="bulk" class="tab-pane fade">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mark Bulk Attendance</h5>
                            <form method="POST" action="">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input type="date" name="bulk_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-success btn-block" onclick="selectAllPresent()">
                                            <i class="fas fa-check-double"></i> Mark All Present
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-warning btn-block" onclick="selectAllAbsent()">
                                            <i class="fas fa-times"></i> Mark All Absent
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                                                </th>
                                                <th>Reg No</th>
                                                <th>Student Name</th>
                                                <th>Course</th>
                                                <th>Room No</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $studentsResult->data_seek(0);
                                            while ($student = $studentsResult->fetch_assoc()) {
                                                $fullName = trim($student['firstName'].' '.$student['middleName'].' '.$student['lastName']);
                                                $regno = $student['regNo'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="students[<?= $regno ?>][mark]" class="student-checkbox">
                                                </td>
                                                <td><?= htmlspecialchars($regno) ?></td>
                                                <td><?= htmlspecialchars($fullName) ?></td>
                                                <td>
                                                    <input type="text" name="students[<?= $regno ?>][course]" class="form-control form-control-sm" placeholder="Course">
                                                </td>
                                                <td>
                                                    <input type="text" name="students[<?= $regno ?>][roomno]" class="form-control form-control-sm" placeholder="Room">
                                                </td>
                                                <td>
                                                    <select name="students[<?= $regno ?>][status]" class="form-control form-control-sm status-select">
                                                        <option value="Present">Present</option>
                                                        <option value="Absent">Absent</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <button type="submit" name="mark_bulk" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Bulk Attendance
                                </button>
                            </form>
                        </div>
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

<script>
function toggleAll(source) {
    var checkboxes = document.querySelectorAll('.student-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}

function selectAllPresent() {
    var checkboxes = document.querySelectorAll('.student-checkbox');
    var selects = document.querySelectorAll('.status-select');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
        selects[i].value = 'Present';
    }
    document.getElementById('selectAll').checked = true;
}

function selectAllAbsent() {
    var checkboxes = document.querySelectorAll('.student-checkbox');
    var selects = document.querySelectorAll('.status-select');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
        selects[i].value = 'Absent';
    }
    document.getElementById('selectAll').checked = true;
}
</script>

</body>
</html>
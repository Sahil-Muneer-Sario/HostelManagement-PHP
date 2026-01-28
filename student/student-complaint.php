<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/dbconn.php');

/* =========================
   1. AUTH CHECK
========================= */
if (!isset($_SESSION['id']) || !isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$studentId  = $_SESSION['id'];
$successMsg = '';
$errorMsg   = '';

/* =========================
   2. FETCH STUDENT DATA
========================= */
$stmt = $mysqli->prepare(
    "SELECT regNo, firstName, middleName, lastName
     FROM userregistration
     WHERE id = ?
     LIMIT 1"
);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student record not found.");
}

$studentRegNo = $student['regNo'];
$studentName  = trim(
    $student['firstName'] . ' ' .
    $student['middleName'] . ' ' .
    $student['lastName']
);

/* =========================
   3. HANDLE FORM SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {

    $complaintType    = trim($_POST['complaint_type']);
    $complaintDetails = trim($_POST['complaint_details']);

    if ($complaintType === '' || $complaintDetails === '') {
        $errorMsg = "All fields are required.";
    } else {
        $stmt = $mysqli->prepare(
            "INSERT INTO complaints
            (studentRegNo, studentName, complaintType, complaintDetails, status)
            VALUES (?, ?, ?, ?, 'Pending')"
        );
        $stmt->bind_param(
            "ssss",
            $studentRegNo,
            $studentName,
            $complaintType,
            $complaintDetails
        );

        if ($stmt->execute()) {
            $successMsg = "Complaint submitted successfully!";
        } else {
            $errorMsg = "Database error: " . $stmt->error;
        }
    }
}

/* =========================
   4. FETCH COMPLAINT TYPES
========================= */
$typesQuery  = "SELECT typeName FROM complaint_types WHERE isActive = 1 ORDER BY typeName";
$typesResult = $mysqli->query($typesQuery);

/* =========================
   5. FETCH STUDENT COMPLAINTS
========================= */
$stmt = $mysqli->prepare(
    "SELECT * FROM complaints
     WHERE studentRegNo = ?
     ORDER BY createdAt DESC"
);
$stmt->bind_param("s", $studentRegNo);
$stmt->execute();
$complaintsResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Student Complaints</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" href="../assets/images/favicon.png">
<link rel="stylesheet" href="../dist/css/style.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.status-badge{padding:5px 10px;border-radius:5px;font-size:12px;font-weight:bold}
.status-pending{background:#ffc107;color:#000}
.status-inprogress{background:#17a2b8;color:#fff}
.status-resolved{background:#28a745;color:#fff}
.status-rejected{background:#dc3545;color:#fff}
</style>
</head>

<body>
<div class="container-fluid mt-4">
<div class="col-12">

<h3 class="mb-4">Submit Complaint</h3>

<?php if ($successMsg): ?>
<div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<?php if ($errorMsg): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<!-- =========================
     COMPLAINT FORM
========================= -->
<div class="card mb-4">
<div class="card-header bg-primary text-white">New Complaint</div>
<div class="card-body">
<form method="POST">

<div class="form-group">
<label>Complaint Type *</label>
<select name="complaint_type" class="form-control" required>
<option value="">-- Select Type --</option>
<?php while ($type = $typesResult->fetch_assoc()): ?>
<option value="<?= htmlspecialchars($type['typeName']) ?>">
<?= htmlspecialchars($type['typeName']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="form-group">
<label>Complaint Details *</label>
<textarea name="complaint_details" class="form-control" rows="5" required></textarea>
</div>

<button type="submit" name="submit_complaint" class="btn btn-primary">
Submit
</button>

</form>
</div>
</div>

<!-- =========================
     COMPLAINT HISTORY
========================= -->
<div class="card">
<div class="card-header bg-dark text-white">My Complaints</div>
<div class="card-body table-responsive">

<table class="table table-bordered">
<thead>
<tr>
<th>#</th>
<th>Type</th>
<th>Details</th>
<th>Status</th>
<th>Admin Remark</th>
<th>Date</th>
</tr>
</thead>
<tbody>

<?php
$cnt = 1;
if ($complaintsResult->num_rows > 0):
while ($row = $complaintsResult->fetch_assoc()):
$statusClass = 'status-' . strtolower(str_replace(' ', '', $row['status']));
?>
<tr>
<td><?= $cnt++ ?></td>
<td><?= htmlspecialchars($row['complaintType']) ?></td>
<td><?= htmlspecialchars($row['complaintDetails']) ?></td>
<td><span class="status-badge <?= $statusClass ?>"><?= $row['status'] ?></span></td>
<td><?= $row['adminRemark'] ?: '<em>Pending</em>' ?></td>
<td><?= date('d M Y, h:i A', strtotime($row['createdAt'])) ?></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="6" class="text-center">No complaints found</td></tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

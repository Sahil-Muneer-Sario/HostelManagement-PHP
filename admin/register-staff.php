<?php
session_start();
include('../includes/dbconn.php');

if (isset($_POST['submit'])) {

    $fname     = $_POST['fname'];
    $mname     = $_POST['mname'];
    $lname     = $_POST['lname'];
    $gender    = $_POST['gender'];
    $contactno = $_POST['contact'];
    $emailid   = $_POST['email'];
    $role      = $_POST['role'];
    $status    = $_POST['status'];
    $address   = $_POST['address'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO staff
    (first_name, middle_name, last_name, gender, contact_no, email, role, status, address, password)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        "ssssssssss",
        $fname,
        $mname,
        $lname,
        $gender,
        $contactno,
        $emailid,
        $role,
        $status,
        $address,
        $password
    );

    $stmt->execute();

    echo "<script>alert('Staff Registered Successfully');</script>";
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hostel Management System</title>

    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../dist/css/style.min.css" rel="stylesheet">

    <script>
        function valid() {
            if (document.registration.password.value != document.registration.cpassword.value) {
                alert("Password and Confirm Password does not match");
                document.registration.cpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>

<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed"
     data-header-position="fixed" data-boxed-layout="full">

<header class="topbar" data-navbarbg="skin6">
    <?php include 'includes/navigation.php'; ?>
</header>

<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</aside>

<div class="page-wrapper">

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h4 class="page-title text-dark font-weight-medium mb-1">
                Staff Registration Form
            </h4>
        </div>
    </div>
</div>

<div class="container-fluid">

<form method="POST" name="registration" onsubmit="return valid();">

<div class="row">

<!-- Staff ID -->
<!-- <div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Staff ID</h4>
        <input type="text" name="staffid" class="form-control" required>
    </div></div>
</div> -->

<!-- First Name -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">First Name</h4>
        <input type="text" name="fname" class="form-control" required>
    </div></div>
</div>

<!-- Middle Name -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Middle Name</h4>
        <input type="text" name="mname" class="form-control" required>
    </div></div>
</div>

<!-- Last Name -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Last Name</h4>
        <input type="text" name="lname" class="form-control" required>
    </div></div>
</div>

<!-- Gender -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Gender</h4>
        <select name="gender" class="custom-select" required>
            <option value="">Choose...</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Others">Others</option>
        </select>
    </div></div>
</div>

<!-- Contact -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Contact Number</h4>
        <input type="number" name="contact" class="form-control" required>
    </div></div>
</div>

<!-- Email -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Email</h4>
        <input type="email" name="email" class="form-control" required>
    </div></div>
</div>

<!-- Staff Role -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Staff Role</h4>
        <select name="role" class="custom-select" required>
            <option value="">Choose...</option>
            <option value="Warden">Warden</option>
            <option value="Accountant">Accountant</option>
            <option value="Manager">Manager</option>
        </select>
    </div></div>
</div>

<!-- Status -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Status</h4>
        <select name="status" class="custom-select" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div></div>
</div>

<!-- Address -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Address</h4>
        <textarea name="address" class="form-control" required></textarea>
    </div></div>
</div>

<!-- Password -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Password</h4>
        <input type="password" name="password" class="form-control" required>
    </div></div>
</div>

<!-- Confirm Password -->
<div class="col-md-4">
    <div class="card"><div class="card-body">
        <h4 class="card-title">Confirm Password</h4>
        <input type="password" name="cpassword" class="form-control" required>
    </div></div>
</div>

</div>

<div class="text-center">
    <button type="submit" name="submit" class="btn btn-success">Register Staff</button>
    <button type="reset" class="btn btn-danger">Reset</button>
</div>

</form>
</div>

<?php include '../includes/footer.php'; ?>

</div>
</div>

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

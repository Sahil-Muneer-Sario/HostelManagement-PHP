<?php
session_start();
include("../includes/dbconn.php");

/* STAFF LOGIN CHECK */
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

/* ONLY ACCOUNTANT STAFF ALLOWED */
if (!isset($_SESSION['staff_role']) || $_SESSION['staff_role'] !== 'Accountant') {
?>
<!DOCTYPE html>
<html>
<head>
<title>Access Denied</title>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Segoe UI", Arial;
}
body{
    background:#f1f5f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.denied-box{
    background:#fff;
    padding:40px;
    width:420px;
    border-radius:16px;
    text-align:center;
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}
.icon{
    font-size:60px;
    color:#dc2626;
    margin-bottom:15px;
}
h1{
    color:#0f172a;
    margin-bottom:10px;
}
p{
    color:#475569;
    margin-bottom:25px;
    font-size:15px;
}
a{
    display:inline-block;
    margin:6px;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
}
.back{
    background:#2563eb;
}
.logout{
    background:#dc2626;
}
a:hover{
    opacity:.9;
}
</style>
</head>
<body>

<div class="denied-box">
    <div class="icon">⛔</div>
    <h1>Access Denied</h1>
    <p>
        You do not have permission to access this page.<br>
        Only <strong>Accountant</strong> staff are allowed.
    </p>

    <a href="dashboard.php" class="back">Go Back</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

</body>
</html>
<?php
exit();
}

/* ======================
   FEE ENTRY LOGIC
====================== */

$success = "";
if (isset($_POST['save'])) {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];

    $stmt = $mysqli->prepare(
        "INSERT INTO payments (student_id, amount, month) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iis", $student_id, $amount, $month);
    $stmt->execute();

    $success = "Fee recorded successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Accountant | Fee Entry</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",Arial;}
body{
    background:#f1f5f9;
}

/* HEADER */
.header{
    background:#0f172a;
    color:#fff;
    padding:16px 30px;
    font-size:20px;
    font-weight:600;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header a{
    color:#fff;
    text-decoration:none;
    background:#2563eb;
    padding:8px 14px;
    border-radius:6px;
    font-size:14px;
}
.header a:hover{
    background:#1d4ed8;
}

/* CARD */
.container{
    max-width:500px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,.1);
}
h2{
    text-align:center;
    margin-bottom:25px;
    color:#0f172a;
}

/* FORM */
label{
    font-weight:600;
    margin-top:15px;
    display:block;
}
input{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:8px;
    border:1px solid #cbd5e1;
    font-size:15px;
}
input:focus{
    outline:none;
    border-color:#2563eb;
}
button{
    width:100%;
    margin-top:25px;
    padding:14px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}
button:hover{
    background:#1d4ed8;
}

/* SUCCESS */
.success{
    background:#ecfdf5;
    color:#065f46;
    padding:12px;
    border-left:5px solid #10b981;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}
</style>
</head>

<body>

<div class="header">
    Accountant Panel
    <div>
        <a href="view-payments.php">View Payments</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Fee Entry</h2>

    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="POST">
        <label>Student ID</label>
        <input type="number" name="student_id" required>

        <label>Amount</label>
        <input type="number" name="amount" required>

        <label>Month</label>
        <input type="text" name="month" placeholder="January" required>

        <button name="save">Save Fee</button>
    </form>
</div>

</body>
</html>

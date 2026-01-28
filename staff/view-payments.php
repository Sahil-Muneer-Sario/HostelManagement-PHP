<?php
session_start();
include("../includes/dbconn.php");

/* STAFF LOGIN CHECK */
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

/* ONLY ACCOUNTANT STAFF */
if (!isset($_SESSION['staff_role']) || $_SESSION['staff_role'] !== 'Accountant') {
    echo "Access Denied";
    exit();
}

/* FETCH PAYMENTS */
$res = $mysqli->query("SELECT * FROM payments ORDER BY paid_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>View Payments</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Segoe UI", Arial;
}
body{
    background:#f1f5f9;
}

/* HEADER */
.header{
    background:#0f172a;
    color:#fff;
    padding:16px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:18px;
    font-weight:600;
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

/* TABLE CARD */
.container{
    width:92%;
    margin:50px auto;
    background:#fff;
    padding:25px;
    border-radius:14px;
    box-shadow:0 25px 45px rgba(0,0,0,.1);
}

h2{
    text-align:center;
    margin-bottom:20px;
    color:#0f172a;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#2563eb;
    color:#fff;
    padding:12px;
}
td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
}
tr:hover{
    background:#f8fafc;
}
</style>
</head>

<body>

<div class="header">
    Accountant Panel
    <div>
        <a href="fees-entry.php">Fee Entry</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
<h2>Payment Records</h2>

<table>
<tr>
    <th>ID</th>
    <th>Student ID</th>
    <th>Amount</th>
    <th>Month</th>
    <th>Paid Date</th>
</tr>

<?php if($res && $res->num_rows > 0){ ?>
<?php while($row = $res->fetch_assoc()){ ?>
<tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['student_id']) ?></td>
    <td><?= htmlspecialchars($row['amount']) ?></td>
    <td><?= htmlspecialchars($row['month']) ?></td>
    <td><?= htmlspecialchars($row['paid_at']) ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
    <td colspan="5">No payments found</td>
</tr>
<?php } ?>

</table>
</div>

</body>
</html>

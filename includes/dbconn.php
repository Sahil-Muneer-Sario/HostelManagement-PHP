<?php
$dbuser = "root";
$dbpass = "";
$host   = "localhost";
$db     = "hostelmsphp";

$mysqli = new mysqli($host, $dbuser, $dbpass, $db);

if ($mysqli->connect_error) {
    die("Database connection failed");
}
?>

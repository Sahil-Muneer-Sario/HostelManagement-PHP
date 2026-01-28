<?php
function check_login()
{
    // Check if session ID exists and is not empty
    if(!isset($_SESSION['id']) || strlen($_SESSION['id']) == 0)
    {	
        $host = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "index.php";		
        $_SESSION["id"] = "";
        header("Location: http://$host$uri/$extra");
        exit(); // Important: stop script execution after redirect
    }
}
?>
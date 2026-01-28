<?php
session_start();
include('includes/dbconn.php');

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);
    
    $stmt = $mysqli->prepare("SELECT email, password, id FROM userregistration WHERE email=? AND password=?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($email, $password, $id);
    $rs = $stmt->fetch();
    $stmt->close();
    
    if($rs) {
        session_regenerate_id(true);
        $_SESSION['id'] = $id;
        $_SESSION['login'] = $email;

        $uid = $_SESSION['id'];
        $uemail = $_SESSION['login'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $geopluginURL = 'http://www.geoplugin.net/php.gp?ip='.$ip;
        $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
        $city = $addrDetailsArr['geoplugin_city'];
        $country = $addrDetailsArr['geoplugin_countryName'];

        $log = "INSERT INTO userLog(userId,userEmail,userIp,city,country)
                VALUES('$uid','$uemail','$ip','$city','$country')";
        $mysqli->query($log);

        header("Location: student/dashboard.php");
        exit();
    } else {
        echo "<script>alert('Sorry, Invalid Email or Password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Student Login | Hostel Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    overflow-x: hidden;
}

/* Animated gradient background */
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    position: relative;
    padding: 20px;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating shapes */
.auth-wrapper::before,
.auth-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    animation: float 20s ease-in-out infinite;
}

.auth-wrapper::before {
    width: 300px;
    height: 300px;
    top: -100px;
    right: -100px;
    animation-delay: -5s;
}

.auth-wrapper::after {
    width: 400px;
    height: 400px;
    bottom: -150px;
    left: -150px;
    animation-delay: -10s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(30px, -50px) rotate(120deg); }
    66% { transform: translate(-20px, 20px) rotate(240deg); }
}

/* Glass morphism card */
.auth-box {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    width: 100%;
    max-width: 500px;
    padding: 50px 45px;
    border-radius: 24px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-align: center;
    position: relative;
    z-index: 10;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Logo/Icon */
.logo-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 25px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Titles */
.app-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.subtitle {
    font-size: 15px;
    color: #64748b;
    margin-bottom: 40px;
    font-weight: 500;
}

/* Input container */
.input-group {
    position: relative;
    margin-bottom: 20px;
}

.input-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 18px;
    pointer-events: none;
    transition: color 0.3s;
}

/* Inputs */
.form-control {
    width: 100%;
    height: 56px;
    border-radius: 14px;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 20px 0 50px;
    font-size: 17px;
    font-weight: 500;
    color: #1e293b;
    transition: all 0.3s ease;
}

.form-control::placeholder {
    color: #94a3b8;
    font-size: 16px;
}

.form-control:focus {
    background: #ffffff;
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-control:focus + .input-icon {
    color: #667eea;
}

/* Button */
.btn-login {
    width: 100%;
    height: 54px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    font-weight: 600;
    font-size: 16px;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(102, 126, 234, 0.45);
}

.btn-login:active {
    transform: translateY(0);
}

/* Divider */
.divider {
    margin: 30px 0 25px;
    text-align: center;
    position: relative;
}

.divider::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #e2e8f0;
}

.divider span {
    position: relative;
    background: rgba(255, 255, 255, 0.95);
    padding: 0 15px;
    color: #94a3b8;
    font-size: 13px;
    font-weight: 500;
}

/* Panel links */
.panels {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.panels a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.admin {
    background: linear-gradient(135deg, #f093fb, #f5576c);
}

.staff {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
}

.panels a:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.panels a:active {
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 500px) {
    .auth-box {
        padding: 40px 30px;
    }
    
    .app-title {
        font-size: 24px;
    }
    
    .form-control,
    .btn-login {
        height: 52px;
    }

    .panels {
        flex-direction: column;
    }

    .panels a {
        width: 100%;
        justify-content: center;
    }
}

/* Add smooth transitions */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
</head>

<body>

<div class="auth-wrapper">
    <div class="auth-box">

        <div class="logo-icon">🎓</div>
        
        <h1 class="app-title">Hostel Management</h1>
        <p class="subtitle">Student Login Portal</p>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" class="form-control"
                       placeholder="Email address" required>
                <span class="input-icon">📧</span>
            </div>

            <div class="input-group">
                <input type="password" name="password" class="form-control"
                       placeholder="Password" required>
                <span class="input-icon">🔒</span>
            </div>

            <button type="submit" name="login" class="btn-login">
                Login
            </button>
        </form>

        <div class="divider">
            <span>Other Portals</span>
        </div>

        <div class="panels">
            <a href="admin/index.php" class="admin">
                <span>🔐</span>
                <span>Admin Panel</span>
            </a>
            <a href="staff/login.php" class="staff">
                <span>👔</span>
                <span>Staff Panel</span>
            </a>
        </div>

    </div>
</div>

</body>
</html>
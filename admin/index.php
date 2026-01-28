<?php
session_start();
include('../includes/dbconn.php');
if(isset($_POST['login'])){
    $username=$_POST['username'];
    $password=md5($_POST['password']);

    $stmt=$mysqli->prepare("SELECT username,email,password,id FROM admin WHERE (userName=? OR email=?) AND password=?");
    $stmt->bind_param('sss',$username,$username,$password);
    $stmt->execute();
    $stmt->bind_result($username,$username,$password,$id);
    $rs=$stmt->fetch();
    $_SESSION['id']=$id;

    if($rs){
        header("location:dashboard.php");
    } else {
        echo "<script>alert('Invalid Username/Email or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | Hostel Management</title>
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
    background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
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
    width: 350px;
    height: 350px;
    top: -120px;
    right: -120px;
    animation-delay: -5s;
}

.auth-wrapper::after {
    width: 450px;
    height: 450px;
    bottom: -180px;
    left: -180px;
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
    max-width: 480px;
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
    background: linear-gradient(135deg, #ee7752, #e73c7e);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    box-shadow: 0 10px 30px rgba(238, 119, 82, 0.4);
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

/* Form group */
.form-group {
    margin-bottom: 20px;
}

/* Input container */
.input-group {
    position: relative;
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
    border-color: #ee7752;
    box-shadow: 0 0 0 4px rgba(238, 119, 82, 0.1);
}

.form-control:focus + .input-icon {
    color: #ee7752;
}

/* Button */
.btn-block {
    width: 100%;
    height: 54px;
    border-radius: 14px;
    background: linear-gradient(135deg, #ee7752, #e73c7e);
    border: none;
    font-weight: 600;
    font-size: 16px;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    box-shadow: 0 8px 20px rgba(238, 119, 82, 0.35);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.btn-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(238, 119, 82, 0.45);
}

.btn-block:active {
    transform: translateY(0);
}

/* Divider */
.divider {
    margin: 30px 0;
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

/* Back link */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 25px;
    font-size: 14px;
    text-decoration: none;
    color: #ee7752;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-link:hover {
    gap: 10px;
    color: #e73c7e;
}

/* Security badge */
.security-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
    padding: 10px 18px;
    background: rgba(238, 119, 82, 0.1);
    border-radius: 12px;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.security-badge::before {
    content: '🔒';
    font-size: 16px;
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
    .btn-block {
        height: 52px;
    }
}

/* Utility classes */
.mt-3 { margin-top: 20px; }
.mt-4 { margin-top: 25px; }
.text-center { text-align: center; }

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

        <div class="logo-icon">⚙️</div>
        
        <h1 class="app-title">Hostel Management</h1>
        <p class="subtitle">Admin Control Panel</p>

        <form method="POST">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="username" class="form-control"
                           placeholder="Email or Username" required>
                    <span class="input-icon">👤</span>
                </div>
            </div>

            <div class="form-group mt-3">
                <div class="input-group">
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter your password" required>
                    <span class="input-icon">🔑</span>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="login" class="btn-block">
                    Login to Dashboard
                </button>
            </div>

            <div class="security-badge">
                Secure Admin Access
            </div>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="text-center">
                <a href="../index.php" class="back-link">← Return to Home</a>
            </div>
        </form>

    </div>
</div>

</body>
</html>
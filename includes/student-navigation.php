<?php
// Emergency check: If this menu was already loaded, stop here.
if (defined('NAV_LOADED')) {
    return;
}
define('NAV_LOADED', true);
?>

<nav class="navbar top-navbar navbar-expand-md">
    <div class="navbar-header" data-logobg="skin6">
        <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
        <div class="navbar-brand">
            <a href="dashboard.php">
                <b class="logo-icon">
                    <!-- <img src="../assets/images/logo-icon-nav.png" alt="homepage" class="dark-logo" /> -->
                    <img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" width="40" height="40" />
                     
                </b>
                <span class="logo-text">
                    <img src="../assets/images/logo-text-nav.png" alt="homepage" class="dark-logo" />
                    <!-- <img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" width="40" height="40" /> -->

                </span>
            </a>
        </div>
        <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent"><i class="ti-more"></i></a>
    </div>

    <div class="navbar-collapse collapse" id="navbarSupportedContent">
        <ul class="navbar-nav float-left mr-auto ml-3 pl-1"></ul>
        <ul class="navbar-nav float-right">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">
                    <img src="../assets/images/users/user-icn.png" alt="user" class="rounded-circle" width="40">
                    <?php   
                        $aid = $_SESSION['id'];
                        $ret = "SELECT firstName FROM userregistration WHERE id=? LIMIT 1";
                        $stmt = $mysqli->prepare($ret);
                        $stmt->bind_param('i', $aid);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if($row = $res->fetch_object()) { 
                    ?>   
                    <span class="ml-2 d-none d-lg-inline-block">
                        <span>Hello,</span> 
                        <span class="text-dark"><?php echo $row->firstName; ?></span> 
                        <i data-feather="chevron-down" class="svg-icon"></i>
                    </span>
                    <?php } ?> 
                </a>
                <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                    <a class="dropdown-item" href="profile.php"><i data-feather="user" class="svg-icon mr-2 ml-1"></i> My Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php"><i data-feather="power" class="svg-icon mr-2 ml-1"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
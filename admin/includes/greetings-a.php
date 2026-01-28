<?php 
 
    date_default_timezone_set('Asia/Karachi');
    
    // Get current hour (0-23)
    $numeric_date = date("G"); 
    
    // Determine greeting based on time of day
    if($numeric_date >= 0 && $numeric_date < 12) {
        $welcome_string = "Good Morning,";
    } 
    else if($numeric_date >= 12 && $numeric_date < 17) {
        $welcome_string = "Good Afternoon,";
    } 
    else if($numeric_date >= 17 && $numeric_date < 21) {
        $welcome_string = "Good Evening,";
    }
    else {
        // 21:00 to 23:59
        $welcome_string = "Good Night,";
    }
    

    $aid = $_SESSION['id'];
    $ret = "SELECT * FROM admin WHERE id=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $aid);
    $stmt->execute();
    $res = $stmt->get_result();
                                        
    while($row = $res->fetch_object()) {
        echo "<h3 class='page-title text-truncate text-dark font-weight-medium mb-1'>$welcome_string $row->username!</h3>";
    }
 
?>
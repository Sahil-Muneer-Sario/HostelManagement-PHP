<?php 
    date_default_timezone_set('America/Chicago');
    
    $welcome_string="Welcome"; 
    $numeric_date=date("G"); 
    
    if($numeric_date>=0&&$numeric_date<=11) 
        $welcome_string="Good Morning,"; 
    else if($numeric_date>=12&&$numeric_date<=17) 
        $welcome_string="Good Afternoon,"; 
    else if($numeric_date>=18&&$numeric_date<=23) 
        $welcome_string="Good Evening,"; 

    // Ensure session ID exists
    if(isset($_SESSION['id'])) {
        $aid=$_SESSION['id'];
        // Added LIMIT 1 to ensure only one record is pulled
        $ret="SELECT firstName FROM userregistration WHERE id=? LIMIT 1";
        $stmt= $mysqli->prepare($ret);
        $stmt->bind_param('i',$aid);
        $stmt->execute();
        $res=$stmt->get_result();
        
        // Changed 'while' to 'if' so it only runs once
        if($row=$res->fetch_object()) {
            echo "<h3 class='page-title text-truncate text-dark font-weight-medium mb-1'>$welcome_string $row->firstName! </h3>"; 
        }
    }
?>
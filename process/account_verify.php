<?php
    require_once '../require/config.php';
    require_once '../require/functions.php';

    //Get the code from URL
    if(isset($_GET['code'])){
        
        $reset_code = test_input($_GET['code']);
        
        $sql = "SELECT * FROM users WHERE reset_code = '$reset_code'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $row = $result->fetch_assoc();
            $sql = "UPDATE users SET is_active = 1, reset_code = '' WHERE reset_code = '$reset_code'";
            
            echo 'We are activating your account...';
                
            if ($conn->query($sql) === TRUE) {
                $msg = 'Your account has been activated. You can now login';
                header("Location: ../login.php?message=$msg");
            } else {
                echo "Error updating record: " . $conn->error;
            }
                
        } else {
            echo "0 results";
        }
    }
?>
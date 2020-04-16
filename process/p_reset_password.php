<?php
    session_start();
    
    require_once '../require/config.php';
    require_once '../require/functions.php';

    $count = 0;
    $msg = '';

    if(isset($_GET['code'])){
        $reset_code = test_input($_GET['code']);
        
        //Select user with this code
        $sql = "SELECT * FROM users WHERE reset_code = '$reset_code'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['reset_code'] = $row['reset_code'];
            header("Location: ../reset_password.php");
            exit();
        } else {
            echo "0 results";
        }
    } else {// End of IF
        //Select user with this code
        $sql = "SELECT * FROM users WHERE reset_code = '$reset_code'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['reset_code'] = $row['reset_code'];
            header("Location: ../reset_password.php");
            exit();
        } else {
            echo "0 results";
        }
    } 
?>
<?php
    session_start();
    
    require_once 'require/config.php';
    require_once 'require/functions.php';

    //Get the code from session
    $reset_code = $_SESSION['reset_code'];

    // define variables and set to empty values
    $is_active = $password = $confirm_password = "";
    $passwordErr = $confirm_passwordErr = "";

    $count = 0;
    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $password = test_input($_POST["password"]);
        $confirm_password = test_input($_POST["confirm_password"]);
        
        //Validating our password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $count++;
        } else {
            $password = test_input($_POST["password"]);
        }
        
        //Validating our confirm password
        if (empty($_POST["confirm_password"])) {
            $confirm_passwordErr = "Please confirm your password";
            $count++;
        } else {
            $confirm_password = test_input($_POST["confirm_password"]);
            //Check if passwords match
            if($confirm_password != $password){
                $confirm_passwordErr = "Password does not match";
                $confirm_password = "";
                $count++;
            } else {
                $confirm_password = test_input($_POST["confirm_password"]);
            }
        }
        
        //If we are free of errors
        if($count == 0){
            //Getting information from user with a proper reset_code
            $sql = "SELECT * FROM users WHERE reset_code = '$reset_code'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                //If the user is verified
                if($row['is_active'] == 1){
                    //hashing the password before inserting it into database
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    //Update the user password and delete the reset code
                    $sql = "UPDATE users SET password = '$hashed_password', reset_code = '' WHERE reset_code = '$reset_code'";
                    
                    if ($conn->query($sql) === TRUE) {
                    $msg = 'Your password has been reset';
                    header("Location: login.php?message=$msg");
                    //Unset the reset_code variable
                    session_unset();
                    exit();
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    //hashing the password before inserting it into database
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    //Update the user password only
                    $sql = "UPDATE users SET password = '$hashed_password' WHERE reset_code = '$reset_code'";

                    if ($conn->query($sql) === TRUE) {
                        $msg = 'Your password has been reset';
                        header("Location: login.php?message=$msg");
                        //Unset the reset_code variable
                        session_unset();
                        exit();
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                }
            } else {
                echo "0 results";
            }
        }
    }//End of IF
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Complete_Authentication_System_new</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Basic.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/smoothproducts.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-lg fixed-top bg-white text-monospace clean-navbar">
        <div class="container"><a class="navbar-brand logo" href="index.php">Home</a><button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item item" role="presentation"><a class="nav-link" href="register.php">Register</a></li>
                    <li class="nav-item item" role="presentation"><a class="nav-link" href="login.php">login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page login-page">
        <section class="clean-block clean-form dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-size: cover;background-repeat: no-repeat;background-position: center;">
            <div class="container">
                <div class="text-monospace block-heading">
                    <h2 class="text-info">Reset password</h2>
                    <p>Please fill the credentials to reset your password</p>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <input class="form-control" type="password" name="password" placeholder="Enter your new password" value="<?php echo $password; ?>">
                        <span class="error form-text"><?php echo $passwordErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="confirm_password" placeholder="Confirm your new password">
                        <span class="error form-text"><?php echo $confirm_passwordErr; ?></span>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit" name="submit">Reset password</button>
                </form>
            </div>
        </section>
    </main>
    <div class="footer-basic">
        <footer>
            <div class="social"><a href="#"><i class="icon ion-social-instagram"></i></a><a href="#"><i class="icon ion-social-snapchat"></i></a><a href="#"><i class="icon ion-social-twitter"></i></a><a href="#"><i class="icon ion-social-facebook"></i></a></div>
            <ul class="list-inline text-monospace">
                <li class="list-inline-item"><a href="#">Home</a></li>
                <li class="list-inline-item"><a href="#">Services</a></li>
                <li class="list-inline-item"><a href="#">About</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
            </ul>
            <p class="copyright">Varnas Spiros © 2018</p>
        </footer>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.js"></script>
    <script src="assets/js/smoothproducts.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>
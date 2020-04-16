<?php
    require_once 'require/config.php';
    require_once 'require/functions.php';
    require_once 'libraries/PHPMailer-master/PHPMailerAutoload.php';
    
    // define email variable and set to empty value
    $reset_code = $is_active = $email = $emailErr = "";

    $count = 0;
    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $email = test_input($_POST["email"]);
        
        //Validating our email
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $count++;
        } else {
            $email = test_input($_POST["email"]);
            
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $count++;
            } else {
                //check if email exist
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = $conn->query($sql);
                
                if ($result->num_rows == 0) {
                    $emailErr = "Email not found";
                    $email = "";
                    $count++;
                } else {
                    //Store the is_active and reset_code variable for the email use
                    $row = $result->fetch_assoc();
                    $is_active = $row['is_active'];
                    $reset_code = $row['reset_code'];
                }
            }
        }
        
        //If we are free of errors
        if ($count == 0){
            //If account is verified
            if($is_active == 1) {
                //Generate a unique code
                $reset_code = md5(crypt(rand(), 'aa'));
                //Update the database delete password and insert the new reset_code
                $sql = "UPDATE users SET password = '', reset_code = '$reset_code' WHERE email = '$email'";
                
                if ($conn->query($sql) === TRUE) {
                    
                    $msg = 'You made a password request, please check email to reset your password';

                    $message = "You requested a password reset. Click the link below to reset your password. <br><br> 
                    <a href='http://localhost/complete_authentication/process/p_reset_password.php?code=$reset_code'>Click here to reset your password</a>";

                    //sending email to the user
                    send_mail($email, $message);

                    $email = $emailErr = "";
                    
                } else {
                    echo "Error updating record: " . $conn->error;
                }
                
            } else {
                //Update the database delete the password only
                $sql = "UPDATE users SET password = '' WHERE email = '$email'";
                if ($conn->query($sql) === TRUE) {
                    $msg = 'You made a password request, please check email to reset your password';
                    
                    $message = "You requested a password reset. Click the link below to reset your password. <br><br> 
                    <a href='http://localhost/complete_authentication/process/p_reset_password.php?code=$reset_code'>Click here to reset your password</a>";
                    
                    //sending email to the user
                    send_mail($email, $message);

                    $email = $emailErr = "";
                    
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
        }
    }// End of IF
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
                    <li class="nav-item" role="presentation"><a class="nav-link" href="registration.php">Register</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page login-page">
        <section class="clean-block clean-form dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-size: cover;background-repeat: no-repeat;background-position: center;">
            <div class="container">
                <div class="text-monospace block-heading">
                    <h2 class="text-info">Forgot your password?</h2>
                    <p>Please fill the credentials to reset your password</p>
                    <?php 
                        if($msg != ''){
                            echo '<hr>';
                            echo '<div class="alert alert-warning" role="alert">';
                            echo  $msg;
                            echo '</div>';
                        }
                    ?>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <input class="form-control item" type="email" name="email" placeholder="Enter your email">
                        <span class="error form-text"><?php echo $emailErr; ?></span>
                        <small class="form-text text-muted">We will send you a reset code.</small>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Reset my password</button>
                    <a
                        class="text-monospace form-text text-muted" href="login.php" style="margin-top: 20px;font-size: 14px;">Go back to login</a>
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
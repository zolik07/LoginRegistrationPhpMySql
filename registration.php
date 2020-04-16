<?php
    
    require_once 'require/config.php';
    require_once 'require/functions.php';
    require_once 'libraries/PHPMailer-master/PHPMailerAutoload.php';

    // define variables and set to empty values
    $name = $username = $email = $website = $password = $confirm_password = "";
    $nameErr = $usernameErr = $emailErr = $websiteErr = $passwordErr = $confirm_passwordErr = "";

    $count = 0;
    $msg = '';
    
    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $name = test_input($_POST["name"]);
        $username = test_input($_POST["username"]);
        $email = test_input($_POST["email"]);
        $website = test_input($_POST["website"]);
        $password = test_input($_POST["password"]);
        $confirm_password = test_input($_POST["confirm_password"]);
        
        //Validating our name
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
            $count++;
        } else {
            $name = test_input($_POST["name"]);
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
                $nameErr = "Only letters and white space allowed"; 
                $count++;
            }
        }
        
        //Validating our username
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
            $count++;
        } else {
            $username = test_input($_POST["username"]);
            //check if email exist
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $usernameErr = "Username already exists in datbase";
                $username = "";
                $count++;
            }
        }
        
        //Validating our email
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $count++;
        } else {
            $email = test_input($_POST["email"]);
            
            //check if email exist
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $emailErr = "Email already exists in datbase";
                $email = "";
                $count++;
            } else {
                // check if e-mail address is well-formed
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Invalid email format";
                    $count++;
                }
            }
        }
        
        //Validating our website
        if (empty($_POST["website"])) {
            $websiteErr = "Website is required";
            $count++;
        } else {
            $website = test_input($_POST["website"]);
            // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
                $websiteErr = "Invalid URL. You need to provide www. before your domain name"; 
                $count++;
            }
        }
        
        //Validating our password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $count++;
        } else {
            $password = test_input($_POST["password"]);
        }
        
        if (empty($_POST["confirm_password"])) {
            $confirm_passwordErr = "Please confirm your password";
            $count++;
        } else {
            $confirm_password = test_input($_POST["confirm_password"]);
            //Check if the confirm password match the password
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

            //hashing the password before inserting it into database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            //generating a random reset code
            $reset_code = md5(crypt(rand(), 'aa'));
            
            //Inserting data into database
            $sql = "INSERT INTO users (name, email, username, password, website, created_at, reset_code, is_active)
            VALUES ('$name', '$email', '$username', '$hashed_password', '$website', " . time() . ", '$reset_code', 0)";

            if ($conn->query($sql) === TRUE) {
                $msg = 'You account has been created successfully. Click <a href="https://mail.google.com" target="external" class="alert-link">here</a> to verify your account';
                
                $message = "You have beed registered successfully. Click the link below to verify your accout: <br><br> 
                <a href='http://localhost/complete_authentication/process/account_verify.php?code=$reset_code'>Click here to verify</a>";
                
                //sending email to the user
                send_mail($email, $message);
                
                $name = $username = $email = $website = $password = $confirm_password = '';
            } else {
                //echo "Error: " . $sql . "<br>" . $conn->error;
                echo 'Something went wrong';
            }
        }
    }//End of IF
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Register - Complete authentication</title>
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
        <div class="container">
            <a class="navbar-brand logo" href="index.php">Home</a>
            <button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" href="registration.php">Register</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page registration-page">
        <section class="clean-block clean-form dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-repeat: no-repeat;background-size: cover;background-position: center;">
            <div class="container" style="background-image: url(&quot;none&quot;);">
                <div class="text-monospace block-heading">
                    <h2 class="text-info">Registration</h2>
                    <p>Please fill the credentials to register</p>
                    <?php 
                        if($msg != ''){
                            echo '<hr>';
                            echo '<div class="alert alert-success" role="alert">';
                            echo  $msg;
                            echo '</div>';
                        }
                    ?>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <input class="form-control item" type="text" name="name" placeholder="Enter your full name" value="<?php echo $name; ?>">
                        <span class="error form-text"><?php echo $nameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control item" type="text" name="username" placeholder="Enter your username" value="<?php echo $username; ?>">
                        <span class="error form-text"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control item" type="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>">
                        <span class="error form-text"><?php echo $emailErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control item" type="text" name="website" placeholder="Enter your website" value="<?php echo $website; ?>">
                        <span class="error form-text"><?php echo $websiteErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control item" type="password" name="password" placeholder="Enter your password" value="<?php echo $password; ?>">
                        <span class="error form-text"><?php echo $passwordErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control item" type="password" name="confirm_password" placeholder="Confirm your password" value="<?php echo $confirm_password; ?>">
                        <span class="error form-text"><?php echo $confirm_passwordErr; ?></span>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Sign Up</button>
                    <a href="login.php" class="form-text text-muted" style="margin-top: 20px;font-size: 14px;">Already have an account? Login here.</a>
                </form>
            </div>
        </section>
    </main>
    <div class="footer-basic">
        <footer>
            <div class="social">
                <a href="#">
                    <i class="icon ion-social-instagram"></i>
                </a>
                <a href="#">
                    <i class="icon ion-social-snapchat"></i>
                </a>
                <a href="#">
                    <i class="icon ion-social-twitter">
                    </i>
                </a>
                <a href="#">
                    <i class="icon ion-social-facebook"></i>
                </a>
            </div>
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
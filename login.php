<?php 

    session_start();
    
    require_once 'require/config.php';
    require_once 'require/functions.php';
    
    //Chekc if we are already logged in to prevent redirections
    if(isset($_SESSION['id'])){
        header("Location: profile.php");
    }

    // define variables and set to empty values
    $username = $password = "";
    $usernameErr = $passwordErr = "";

    //Define cookie variables
    $cookie_username = "username";
    $cookie_password = "password";

    $count = 0;
    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $username = test_input($_POST["username"]);
        $password = test_input($_POST["password"]);
        
        //Validating our username
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
            $count++;
        } else {
            $username = test_input($_POST["username"]);
        }
        
        //Validating our password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $count++;
        } else {
            $password = test_input($_POST["password"]);
        }
        
        //Check if we are free of errors
        if($count == 0){
            
            //check if this user exists in the database
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($sql);
            
            //if data matches
            if($result->num_rows > 0) {
                
                // output data
                $row = $result->fetch_assoc();
                //If the user is verified
                if ($row['is_active'] == 1) {
                    
                    //Check if passwords match
                    if(password_verify($password, $row['password'])) {
                        //Set up cookie files to store username and password
                        if (isset($_POST['checkbox'])){
                            setcookie("username", $username, time() + (86400 * 30), "/");
                            setcookie("password", $password, time() + (86400 * 30), "/");
                        } 
                        //Setting our SESSION variables
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['password'] = $row['password'];
                        $_SESSION['website'] = $row['website'];
                        $_SESSION['image'] = $row['image'];
                        $_SESSION['created_at'] = $row['created_at'];
                        header ("Location: profile.php");
                        exit();

                    } else {
                        $passwordErr = 'Wrong password. Please try again';
                        $password = "";
                        $count++;
                    }
                } else {
                    $msg = 'You need to verify your account before you login';
                    $count++;
                }
            } else {
                $msg = "There is no account with this username in the database";
                $username = $password = "";
                $count++;
            }
        }
    } // End of IF
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - Brand</title>
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
                    <li class="nav-item" role="presentation"><a class="nav-link" href="registration.php">Register</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link active" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page login-page">
        <section class="clean-block clean-form dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-size: cover;background-repeat: no-repeat;background-position: center;">
            <div class="container">
                <div class="text-monospace block-heading">
                    <h2 class="text-info">Log In</h2>
                    <p>Please fill the credentials to login</p>
                    <?php 
                        if($msg != ''){
                            echo '<hr>';
                            echo '<div class="alert alert-danger" role="alert">';
                            echo  $msg;
                            echo '</div>';
                        } else if (isset($_GET['message'])){
                            echo '<hr>';
                            echo '<div class="alert alert-success" role="alert">';
                            echo  $_GET['message'];
                            echo '</div>';
                        }
                    ?>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <?php 
                            if(!isset($_COOKIE[$cookie_username])){
                                echo '<input class="form-control item" type="text" name="username" placeholder="Enter your username" value="' . $username . '">';
                                echo '<span class="error form-text"> ' . $usernameErr . '</span>';
                            } else {
                                echo '<input class="form-control item" type="text" name="username" placeholder="Enter your username" value="' . $_COOKIE[$cookie_username] . '">';
                                echo '<span class="error form-text">' . $usernameErr . '</span>';
                            }
                        ?>
                    </div>
                    <div class="form-group">
                        <?php 
                            if(!isset($_COOKIE[$cookie_password])){
                                echo '<input class="form-control item" type="password" name="password" placeholder="Enter your password" value="' . $password . '">';
                                echo '<span class="error form-text">'. $passwordErr . '</span>';
                            } else {
                                echo '<input class="form-control item" type="password" name="password" placeholder="Enter your password" value="' . $_COOKIE[$cookie_password] . '">';
                                echo '<span class="error form-text">' . $passwordErr . '</span>';
                            }
                        ?>
                    </div>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="checkbox"><label class="form-check-label" for="checkbox">Remember me</label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Log In</button>
                    <a href="forgot_password.php" class="form-text text-muted" style="font-size: 14px;margin-top: 20px;">Forgot your password?</a>
                    <a href="registration.php" class="form-text text-muted" style="font-size: 14px;">Don't you have an accout? Register here.</a>
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
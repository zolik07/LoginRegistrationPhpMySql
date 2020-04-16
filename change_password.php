<?php
    session_start();

    require_once 'require/config.php';
    require_once 'require/functions.php';

    // define id variable and set to session value
    $id = $_SESSION['id'];

    // define variables and set to empty values
    $old_password = $new_password = $new_confirm_password = "";
    $old_passwordErr = $new_passwordErr = $new_confirm_passwordErr = "";

    $count = 0;
    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $old_password = test_input($_POST["old_password"]);
        $new_password = test_input($_POST["new_password"]);
        $new_confirm_password = test_input($_POST["new_confirm_password"]);
        
        //Validate the old password
        if (empty($_POST["old_password"])) {
            $old_passwordErr = "Old password is required";
            $count++;
        } else {
            $old_password = test_input($_POST["old_password"]);
            
            //Check if the old password is the correct one
            $sql = "SELECT password FROM users WHERE id = '$id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                //If passwords don't match
                if(!password_verify($old_password, $row['password'])){
                    $old_passwordErr = "Your password is not correct";
                    $old_password = '';
                    $count++;
                }
            } else {
                echo "0 results";
            }
        }
        
        //Validate the new password
        if (empty($_POST["new_password"])) {
            $new_passwordErr = "New password is required";
            $count++;
        } else {
            $new_password = test_input($_POST["new_password"]);
            //If the password is the same as the current password
            if($new_password == $old_password){
                $new_passwordErr = "New password can't be same like the old password";
                $new_password = "";
                $count++;
            } else {
                $new_password = test_input($_POST["new_password"]);
            }
        }
        
        //Validate the confirm password
        if (empty($_POST["new_confirm_password"])) {
            $new_confirm_passwordErr = "Please confirm your password";
            $count++;
        } else {
            $new_confirm_password = test_input($_POST["new_confirm_password"]);
            //If the passwords are not the same
            if($new_confirm_password != $new_password){
                $new_confirm_passwordErr = "Password does not match";
                $new_confirm_password = "";
                $count++;
            } else {
                $new_confirm_password = test_input($_POST["new_confirm_password"]);
            }
        }
        
        //If we are free of errors
        if($count == 0){
            //hashing the password before inserting it into database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            //Update information in the database
            $sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                $msg = "Your password has been changed successfully";
                header("Location: profile.php?message=$msg");
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
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
                    <li class="nav-item item" role="presentation"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item item" role="presentation"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page login-page">
        <section class="clean-block clean-form dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-size: cover;background-repeat: no-repeat;background-position: center;">
            <div class="container">
                <div class="text-monospace block-heading">
                    <h2 class="text-info">Change password</h2>
                    <p>Please fill the credentials to change your password</p>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <input class="form-control" type="password" name="old_password" value="<?php echo $old_password; ?>" placeholder="Enter your old password">
                        <span class="error form-text"><?php echo $old_passwordErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="new_password" value="<?php echo $new_password; ?>" placeholder="Enter your new password">
                        <span class="error form-text"><?php echo $new_passwordErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="new_confirm_password" value="<?php echo $new_confirm_password; ?>" placeholder="Confirm your new password">
                        <span class="error form-text"><?php echo $new_confirm_passwordErr; ?></span>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Change password</button>
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
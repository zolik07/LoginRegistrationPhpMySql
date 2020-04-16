<?php 
    session_start();

    require_once 'require/config.php';
    require_once 'require/functions.php';

    // define id variable and set to session value
    $id = $_SESSION['id'];
    
    // define variables and set to empty values
    $name = $username = $email = $website = "";
    $nameErr = $usernameErr = $emailErr = $websiteErr = "";

    $count = 0;
    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $name = test_input($_POST["name"]);
        $username = test_input($_POST["username"]);
        $email = test_input($_POST["email"]);
        $website = test_input($_POST["website"]);
        
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
        }
        
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
        
        //If we are free of errors
        if($count == 0){
            //Update information in the database
            $sql = "UPDATE users SET name = '$name', username = '$username', email = '$email', website = '$website' WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                $msg = "Information updated successfully";
                header("Location: profile.php?message=$msg");
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }// END of IF
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
                    <h2 class="text-info">Edit profile information</h2>
                    <p>Here you can change your profile information</p>
                </div>
                <?php 
                    $sql = "SELECT * FROM users WHERE id = '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        $row = $result->fetch_assoc();
                        $name = $row['name'];
                        $username = $row['username'];
                        $email = $row['email'];
                        $website = $row['website'];
                    } else {
                        echo "0 results";
                    }
                ?>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <input class="form-control" type="text" name="name" value="<?php echo $name; ?>" placeholder="Change your name">
                        <span class="error form-text"><?php echo $nameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="username" value="<?php echo $username; ?>" placeholder="Change your username">
                        <span class="error form-text"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" value="<?php echo $email; ?>" placeholder="Change your email">
                        <span class="error form-text"><?php echo $emailErr; ?></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="website" value="<?php echo $website; ?>" placeholder="Change your website">
                        <span class="error form-text"><?php echo $websiteErr; ?></span>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Update profile</button>
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
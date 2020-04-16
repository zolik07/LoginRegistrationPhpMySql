<?php 
    session_start();

    require_once 'require/config.php';
    require_once 'require/functions.php';
    
    //Check if the user is not logged in
    if(!isset($_SESSION['id'])){
        header("Location: login.php");
    }

    //Define variables and set them to empty values
    $name = $username = $email = $website = $created_at = '';

    // define id variable and set to session value
    $id = $_SESSION['id'];

    $msg = '';

    //Submitting the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Delete account
        $sql = "DELETE FROM users WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            $msg = "You account has been deleted";
            //Unset and delete the user information
            session_unset();
            session_destroy();
            
            //Destroy the username cookie
            if(isset($_COOKIE['username'])){
                setcookie("username", "", time() - (86400 * 30));
            }
            //Destroy the password cookie
            if(isset($_COOKIE['password'])){
                setcookie("password", "", time() - (86400 * 30));
            }
            header("Location: login.php?message=$msg");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
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
        <div class="container">
            <a class="navbar-brand logo" href="index.php">Home</a>
            <button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item item" role="presentation"><a class="nav-link active" href="profile.php">Profile</a></li>
                    <li class="nav-item item" role="presentation"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page login-page">
        <section class="text-monospace clean-block clean-info dark" style="background-image: url(&quot;assets/img/scenery/image1.jpg&quot;);background-position: center;background-size: cover;">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Profile Information</h2>
                    <p>Here you can view your profile information and you can also edit them</p>
                    <?php
                        if (isset($_GET['message'])){
                            echo '<hr>';
                            echo '<div class="alert alert-success" role="alert">';
                            echo  $_GET['message'];
                            echo '</div>';
                        }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php 
                            $sql = "SELECT image FROM users WHERE id = '$id'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $image = $row['image'];
                                if ($image == ''){
                                    echo '<div class="image-box">';
                                    echo    '<div class="img" style="background-image: url(&quot;images/profil_auto.jpg&quot;);"></div>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="image-box">';
                                    echo    '<div class="img" style="background-image: url(&quot;images/' . $image . '&quot;);"></div>';
                                    echo '</div>';
                                }
                            } else {
                                echo "0 results";
                            }
                        ?>
                    </div>
                    <div class="col-md-6">
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
                                $created_at = $row['created_at'];
                            } else {
                                echo "0 results";
                            }
                        ?>
                        <h3><?php echo $name; ?></h3>
                        <hr>
                        <div class="getting-started-info">
                            <p>Username: <strong><?php echo $username; ?></strong></p>
                            <p>Email: <strong><?php echo $email; ?></strong>&nbsp;</p>
                            <p>Website: <strong><?php echo $website; ?></strong>&nbsp;</p>
                        </div>
                        <hr>
                        <p>Created at: <strong><?php echo(date("d-m-Y",$created_at)); ?></strong></p>
                        <hr>
                        <div class="row">
                            <div class="col text-left">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-dark text-center border rounded shadow-lg d-xl-flex" role="button" href="upload_image.php" data-toggle="tooltip" data-placement="top" title="Upload new image">
                                        <i class="fa fa-file-picture-o d-xl-flex" style="margin-right: 0px;"></i>
                                    </a>
                                    <a class="btn btn-dark border rounded shadow-lg d-xl-flex" role="button" href="edit_profile.php" data-toggle="tooltip" data-placement="top" title="Edit profile">
                                        <i class="fa fa-edit d-xl-flex" style="margin-right: 0px;"></i>
                                    </a>
                                    <a class="btn btn-dark border rounded shadow-lg d-xl-flex" role="button" href="change_password.php" data-toggle="tooltip" data-placement="top" title="Change password">
                                        <i class="fa fa-unlock-alt d-xl-flex" style="margin-right: 0px;"></i>
                                    </a>
                                </div>
                                
                            </div>
                            <div class="col text-right">
                                <a class="btn btn-dark text-center border rounded shadow-lg " role="button" href="#" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fa fa-power-off d-xl-flex" style="margin-right: 0px;"></i>
                                </a>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Delete your account</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        Do you really want to delete your account?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                            <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                                  <input type="submit" value='Yes' class="btn btn-primary">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script src="assets/js/js.js"></script>
</body>

</html>
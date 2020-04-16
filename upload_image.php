<?php
    session_start();

    require_once 'require/config.php';
    require_once 'require/functions.php';

    $id = $_SESSION['id'];

    $msg = '';
    $success = 0;

    // Check if image file is a actual image or fake image
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $file = $_FILES['file'];
        $filename = $_FILES['file']['name'];
        $fileTmpname = $_FILES['file']['tmp_name'];
        $fileError = $_FILES['file']['error'];
        $fileSize = $_FILES['file']['size'];
        
        //Explode file name with the extension
        $fileExt = explode('.', $filename);
        //Transform everything to lowercase
        $fileActualExt = strtolower(end($fileExt));
        
        $allowed = array('jpg', 'jpeg', 'png');
        
        //If extension is inside the array
        if(in_array($fileActualExt, $allowed)){
            //if we are free of errors
            if($fileError === 0){
                //If the file size is lesser that 10000000KBs
                if($fileSize < 10000000){
                    //Generate a unique name based in nanoseconds
                    $fileNameNew = uniqid('', true).".".$fileActualExt;
                    //Define the destination of the file to be stored
                    $fileDestination = 'images/'.$fileNameNew;
                    //Upload the image to destination
                    if(move_uploaded_file($fileTmpname, $fileDestination)){
                        $success = 1;
                        //Update database image column with the fileNameNew
                        $sql = "UPDATE users SET image = '$fileNameNew' WHERE id = '$id'";
                        if ($conn->query($sql) === TRUE) {
                            $msg = "Your image has been changed";
                            header("Location: profile.php?message=$msg");
                            exit();
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    } else {
                        $success = 0;
                        $msg = "Your file failed to upload";
                    }
                } else {
                    $success = 0;
                    $msg = "Your file is too large";
                }
            } else {
                $success = 0;
                $msg = "There was an error uploading your file";
            }
        } else {
            $success = 0;
            $msg = "You cannot upload files of this type";
        }
    }
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
                    <h2 class="text-info">Upload image</h2>
                    <p>Select an image to upload</p>
                    <?php 
                        if($success == 1){
                            if($msg != ''){
                                echo '<hr>';
                                echo '<div class="alert alert-success" role="alert">';
                                echo  $msg;
                                echo '</div>';
                            }
                        } else {
                            if($msg != ''){
                                echo '<hr>';
                                echo '<div class="alert alert-danger" role="alert">';
                                echo  $msg;
                                echo '</div>';
                            }
                        }
                    ?>
                </div>
                <form class="text-monospace" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <input class="form-control" type="file" name="file">
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Upload image</button>
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
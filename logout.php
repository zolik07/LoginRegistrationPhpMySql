<?php
    //Unset and destroy all sessions so the user is not logged in
    session_start();
    session_unset();
    session_destroy();
    header('Location: login.php');
?>
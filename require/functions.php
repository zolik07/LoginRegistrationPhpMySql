<?php

//Test form inputs function
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Send email function
function send_mail($email, $message){

    $mail = new PHPMailer(true);                                // Passing `true` enables exceptions
    try {
        //Server settings
        //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                        // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                         // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                 // Enable SMTP authentication
        $mail->Username = 'your username';                 // SMTP username
        $mail->Password = 'your password';                       // SMTP password
        $mail->SMTPSecure = 'tls';                              // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                      // TCP port to connect to

        //Recipients
        $mail->setFrom('no-reply@info.com', 'Complete Auth System');
        $mail->addAddress($email);

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                    // Set email format to HTML
        $mail->Subject = 'Account Verification';
        $mail->Body = $message;
        //$mail->Body    = "You have beed registered successfully. Click the link below to verify your accout: <br><br>
        //                <a href='http://localhost/ExtensionApp_Vol_2/confirmation_lec.php?email=$email'>Click here to verify</a>";
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}

?>
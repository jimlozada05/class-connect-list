<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';

//Load sql functions
require_once '../dbconnect.php';

if (isset($_POST['email_value'])) {
    $email = $_POST['email_value'];
    $email_result = checkEmail($email);
    if ($email_result == NULL) {
        echo "Email does not exist.";
        exit;
    }
    $email_layout = file_get_contents('email-layout.html');
    $otp = bin2hex(random_bytes(3));
    $email_layout = str_replace('$otp', $otp, $email_layout);

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'classconnectlist@gmail.com';                     //SMTP username
        $mail->Password   = 'zveonycdxskxhzny';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('classconnectlist@gmail.com', 'CC:List');
        $mail->addAddress($email);     //Add a recipient
        // $mail->addAddress('ellen@example.com');               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'New Password One-Time Pin';
        $mail->Body    = $email_layout;
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        insertOTP($otp, $email);
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['verify_otp'];
    $email = $_POST['email'];
    $verify_otp = getOTP($email);
    if (!empty($verify_otp['otp'])) {
        deleteOTP($email);
        $email_result = checkEmail($_POST['email']);
        session_start();
        $_SESSION['user_id'] = $email_result['email'];
        echo $email;
    } else {
        echo "Fail";
    }
}

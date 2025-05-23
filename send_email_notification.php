<?php

require __DIR__ . '/vendor/autoload.php';  

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmailToParent($toEmail, $toName, $subject, $bodyContent) {
    $mail = new PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'enxi6387@gmail.com';
        $mail->Password = 'lroa ywhk lldq fwzt'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // sender
        $mail->setFrom('enxi6387@gmail.com', 'The Seeds Learning Tuition Centre');

        // receiver
        $mail->addAddress($toEmail, $toName);

        // content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyContent;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>

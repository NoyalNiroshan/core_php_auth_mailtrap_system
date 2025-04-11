<?php
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '4d84a101652ed1'; 
        $mail->Password   = '72146cf675d51f';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('jenijeniston05@gmail.com', 'Auth System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = ' Password Reset Request';

        $base_url = "http://localhost/core_php_auth_mailtrap_system";

        $mail->Body = "
        <div style='max-width:600px;margin:auto;background:#ffffff;border-radius:10px;padding:30px;font-family:Ubuntu,sans-serif;color:#333;box-shadow:0 0 10px rgba(0,0,0,0.1);'>
            <h2 style='text-align:center;color:#00bb88;'> Reset Your Password</h2>
            <p style='font-size:16px;'>Hello,</p>
            <p style='font-size:15px;line-height:1.6;'>
                We received a request to reset your password. If you made this request, click the button below to reset it.
                This link will expire in <strong>1 hour</strong>.
            </p>
            <div style='text-align:center;margin:30px 0;'>
                <a href='$base_url/reset_password.php?token=$token' 
                   style='background:#00bb88;color:white;padding:14px 28px;border-radius:6px;
                          text-decoration:none;font-weight:bold;font-size:16px;display:inline-block;'>
                    Reset Password
                </a>
            </div>
            <p style='font-size:14px;color:#555;'>
                If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </p>
            <hr style='margin:30px 0;border:none;border-top:1px solid #eee;'>
            <p style='font-size:13px;text-align:center;color:#999;'>
                © " . date("Y") . " Auth System. All rights reserved.
            </p>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optional: Uncomment to debug errors
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>

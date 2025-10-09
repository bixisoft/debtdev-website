<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize inputs
    $name = htmlspecialchars($_POST['name'] ?? '');
    $company = htmlspecialchars($_POST['company'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $leadVolume = htmlspecialchars($_POST['leadVolume'] ?? 'Not specified');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
        exit;
    }

 // Construct HTML email
$html_message = "
<!DOCTYPE html>
<html>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<style>
body {
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
    background: #f4f6f8;
    color: #333;
    margin: 0;
    padding: 0;
}
.email-container {
    max-width: 600px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    overflow: hidden;
}
.header {
    background: linear-gradient(135deg, #0B1B2B, #162A3F);
    text-align: center;
    padding: 35px 20px 25px;
    color: #ffffff;
    position: relative;
}
.header .logo-box {
    display: inline-block;
    background: #ffffff;
    border-radius: 15px; /* Rounded rectangle shape */
    padding: 12px 18px;
    margin-bottom: 12px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.header .logo-box img {
    max-width: 110px;
    height: auto;
    display: block;
}
.header h1 {
    margin: 12px 0 0;
    font-size: 22px;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.content {
    padding: 35px 40px;
    font-size: 16px;
    line-height: 1.7;
}
.content h2 {
    color: #28a99c;
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
}
.field {
    margin-bottom: 14px;
}
.field strong {
    color: #0B1B2B;
}
a {
    color: #28a99c;
    text-decoration: none;
    font-weight: 500;
}
.footer {
    background: #f0faf8;
    padding: 20px;
    text-align: center;
    font-size: 13px;
    color: #0B1B2B;
    border-top: 1px solid #d6efeb;
}
@media(max-width:600px) {
    .email-container { margin: 20px 10px; }
    .content { padding: 25px 20px; font-size: 15px; }
    .header h1 { font-size: 20px; }
}
</style>
</head>
<body>
<div class='email-container'>
    <div class='header'>
        <div class='logo-box'>
            <img src='https://debtdev.com/assets/img/logo.png' alt='DebtDev Logo'/>
        </div>
        <h1>Contact Information</h1>
    </div>
    <div class='content'>
        <h2>Dear DebtDev Team,</h2>
        <p>A new contact form has been submitted. Below are the details:</p>
        <div class='field'><strong>Name:</strong> {$name}</div>
        <div class='field'><strong>Company:</strong> {$company}</div>
        <div class='field'><strong>Email:</strong> <a href='mailto:{$email}'>{$email}</a></div>
        <div class='field'><strong>Phone:</strong> <a href='tel:{$phone}'>{$phone}</a></div>
        <div class='field'><strong>Monthly Lead Volume:</strong> {$leadVolume}</div>
        <div class='field'><strong>Message:</strong><br>" . nl2br($message) . "</div>
    </div>
    <div class='footer'>
        © " . date('Y') . " DebtDev. All rights reserved.
    </div>
</div>
</body>
</html>
";
//  Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '4b2667d2975e44';
        $mail->Password = '10f307526fbaf4';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('noreply@debtdev.com', 'DebtDev Website');
        $mail->addAddress('rmak78@gmail.com', 'DebtDev Team');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission - {$name}";
        $mail->Body = $html_message;
        $mail->AltBody = strip_tags($html_message);

        if ($mail->send()) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Email sent successfully"]);
        } else {
            throw new Exception($mail->ErrorInfo);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Mailer Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
